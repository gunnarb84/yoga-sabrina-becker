#!/usr/bin/env python3
"""Fuehrt Deptrac mit der befuellten Deptrac-Regeldatei ([php] deptracConfig,
Vorgabe harness/deptrac.yaml) gegen das Projekt aus und parst die Ergebnisse in
ein minimales JSON der Verstoesse gegen den Design-Contract (harness/_design.md).

Deptrac laeuft aus der Composer-Wurzel ([php] wurzel) heraus — dort liegt er unter
vendor/bin/. Welche Pfade geprueft werden, entscheidet allein die Regelvorlage;
im Projekt ist sie die befuellte Kopie der Harness-Vorlage (Platzhalter fuer
Namensraum-Praefix und Modulnamen, _design.md §1).

Als Gegenprobe nennt der Lauf am Ende jedes Modulverzeichnis der Modulwurzel
([php] modulwurzel), das im Ergebnis nicht vorkam — eine Luecke zwischen
Regelvorlage und Modulwurzel wuerde sonst stillschweigend ungeprueft bleiben.

Ausgabe je Verstoss nur, was zum Finden/Fixen der Stelle noetig ist:
  rule, depender (Klasse), dependerLayer, dependent (Klasse), dependentLayer.

Aufruf:
  python harness/analyse_design.py [--output <json>] [--config <deptrac.yaml>]
"""
from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from _analyse_common import (  # noqa: E402
    ProfileError,
    analysis_dir,
    composer_root,
    deptrac_config,
    fail_on_profile_error,
    modul_root,
    to_repo_path,
    write_json,
)

# Modulname aus einem Namensraum: "...Modules\<Modul>\..." — der Praefix davor ist
# projektspezifisch ([projekt] kurzname) und bleibt deshalb unerwuenscht/unbeachtet.
_MODULE = re.compile(r"(?:^|\\)Modules\\([A-Za-z0-9_]+)\\")

# Befundtext (Deptrac >= 4.7): "A must not depend on B (SchichtA on SchichtB)".
_VIOLATION = re.compile(r"^(.+?) must not depend on (.+?) \((.+?) on (.+?)\)$")

# Hinweistext einer ungedeckten Abhaengigkeit (Deptrac >= 4.7, --report-uncovered):
# "A has uncovered dependency on B (Schicht)" — Grundlage der Abdeckungsprobe.
_UNCOVERED = re.compile(r"^(.+?) has uncovered dependency on (.+?)(?: \((.+?)\))?$")


def require_binary(root: Path) -> Path:
    """Deptrac unter vendor/bin/ des Composer-Projekts pruefen, bevor gelaufen wird."""
    binary = root / "vendor/bin/deptrac"
    if not binary.exists():
        sys.exit(
            f"Fehler: Deptrac nicht gefunden: {to_repo_path(binary)} — "
            f"Abhaengigkeiten unter [php] wurzel ({to_repo_path(root)}) installieren."
        )
    return binary


def run_deptrac(root: Path, config: Path) -> dict:
    """Deptrac-Analyse als JSON. Exit 1 heisst hier "Befunde", kein Fehlschlag."""
    binary = require_binary(root)
    cmd = [
        str(binary), "analyse",
        "--formatter=json",
        "--no-progress",
        "--no-interaction",
        "--report-uncovered",
        f"--config-file={config}",
    ]
    proc = subprocess.run(cmd, cwd=str(root), capture_output=True, text=True)
    if proc.returncode not in (0, 1):
        sys.exit(
            f"Fehler: deptrac schlug fehl (Exit {proc.returncode}):\n"
            f"{proc.stderr.strip() or proc.stdout.strip()}"
        )
    try:
        # Leere Ausgabe ist kein gueltiges Ergebnis — ein gebrochener Lauf
        # (z. B. falsche Option) wuerde sonst stillschweigend als gruen gelten.
        return json.loads(proc.stdout)
    except json.JSONDecodeError:
        sys.exit(
            "Fehler: deptrac lieferte kein gueltiges JSON (Konfiguration pruefen):\n"
            f"{(proc.stderr.strip() or proc.stdout.strip())[:500]}"
        )


def _classes(node: object, seen: set[str]) -> None:
    """Alle Klassenbezeichner (Strings mit Namensraum) einsammeln — Grundlage der
    Modulabdeckung, ohne sich auf ein konkretes Ausgabe-Schema festzulegen."""
    if isinstance(node, dict):
        for value in node.values():
            _classes(value, seen)
    elif isinstance(node, list):
        for value in node:
            _classes(value, seen)
    elif isinstance(node, str) and "\\" in node and " " not in node:
        seen.add(node)


def parse(data: dict) -> list[dict]:
    """Befunde in ein einheitliches Format bringen.

    Deptrac >= 4.7 liefert je Datei eine "messages"-Liste mit lesbarem Befundtext
    (Fehler: "A must not depend on B (SchichtA on SchichtB)"). Aeltere Deptrac-
    Versionen verschachteln "violations"-Objekte je Schicht — dieses Format wird
    daneben weiter akzeptiert (siehe _parse_nested).
    """
    violations: list[dict] = []
    files = data.get("files")
    if isinstance(files, dict):
        for file, info in files.items():
            for message in info.get("messages") or []:
                if message.get("type") != "error":
                    continue
                match = _VIOLATION.match(message.get("message") or "")
                if match is None:
                    continue
                depender, dependent, depender_layer, dependent_layer = match.groups()
                violations.append({
                    "rule": "deptrac.violation",
                    "depender": depender,
                    "dependerLayer": depender_layer,
                    "dependent": dependent,
                    "dependentLayer": dependent_layer,
                    "file": file,
                    "line": message.get("line"),
                })

    seen = {
        (v["depender"], v["dependent"], v["dependerLayer"], v["file"], v["line"])
        for v in violations
    }
    for v in _parse_nested(data):
        key = (v["depender"], v["dependent"], v["dependerLayer"], v["file"], v["line"])
        if key not in seen:
            seen.add(key)
            violations.append(v)

    violations.sort(key=lambda x: (x["file"] or "~", x["line"] or 0, x["depender"] or ""))
    return violations


