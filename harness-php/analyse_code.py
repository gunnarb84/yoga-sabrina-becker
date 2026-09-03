#!/usr/bin/env python3
"""Fuehrt PHPStan und Pint gegen das Projekt aus und fasst die Ergebnisse in ein
minimales JSON der Verstoesse gegen die Coding-Guideline (harness/_code.md).

Beide Werkzeuge laufen aus der Composer-Wurzel ([php] wurzel) heraus — dort liegen
sie unter vendor/bin/; die Regelvorlagen sind die aus dem Harness uebernommenen
Dateien ([php] phpstanConfig / [php] pintConfig, Vorgabe harness/phpstan.neon und
harness/pint.json). Geprueft wird die Suchwurzel ([pfade] pruefwurzel, Vorgabe
[pfade] backend) — erzeugter Code (vendor/, bootstrap/cache, kompilierte Views)
schiessen die Ausschluesse der Regelvorlage aus (_code.md §13), nicht der Lauf.

Ausgabe je Verstoss nur, was zum Finden/Fixen der Stelle noetig ist:
  tool, rule, file (repo-relativ), line, message.

Exit-Codes: PHPStan und Pint melden Befunde mit Exit 1 — das ist hier ein
Ergebnis, kein Fehler. Ein Exit >= 2 bricht mit der Werkzeugausgabe ab.

Aufruf:
  python harness/analyse_code.py [--output <json>] [--keep-generated]
                                 [<pfad> ...]
"""
from __future__ import annotations

import argparse
import json
import subprocess
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from _analyse_common import (  # noqa: E402
    REPO,
    ProfileError,
    analysis_dir,
    check_root,
    composer_root,
    fail_on_profile_error,
    phpstan_config,
    pint_config,
    to_repo_path,
    write_json,
)


def require_binary(root: Path, relative: str, werkzeug: str) -> Path:
    """Werkzeug unter vendor/bin/ des Composer-Projekts pruefen, bevor gelaufen wird."""
    binary = root / relative
    if not binary.exists():
        sys.exit(
            f"Fehler: {werkzeug} nicht gefunden: {to_repo_path(binary)} — "
            f"Abhaengigkeiten unter [php] wurzel ({to_repo_path(root)}) installieren."
        )
    return binary


def run(cmd: list[str], root: Path, werkzeug: str) -> str:
    """Werkzeuglauf mit klarer Fehlermeldung; stdout wird als JSON erwartet."""
    proc = subprocess.run(cmd, cwd=str(root), capture_output=True, text=True)
    # Exit 1 ist bei beiden Werkzeugen "Befunde gefunden", kein Fehlschlag.
    if proc.returncode not in (0, 1):
        sys.exit(
            f"Fehler: {werkzeug} schlug fehl (Exit {proc.returncode}):\n"
            f"{proc.stderr.strip() or proc.stdout.strip()}"
        )
    return proc.stdout or ""


def run_phpstan(root: Path, config: Path, paths: list[Path]) -> list[dict]:
    """PHPStan-Analyse; Befunde je Datei in ein einheitliches Format bringen."""
    binary = require_binary(root, "vendor/bin/phpstan", "PHPStan")
    cmd = [
        str(binary), "analyse",
        "--error-format=json",
        "--no-progress",
        "--no-interaction",
        f"--configuration={config}",
        "--memory-limit=1G",
        *(str(p) for p in paths),
    ]
    stdout = run(cmd, root, "phpstan")
    try:
        data = json.loads(stdout)
    except json.JSONDecodeError:
        sys.exit("Fehler: PHPStan lieferte kein gueltiges JSON (Konfiguration pruefen).")

    violations: list[dict] = []
    for file, info in data.get("files", {}).items():
        for message in info.get("messages", []):
            violations.append({
                "tool": "phpstan",
                "rule": message.get("identifier") or "phpstan",
                "file": to_repo_path(Path(file)),
                "line": message.get("line"),
                "message": message.get("message"),
            })
    return violations


def run_pint(root: Path, config: Path, paths: list[Path]) -> list[dict]:
    """Pint im Testlauf (--test, keine Aenderung); Verstoesse je Datei auslesen."""
    binary = require_binary(root, "vendor/bin/pint", "Pint")
    cmd = [
        str(binary), "--test",
        "--format=json",
        f"--config={config}",
        *(str(p) for p in paths),
    ]
    stdout = run(cmd, root, "pint")
    try:
        data = json.loads(stdout)
    except json.JSONDecodeError:
        sys.exit("Fehler: Pint lieferte kein gueltiges JSON (Konfiguration pruefen).")

    violations: list[dict] = []
    for file, info in data.get("files", {}).items():
        for error in info.get("errors", []):
            violations.append({
                "tool": "pint",
                "rule": error.get("source") or "pint",
                "file": to_repo_path(Path(file)),
                "line": error.get("line"),
                "message": error.get("message"),
            })
    return violations


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="PHPStan/Pint-Analyse -> JSON (harness/_code.md).")
    parser.add_argument("--output", type=Path, default=analysis_dir() / "code.json", help="Ziel-JSON.")
    parser.add_argument("--phpstan-config", type=Path, default=None,
                        help="PHPStan-Regelvorlage (Vorgabe aus PROJEKT.md).")
    parser.add_argument("--pint-config", type=Path, default=None,
                        help="Pint-Regelvorlage (Vorgabe aus PROJEKT.md).")
    parser.add_argument("paths", nargs="*",
                        help="Zu pruefende Pfade (Vorgabe: [pfade] pruefwurzel aus PROJEKT.md).")
    args = parser.parse_args(argv)

    try:
        root = composer_root()
        phpstan_cfg = args.phpstan_config or phpstan_config()
        pint_cfg = args.pint_config or pint_config()
        paths = [REPO / p for p in args.paths] or [check_root()]
    except ProfileError as exc:
        fail_on_profile_error(exc)

    for cfg in (phpstan_cfg, pint_cfg):
        if not cfg.exists():
            sys.exit(f"Fehler: Regelvorlage nicht gefunden: {to_repo_path(cfg)} (_code.md §12).")

    violations = run_phpstan(root, phpstan_cfg, paths) + run_pint(root, pint_cfg, paths)
    violations.sort(key=lambda v: (v["file"] or "~", v["line"] or 0, v["tool"], v["rule"] or ""))

    phpstan_count = sum(1 for v in violations if v["tool"] == "phpstan")
    pint_count = len(violations) - phpstan_count
    payload = {
        "tool": "phpstan+pint",
        "guideline": "harness/_code.md",
        "phpstanConfig": to_repo_path(phpstan_cfg),
        "pintConfig": to_repo_path(pint_cfg),
        "targets": [to_repo_path(p) for p in paths],
        "violationCount": len(violations),
        "phpstan": {"violationCount": phpstan_count},
        "pint": {"violationCount": pint_count},
        "violations": violations,
    }
    write_json(args.output, payload)
    print(
        f"[analyse_code] {len(violations)} Verstoss/Verstoesse "
        f"(PHPStan {phpstan_count}, Pint {pint_count}) -> {to_repo_path(args.output)}",
        file=sys.stderr,
    )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())