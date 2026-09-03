#!/usr/bin/env python3
"""Generate @property docblocks for Eloquent models from their migrations and casts."""
from __future__ import annotations

import re
from pathlib import Path

REPO = Path(__file__).resolve().parent

MIGRATION_DIRS = [
    REPO / "modules" / "Verwaltung" / "Persistence" / "migrations",
    REPO / "modules" / "Webseite" / "Persistence" / "migrations",
    REPO / "platform" / "NumberSequence" / "Persistence" / "migrations",
]

DOMAIN_DIRS = [
    REPO / "modules" / "Verwaltung" / "Domain",
    REPO / "modules" / "Webseite" / "Domain",
    REPO / "platform" / "NumberSequence" / "Domain",
]

TYPE_MAP = {
    "binary": "string",
    "uuid": "string",
    "string": "string",
    "text": "string",
    "longText": "string",
    "mediumText": "string",
    "boolean": "bool",
    "tinyInteger": "bool",
    "integer": "int",
    "unsignedInteger": "int",
    "bigInteger": "int",
    "unsignedBigInteger": "int",
    "smallInteger": "int",
    "decimal": "string",
    "float": "string",
    "double": "string",
    "timestamp": "\\Carbon\\Carbon",
    "dateTime": "\\Carbon\\Carbon",
    "date": "\\Carbon\\Carbon",
}


def find_migration_files() -> list[Path]:
    files: list[Path] = []
    for d in MIGRATION_DIRS:
        files.extend(d.glob("*.php"))
    return files


def parse_columns(text: str, table: str) -> dict[str, str]:
    """Parse column additions for a table from a migration body."""
    cols: dict[str, str] = {}
    # Match both Schema::create and Schema::table closures for this table.
    for match in re.finditer(
        r"Schema::(?:create|table)\(\s*['\"]" + re.escape(table) + r"['\"]\s*,\s*function\s*\([^)]*\)\s*:\s*void\s*\{(.*?)\n\s*}\);",
        text,
        re.S,
    ):
        body = match.group(1)
        for line in body.splitlines():
            line = line.strip()
            if not line.startswith("$table->"):
                continue
            nullable = "->nullable" in line or "nullable()" in line
            # Match $table->method('col', ...)
            m = re.match(r"\$table->([a-zA-Z0-9_]+)\(\s*['\"]([^'\"]+)['\"](?:\s*,[^)]+)?\)", line)
            if not m:
                continue
            method, col = m.group(1), m.group(2)
            if method not in TYPE_MAP:
                continue
            php = TYPE_MAP[method]
            cols[col] = php if not nullable else f"{php}|null"
    return cols


def parse_all_migrations(table: str) -> dict[str, str]:
    cols: dict[str, str] = {}
    for path in find_migration_files():
        cols.update(parse_columns(path.read_text(), table))
    return cols


def parse_casts(text: str) -> dict[str, str]:
    casts: dict[str, str] = {}
    m = re.search(r"protected\s+\$casts\s*=\s*\[(.*?)\];", text, re.S)
    if not m:
        return casts
    body = m.group(1)
    for line in body.splitlines():
        m2 = re.match(r"\s*['\"]([^'\"]+)['\"]\s*=>\s*([^,]+),", line)
        if not m2:
            continue
        col = m2.group(1)
        cast = m2.group(2).strip()
        php = None
        if "UuidCast" in cast:
            php = "string"
        elif cast in ("'int'", '"int"', "'integer'", '"integer"'):
            php = "int"
        elif cast in ("'bool'", '"bool"', "'boolean'", '"boolean"'):
            php = "bool"
        elif cast in ("'datetime'", '"datetime"', "'date'", '"date"'):
            php = "\\Carbon\\Carbon"
        elif "decimal" in cast:
            php = "string"
        elif "::class" in cast:
            php = cast.replace("::class", "").strip()
        if php:
            casts[col] = php
    return casts


def model_table(text: str) -> str | None:
    m = re.search(r"protected\s+\$table\s*=\s*['\"]([^'\"]+)['\"]", text)
    return m.group(1) if m else None


def has_timestamps(text: str) -> bool:
    if re.search(r"public\s+\$timestamps\s*=\s*false", text):
        return False
    return True


def run() -> None:
    for d in DOMAIN_DIRS:
        for path in d.rglob("*.php"):
            text = path.read_text()
            if "extends Model" not in text:
                continue
            table = model_table(text)
            if not table:
                continue
            cols = parse_all_migrations(table)
            casts = parse_casts(text)
            for col, php in casts.items():
                if col in cols and cols[col].endswith("|null") and not php.endswith("|null"):
                    php += "|null"
                cols[col] = php

            if "id" not in cols:
                cols["id"] = "string"
            if has_timestamps(text):
                if "angelegt_am" not in cols:
                    cols["angelegt_am"] = "\\Carbon\\Carbon"
                if "geaendert_am" not in cols:
                    cols["geaendert_am"] = "\\Carbon\\Carbon|null"
            if "version" not in cols:
                cols["version"] = "int"
            for audit in ("angelegt_von", "geaendert_von"):
                if audit in cols:
                    cols[audit] = "string|null"

            ordered = sorted(cols)
            props = [f" * @property {cols[c]} ${c}" for c in ordered]
            docblock = "/**\n" + "\n".join(props) + "\n */"

            if re.search(r"/\*\*.*?\*/\s*\nclass\s+", text, re.S):
                new_text = re.sub(r"/\*\*.*?\*/\s*\n(?=class\s+)", lambda _m: docblock + "\n", text, count=1, flags=re.S)
            else:
                new_text = re.sub(r"(?=class\s+\w+\s+extends\s+Model)", lambda _m: docblock + "\n", text, count=1)
            path.write_text(new_text)
            print(f"Updated {path.relative_to(REPO)}")


if __name__ == "__main__":
    run()
