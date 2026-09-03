"""Gemeinsame Helfer fuer die Analyse-Skripte (analyse_code / analyse_design).

Bewusst schlank gehalten: Profil einlesen, Pfadaufloesung, JSON-Ausgabe.

Projektspezifische Werte stehen ausschliesslich im Projektprofil `PROJEKT.md` im Wurzel-
verzeichnis des Projekts (erster ```toml-Block). Dieses Verzeichnis (`harness/`) enthaelt
keine Projektnamen und keine Projektpfade.
"""
from __future__ import annotations

import json
import re
import sys
from functools import lru_cache
from pathlib import Path

try:
    import tomllib  # Python >= 3.11
except ModuleNotFoundError:  # Python < 3.11
    try:
        import tomli as tomllib  # type: ignore[import-not-found,no-redef]
    except ModuleNotFoundError:
        tomllib = None  # type: ignore[assignment]

# harness/ liegt direkt unter dem Repo-Root.
HARNESS = Path(__file__).resolve().parent
REPO = HARNESS.parent

PROFILE_FILE = REPO / "PROJEKT.md"
PROFILE_TEMPLATE = HARNESS / "PROJEKT.template.md"

_UNSET = "AUSFÜLLEN"
_TOML_BLOCK = re.compile(r"^```toml\s*$(.*?)^```\s*$", re.M | re.S)


class ProfileError(RuntimeError):
    """Das Projektprofil fehlt, ist unlesbar oder ein benoetigter Wert ist offen."""


@lru_cache(maxsize=1)
def profile() -> dict:
    """Projektprofil aus PROJEKT.md lesen (erster ```toml-Block)."""
    if not PROFILE_FILE.exists():
        raise ProfileError(
            f"Projektprofil nicht gefunden: {PROFILE_FILE}\n"
            f"Vorlage kopieren: {PROFILE_TEMPLATE} -> {PROFILE_FILE}"
        )
    if tomllib is None:
        raise ProfileError(
            "Weder 'tomllib' (Python >= 3.11) noch 'tomli' verfuegbar. "
            "Entweder eine neuere Python-Version verwenden oder 'pip3 install --user tomli'."
        )
    match = _TOML_BLOCK.search(PROFILE_FILE.read_text(encoding="utf-8"))
    if match is None:
        raise ProfileError(f"Kein ```toml-Block in {PROFILE_FILE} gefunden.")
    try:
        return tomllib.loads(match.group(1))
    except tomllib.TOMLDecodeError as exc:  # type: ignore[union-attr]
        raise ProfileError(f"Profil in {PROFILE_FILE} ist kein gueltiges TOML: {exc}") from exc


def value(*keys: str, default: object | None = None) -> object:
    """Wert aus dem Profil holen, z. B. value("pfade", "backend").

    Bricht ab, wenn der Wert fehlt oder noch auf AUSFUELLEN steht und es keine
    Vorgabe gibt — ein halb ausgefuelltes Profil darf nicht stillschweigend
    zu einer Pruefung fuehren, die nichts prueft.
    """
    node: object = profile()
    for key in keys:
        if not isinstance(node, dict) or key not in node:
            node = None
            break
        node = node[key]
    if node is None or node == _UNSET:
        if default is not None:
            return default
        raise ProfileError(
            f"Profilwert [{'] ['.join(keys)}] ist in {PROFILE_FILE} noch offen. "
            "Bitte ausfuellen — ohne diesen Wert kann nicht geprueft werden."
        )
    return node


def repo_path(*keys: str, default: str | None = None) -> Path:
    """Profilwert als absoluten Pfad unter dem Repo-Root."""
    return REPO / str(value(*keys, default=default))


def composer_root() -> Path:
    """Wurzel des Composer-Projekts mit vendor/ ([php] wurzel).

    PHPStan, Pint und Deptrac laufen aus dieser Wurzel heraus — dort liegen die
    Werkzeuge unter vendor/bin/ und die autoload-Informationen des Projekts.
    """
    return repo_path("php", "wurzel")


def modul_root() -> Path:
    """Wurzel der Fachmodule ([php] modulwurzel, Vorgabe "modules"; _design.md §2)."""
    return repo_path("php", "modulwurzel", default="modules")


def check_root() -> Path:
    """Suchwurzel der Analyse ([pfade] pruefwurzel).

    Getrennt von backend_root()-aehnlichen Werten, weil beides auseinanderfaellt,
    sobald Plattform- oder Oberflaechencode neben den Fachmodulen liegt. Wurde
    derselbe Pfad fuer beides benutzt, blieb Code ausserhalb stillschweigend
    ungeprueft und der Lauf meldete trotzdem Erfolg. Ohne Eintrag gilt der
    Backend-Pfad — dann verhaelt sich der Lauf wie bisher, statt mit einem
    fehlenden Profilwert abzubrechen.
    """
    configured = value("pfade", "pruefwurzel", default="")
    return REPO / str(configured) if configured else repo_path("pfade", "backend")


def phpstan_config() -> Path:
    """PHPStan-Regelvorlage ([php] phpstanConfig, Vorgabe harness/phpstan.neon)."""
    return REPO / str(value("php", "phpstanConfig", default="harness/phpstan.neon"))


def pint_config() -> Path:
    """Pint-Regelvorlage ([php] pintConfig, Vorgabe harness/pint.json)."""
    return REPO / str(value("php", "pintConfig", default="harness/pint.json"))


def deptrac_config() -> Path:
    """Deptrac-Regelvorlage ([php] deptracConfig, Vorgabe harness/deptrac.yaml).

    Die Vorgabe gilt nur, wenn die Kopie des Harness unveraendert am Harness-Ort
    liegen bleibt; im Projekt wird sie befuellt und ihr Pfad eingetragen
    (_design.md §1).
    """
    return REPO / str(value("php", "deptracConfig", default="harness/deptrac.yaml"))


def analysis_dir() -> Path:
    """Zielverzeichnis der Analyse-Ergebnisse ([pfade] analyse, Vorgabe "analysis").

    Faellt ohne lesbares Projektprofil auf "analysis" zurueck, damit analyse_harness.py
    auch im Harness-Repository selbst laeuft, wo es kein PROJEKT.md gibt.
    """
    try:
        return REPO / str(value("pfade", "analyse", default="analysis"))
    except ProfileError:
        return REPO / "analysis"


def fail_on_profile_error(exc: ProfileError) -> None:
    """Profilfehler als verstaendliche Meldung ausgeben und abbrechen."""
    sys.exit(f"Fehler im Projektprofil: {exc}")


def to_repo_path(path: Path) -> str:
    """Absoluten Pfad repo-relativ und mit Forward-Slashes zurueckgeben."""
    path = Path(path).resolve()
    try:
        return path.relative_to(REPO).as_posix()
    except ValueError:
        return path.as_posix()


def write_json(output: Path, payload: dict) -> None:
    output = Path(output)
    output.parent.mkdir(parents=True, exist_ok=True)
    with output.open("w", encoding="utf-8") as fh:
        json.dump(payload, fh, ensure_ascii=False, indent=2)
        fh.write("\n")