"""Prueft, ob der Harness projektneutral geblieben ist.

Der Harness ist ein eigenes Repository und wird in mehrere Projekte eingebunden. Er darf
deshalb keine Projektnamen, Projektpfade, Herstellernamen oder Domaenenbegriffe enthalten —
diese gehoeren in `PROJEKT.md` und `projekt/_domaene.md` des jeweiligen Projekts.

Aufruf:
    python harness/analyse_harness.py            # Bericht auf der Konsole
    python harness/analyse_harness.py --json     # zusaetzlich harness.json im Analyse-Verzeichnis

Rueckgabe 1, wenn Verstoesse gefunden wurden — damit der Lauf eine Pruefung blockieren kann.

Neue verbotene Begriffe werden in PATTERNS ergaenzt. Wer einen Begriff aufnimmt, nennt in der
Begruendung, wohin er stattdessen gehoert.
"""
from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from _analyse_common import HARNESS, analysis_dir, to_repo_path, write_json  # noqa: E402

# (Kennung, Regex, Begruendung/Wohin-stattdessen)
PATTERNS: list[tuple[str, str, str]] = [
    ("projektname", r"persona[- ]?management|personmanager|aurum",
     "Projektname — gehoert in PROJEKT.md [projekt] name/kurzname."),
    ("herstellername", r"\bthome\b",
     "Herstellername — gehoert in PROJEKT.md."),
    ("domaenenbegriff", r"schmuck|werttransport|feingehalt|punzier|valoren|metallkonto|edelmetall"
                        r"|fahrpersonal|\btouren?\b|routendaten|wertdaten|tourdaten",
     "Fachbegriff eines Produkts — gehoert in projekt/_domaene.md."),
    ("herstellerwerkzeug", r"\bn8n\b|\bdatev\b",
     "Produktname eines externen Werkzeugs — Rolle verwenden (Automatisierungswerkzeug, "
     "Buchhaltungsuebergabe), konkretes Werkzeug in PROJEKT.md."),
    # Bewusst schon beim nackten "code/": das Muster verlangte fruehe einen Unterordner
    # ("code/src/") und liess damit genau die Schreibweise durch, die in einem Leitfaden
    # naheliegt — ein Verzeichnisname, den das Profil frei festlegt.
    # (?-i:...) hebt die Gross-/Kleinschreibungstoleranz nur hier auf — sonst trifft das
    # Muster die Aufzaehlung "Code/Design" im Regelindex.
    ("projektpfad", r"(?-i:\bcode/)|\bpublic/css/[a-z0-9-]+\.css",
     "Projektpfad — gehoert in PROJEKT.md [pfade] bzw. [ui]."),
    # Altlasten aus Vorgaengerprojekten. Sie sind nie als Projektname aufgefallen, weil sie
    # wie allgemeine Begriffe aussehen — genau deshalb ueberdauern sie jede Neufassung.
    ("altlast", r"\bE\d+ (Adressen|Personen)\b|/api/(addresses|persons)\b"
                r"|\bSortOrder\b|\bDisplayName\b|\bContactEmail\b|\bSeedTestData\b"
                r"|\bSEED_TEST_DATA\b|/api/test/reset\b",
     "Beispielwert eines Vorgaengerprojekts — neutrales Beispiel verwenden oder auf "
     "PROJEKT.md verweisen."),
    # Technik, die der Bauart aus _index.md widerspricht. Ein Treffer heisst fast immer:
    # der Abschnitt stammt aus einem Projekt anderer Bauart und wurde nicht mitgezogen.
    ("bauartfremd", r"\bblazor\b|\brazor\b|\bpostgres(?:ql)?\b|\bsqlite\b|\bmssql\b"
                    r"|\bsql server\b|\bmongodb\b|\bentity framework\b|\bdotnet\b"
                    r"|\bcsharp\b|\.csproj\b|\bndepend\b|\bresharper\b|\buwp\b"
                    r"|\breact\b|\bangular\b|\bvue\b",
     "Technik ausserhalb der Bauart (php-laravel-livewire-mysql, siehe _index.md). "
     "Entweder ist der Abschnitt veraltet oder das Projekt gehoert nicht zu diesem Harness."),
    # Altmuster der 2026-08 entfernten Mehrmandantenfaehigkeit: je Installation genau eine
    # Firma (_data.md §5). Ein Treffer heisst: der Abschnitt ist vor dem Umbau stehen
    # geblieben oder wurde aus einem alten Stand uebernommen.
    ("mehrmandanten", r"\bcompany_?id\b|companycontext|firmenfilter|firmenkontext",
     "Altmuster der entfernten Mehrmandantenfaehigkeit — je Installation genau eine Firma "
     "(_data.md §5)."),
]

# Dateien, in denen ein bestimmtes Muster absichtlich vorkommt — dort steht es als
# Verbotstext, nicht als Regel. Enger als EXEMPT: nur dieses eine Muster ist dort erlaubt.
KIND_EXEMPT: dict[str, set[str]] = {
    "mehrmandanten": {"_data.md", "_security.md"},
}

# Dateien, die absichtlich Beispielwerte enthalten duerfen.
EXEMPT = {"PROJEKT.template.md", "analyse_harness.py"}

# Nur Textdateien pruefen; Binaerartefakte und Zwischenstaende auslassen.
# .claude/ ist die Session-Konfiguration des Pruefwerkzeugs, kein Harness-Inhalt —
# sie duerfte aber z. B. erlaubte Bash-Befehle wortgleich enthalten und wuerde
# sonst jeden Lauf dauerhaft rot machen.
SUFFIXES = {".md", ".py", ".css", ".neon", ".yaml", ".json"}
SKIP_DIRS = {"__pycache__", ".claude"}


def scan() -> list[dict]:
    findings: list[dict] = []
    compiled = [(kind, re.compile(rx, re.I), why) for kind, rx, why in PATTERNS]

    for path in sorted(HARNESS.rglob("*")):
        if not path.is_file() or path.suffix not in SUFFIXES:
            continue
        if path.name in EXEMPT or any(part in SKIP_DIRS for part in path.parts):
            continue
        for number, line in enumerate(path.read_text(encoding="utf-8").splitlines(), start=1):
            for kind, rx, why in compiled:
                if path.name in KIND_EXEMPT.get(kind, ()):
                    continue
                match = rx.search(line)
                if match is None:
                    continue
                findings.append({
                    "kind": kind,
                    "file": to_repo_path(path),
                    "line": number,
                    "match": match.group(0),
                    "reason": why,
                    "context": line.strip()[:160],
                })
    return findings


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="Prueft den Harness auf Projektliterale.")
    parser.add_argument("--json", action="store_true", help="Ergebnis als harness.json ins Analyse-Verzeichnis schreiben.")
    parser.add_argument("--output", type=Path, default=analysis_dir() / "harness.json", help="Ziel-JSON.")
    args = parser.parse_args(argv)

    findings = scan()

    if args.json:
        write_json(args.output, {
            "tool": "harness-neutralitaet",
            "guideline": "harness/_index.md",
            "violationCount": len(findings),
            "violations": findings,
        })

    if not findings:
        print("[analyse_harness] keine Projektliterale gefunden — Harness ist neutral.", file=sys.stderr)
        return 0

    print(f"[analyse_harness] {len(findings)} Fund(e):", file=sys.stderr)
    for f in findings:
        print(f"  {f['file']}:{f['line']}  {f['kind']}: {f['match']!r}", file=sys.stderr)
        print(f"      {f['reason']}", file=sys.stderr)
    return 1


if __name__ == "__main__":
    raise SystemExit(main())
