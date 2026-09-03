"""Neue Ideen-Datei anlegen und die Nummer zentral reservieren (sofortiger Push).

Zweck: Ideen heissen `I{n} {Name}.md` mit laufender Nummer (_ideas.md, "Ablage"). Arbeiten
mehrere Entwickler parallel, vergeben sie dieselbe Nummer doppelt. Dieses Skript ist deshalb
der einzige zulaessige Weg, eine Ideen-Datei anzulegen: Es stellt sicher, dass der lokale
Stand aktuell ist, vergibt die naechste freie Nummer und pusht die neue Datei sofort —
damit ist die Nummer fuer alle sichtbar reserviert.

Aufruf:
    python harness/idee_neu.py "Name der Idee"

Voraussetzungen, sonst bricht das Skript mit einer Meldung ab:
- Aktueller Zweig ist main oder master (Ideen sind Planungsstand, kein Feature-Code).
- Keine uncommitteten Aenderungen an versionierten Dateien (unversionierte Dateien stoeren
  nicht — sie landen nicht im Commit).
- Keine lokalen Commits, die noch nicht gepusht sind — sonst wuerde der Ideen-Push
  unfertigen Code mitnehmen. Erst pushen (oder den Stand zurueckstellen), dann Idee anlegen.

Ablauf: fetch; liegt das Remote vorn, wird per Fast-Forward aktualisiert; dann naechste
freie Nummer aus dem Ideen-Verzeichnis ([pfade] ideen, Vorgabe "ideas") bestimmen, Datei
aus der Vorlage anlegen (State Draft), committen, pushen. Wird der Push abgewiesen, weil
jemand zeitgleich reserviert hat: Commit zuruecknehmen, Stand holen, naechste Nummer
vergeben und erneut versuchen (3 Versuche).
"""
from __future__ import annotations

import argparse
import re
import subprocess
import sys
from datetime import date
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from _analyse_common import REPO, ProfileError, value  # noqa: E402

VERSUCHE = 3
NUMMER = re.compile(r"^I(\d+)\b")
VERBOTEN = re.compile(r'[\\/:*?"<>|\x00-\x1f]')

VORLAGE = """# {name}

## Meta
- **State:** Draft

## Problem
…

## Lösungsidee
…

## Scope
- **In Scope:** …
- **Out of Scope:** …

## Auswirkungen auf den Bestand
- **Specs:** …
- **Datenstruktur:** …
- **Backend:** …
- **Quellcode:** …

## Entscheidungen
- {datum}: Idee angelegt, Nummer I{nummer} reserviert.

## Offene Punkte
- Dialog noch nicht begonnen.
"""


def git(*args: str, fehler_ok: bool = False) -> subprocess.CompletedProcess:
    lauf = subprocess.run(["git", *args], cwd=REPO, capture_output=True)
    if lauf.returncode != 0 and not fehler_ok:
        meldung = lauf.stderr.decode("utf-8", errors="replace").strip()
        raise RuntimeError(f"git {' '.join(args)} fehlgeschlagen: {meldung}")
    return lauf


def git_text(*args: str) -> str:
    return git(*args).stdout.decode("utf-8", errors="replace").strip()


def ideen_verzeichnis() -> Path:
    try:
        return REPO / str(value("pfade", "ideen", default="ideas"))
    except ProfileError:
        return REPO / "ideas"


def naechste_nummer(verzeichnis: Path) -> int:
    hoechste = 0
    if verzeichnis.is_dir():
        for datei in verzeichnis.iterdir():
            treffer = NUMMER.match(datei.name)
            if treffer:
                hoechste = max(hoechste, int(treffer.group(1)))
    return hoechste + 1


def voraussetzungen() -> tuple[str, str]:
    """Zweig und Remote pruefen; Rueckgabe (zweig, remote). Bricht mit Meldung ab."""
    zweig = git_text("symbolic-ref", "--short", "HEAD")
    if zweig not in {"main", "master"}:
        sys.exit(f"[idee-neu] Abbruch: aktueller Zweig ist {zweig!r} — Ideen werden auf "
                 "main/master angelegt, damit die Nummer fuer alle gilt.")
    schmutzig = git_text("status", "--porcelain", "--untracked-files=no")
    if schmutzig:
        sys.exit("[idee-neu] Abbruch: es gibt uncommittete Aenderungen an versionierten "
                 "Dateien. Erst committen oder zurueckstellen (git stash), dann Idee "
                 "anlegen:\n" + schmutzig)
    lauf = git("rev-parse", "--abbrev-ref", f"{zweig}@{{upstream}}", fehler_ok=True)
    if lauf.returncode != 0:
        sys.exit(f"[idee-neu] Abbruch: der Zweig {zweig!r} hat kein Upstream — einmal "
                 f"'git push -u origin {zweig}' ausfuehren, dann erneut versuchen.")
    upstream = lauf.stdout.decode().strip()
    return zweig, upstream.split("/", 1)[0]


