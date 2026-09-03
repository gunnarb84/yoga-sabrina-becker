#!/usr/bin/env python3
"""Ruft analyse_code.py (PHPStan + Pint / harness/_code.md) und analyse_design.py
(Deptrac / harness/_design.md) auf und fasst beide JSON-Ergebnisse in einem
kombinierten, minimalen JSON zusammen.

Aufruf:
  python harness/analyse_all.py [--output-dir <dir>] [--fail-on-violations]
"""
from __future__ import annotations

import argparse
import json
import subprocess
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from _analyse_common import REPO, analysis_dir, to_repo_path, write_json  # noqa: E402

HERE = Path(__file__).resolve().parent


def run_child(script: str, output: Path) -> dict:
    cmd = [sys.executable, str(HERE / script), "--output", str(output)]
    result = subprocess.run(cmd, cwd=str(REPO))
    if result.returncode != 0 or not output.exists():
        sys.exit(f"Fehler: {script} schlug fehl (Exit {result.returncode}).")
    with output.open(encoding="utf-8") as fh:
        return json.load(fh)


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="Fuehrt Code- und Design-Analyse aus und fasst sie zusammen.")
    parser.add_argument("--output-dir", type=Path, default=analysis_dir(), help="Zielordner fuer die JSON-Dateien ([pfade] analyse).")
    parser.add_argument("--fail-on-violations", action="store_true", help="Exit-Code 1, wenn Verstoesse gefunden werden.")
    args = parser.parse_args(argv)

    out_dir = args.output_dir
    code = run_child("analyse_code.py", out_dir / "code.json")
    design = run_child("analyse_design.py", out_dir / "design.json")

    total = code["violationCount"] + design["violationCount"]
    combined = {
        "violationCount": total,
        "code": code,
        "design": design,
    }
    combined_path = out_dir / "all.json"
    write_json(combined_path, combined)

    print(
        f"[analyse_all] gesamt {total} Verstoss/Verstoesse "
        f"(PHPStan/Pint {code['violationCount']}, Deptrac {design['violationCount']}) "
        f"-> {to_repo_path(combined_path)}",
        file=sys.stderr,
    )
    return 1 if (args.fail_on_violations and total > 0) else 0


if __name__ == "__main__":
    raise SystemExit(main())