def _parse_nested(data: dict) -> list[dict]:
    """Alles mit "violations"-Liste finden (Schema aelterer Deptrac-Versionen)."""
    violations: list[dict] = []

    def visit(node: object, layer_name: str | None = None) -> None:
        if isinstance(node, dict):
            name = node.get("name") or node.get("layer") or layer_name
            entries = node.get("violations")
            if isinstance(entries, list):
                for v in entries:
                    if isinstance(v, dict):
                        violations.append({
                            "rule": v.get("rule") or "deptrac.violation",
                            "depender": v.get("depender") or v.get("classA"),
                            "dependerLayer": v.get("dependerLayer") or name,
                            "dependent": v.get("dependent") or v.get("classB"),
                            "dependentLayer": v.get("dependentLayer"),
                            "file": v.get("dependerFile"),
                            "line": v.get("dependerLine"),
                        })
                    elif isinstance(v, str):
                        violations.append({
                            "rule": "deptrac.violation",
                            "depender": v,
                            "dependerLayer": name,
                            "dependent": None,
                            "dependentLayer": None,
                            "file": None,
                            "line": None,
                        })
            for value in node.values():
                visit(value, name)
        elif isinstance(node, list):
            for value in node:
                visit(value, layer_name)

    visit(data)
    return violations


def found_classes(data: dict) -> set[str]:
    """Alle im Ergebnis genannten Klassenbezeichner einsammeln — Grundlage der
    Abdeckungsprobe. Alte Schemas nennen freie Klassenstrings; Deptrac >= 4.7
    verbirgt sie im Befund- und Hinweistext der je-Datei-Meldungen."""
    classes: set[str] = set()
    _classes(data, classes)
    files = data.get("files")
    if isinstance(files, dict):
        for info in files.values():
            for message in info.get("messages") or []:
                text = message.get("message") or ""
                match = _VIOLATION.match(text)
                if match is not None:
                    classes.update((match.group(1), match.group(2)))
                    continue
                match = _UNCOVERED.match(text)
                if match is not None:
                    classes.update((match.group(1), match.group(2)))
    return classes


def coverage(classes: set[str]) -> dict:
    """Welche Module der Modulwurzel kamen im Ergebnis vor — und welche nicht.

    Der Pruefumfang ergibt sich aus der Regelvorlage; liegt ein Modul ausserhalb,
    faellt es lautlos heraus und der Lauf meldet trotzdem Erfolg. Diese Gegenprobe
    macht die Luecke sichtbar, statt sie als gruenes Ergebnis auszugeben.
    """
    found = {m.lower() for name in classes for m in _MODULE.findall(name)}
    try:
        root = modul_root()
    except ProfileError as exc:
        fail_on_profile_error(exc)
    if not root.is_dir():
        sys.exit(
            f"Fehler: Modulwurzel nicht gefunden: {to_repo_path(root)} — "
            "[php] modulwurzel in PROJEKT.md pruefen."
        )
    dirs = {p.name.lower() for p in root.iterdir() if p.is_dir()}
    return {
        "modulwurzel": to_repo_path(root),
        "checked": sorted(name for name in dirs if name in found),
        "notChecked": sorted(dirs - found),
    }


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="Deptrac-Design-Analyse -> JSON (harness/_design.md).")
    parser.add_argument("--output", type=Path, default=analysis_dir() / "design.json", help="Ziel-JSON.")
    parser.add_argument("--config", type=Path, default=None,
                        help="Deptrac-Regeldatei (Vorgabe aus PROJEKT.md).")
    args = parser.parse_args(argv)

    try:
        root = composer_root()
        config = args.config or deptrac_config()
    except ProfileError as exc:
        fail_on_profile_error(exc)
    if not config.exists():
        sys.exit(
            f"Fehler: Deptrac-Regeldatei nicht gefunden: {to_repo_path(config)} — "
            "befuellte Kopie der Harness-Vorlage anlegen und in PROJEKT.md [php] "
            "deptracConfig eintragen (_design.md §1)."
        )

    data = run_deptrac(root, config)
    abdeckung = coverage(found_classes(data))
    violations = parse(data)

    payload = {
        "tool": "deptrac",
        "guideline": "harness/_design.md",
        "config": to_repo_path(config),
        "coverage": abdeckung,
        "violationCount": len(violations),
        "violations": violations,
    }
    write_json(args.output, payload)
    print(
        f"[analyse_design] {len(abdeckung['checked'])} Modul(e) geprueft, "
        f"{len(abdeckung['notChecked'])} ohne Zuordnung -> {to_repo_path(args.output)}",
        file=sys.stderr,
    )
    for name in abdeckung["notChecked"]:
        print(f"  ungeprueft: {name} — liegt es unter [php] modulwurzel und in der Regelvorlage?",
              file=sys.stderr)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())