def aktualisieren(zweig: str, remote: str) -> None:
    """Fetch; liegt das Remote vorn: Fast-Forward. Lokale Extra-Commits sind ein Abbruch."""
    git("fetch", remote, zweig)
    upstream = f"{remote}/{zweig}"
    voraus = int(git_text("rev-list", "--count", f"{upstream}..HEAD"))
    if voraus:
        sys.exit(f"[idee-neu] Abbruch: {voraus} lokale(r) Commit(s) noch nicht gepusht — "
                 "der Ideen-Push wuerde sie mitnehmen. Erst den Stand pushen (oder per "
                 "Branch zurueckstellen), dann Idee anlegen.")
    zurueck = int(git_text("rev-list", "--count", f"HEAD..{upstream}"))
    if zurueck:
        git("merge", "--ff-only", upstream)
        print(f"[idee-neu] Stand aktualisiert ({zurueck} Commit(s) vom Remote uebernommen).")


def main(argv: list[str] | None = None) -> int:
    for strom in (sys.stdout, sys.stderr):
        if hasattr(strom, "reconfigure"):
            strom.reconfigure(encoding="utf-8", errors="replace")
    parser = argparse.ArgumentParser(
        description="Neue Ideen-Datei anlegen; Nummer wird per sofortigem Push reserviert.")
    parser.add_argument("name", help="Name der Idee (wird Teil des Dateinamens).")
    args = parser.parse_args(argv)

    name = VERBOTEN.sub(" ", args.name).strip().rstrip(".")
    name = re.sub(r"\s+", " ", name)[:80].strip()
    if not name:
        sys.exit("[idee-neu] Abbruch: der Ideen-Name ist leer oder besteht nur aus "
                 "unzulaessigen Zeichen.")
    if NUMMER.match(name):
        sys.exit("[idee-neu] Abbruch: der Name beginnt mit einer Ideen-Nummer — nur den "
                 "Namen angeben, die Nummer vergibt das Skript.")

    try:
        zweig, remote = voraussetzungen()
        aktualisieren(zweig, remote)
        verzeichnis = ideen_verzeichnis()
        verzeichnis.mkdir(parents=True, exist_ok=True)

        datei: Path | None = None
        for versuch in range(1, VERSUCHE + 1):
            nummer = naechste_nummer(verzeichnis)
            datei = verzeichnis / f"I{nummer} {name}.md"
            datei.write_text(VORLAGE.format(name=name, nummer=nummer,
                                            datum=f"{date.today():%d.%m.%Y}"),
                             encoding="utf-8")
            relpfad = datei.relative_to(REPO).as_posix()
            git("add", "--", relpfad)
            git("commit", "-m", f"Idee I{nummer} angelegt: {name} (Draft, Nummer reserviert)")
            if git("push", remote, zweig, fehler_ok=True).returncode == 0:
                print(f"[idee-neu] {relpfad} angelegt und gepusht — Nummer I{nummer} ist "
                      "reserviert.")
                return 0
            # Jemand war schneller: eigenen Commit zuruecknehmen, Stand holen, neue Nummer.
            git("reset", "--mixed", "HEAD~1")
            datei.unlink()
            git("fetch", remote, zweig)
            git("merge", "--ff-only", f"{remote}/{zweig}")
            print(f"[idee-neu] Push abgewiesen (Versuch {versuch}/{VERSUCHE}) — jemand hat "
                  "zeitgleich reserviert; vergebe die naechste Nummer.", file=sys.stderr)
        sys.exit(f"[idee-neu] Abbruch: Push nach {VERSUCHE} Versuchen nicht gelungen — "
                 "Rechte und Zweigschutz pruefen. Es wurde nichts angelegt.")
    except RuntimeError as exc:
        sys.exit(f"[idee-neu] Abbruch: {exc}")


if __name__ == "__main__":
    raise SystemExit(main())
