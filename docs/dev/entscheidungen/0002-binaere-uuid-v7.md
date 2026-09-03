> **Typ:** ADR · **Für:** Entwickler · **Bezug:** [harness-php/_data.md](../../../../harness-php/_data.md)
> **Stand:** 2026-09-03

# ADR-0002: Binäre UUID v7 als Primärschlüssel

## Kontext

Für Primärschlüssel brauchen wir einen Wert, der:

- global eindeutig ist,
- keine sensiblen Sequenzen preisgibt,
- zeitlich sortierbar ist (wichtig für Belege und Listen),
- mit MySQL 8 und MariaDB 10.6+ funktioniert.

## Entscheidung

Wir verwenden **UUID v7** und speichern sie als `binary(16)` in der Datenbank. Nach außen (API, URLs, UI) werden sie als kanonischer 36-Zeichen-String dargestellt. Das Casting übernimmt `Yoga\Platform\Identity\UuidCast`.

## Konsequenzen

- + Keine Autoincrement-IDs; keine Enumeration-Anfälligkeit.
- + Zeitliche Sortierbarkeit erleichtert Indexe und Belege.
- + Geringer Speicherbedarf im Vergleich zu `char(36)`.
- − Etwas aufwändigere Lesbarkeit im SQL-Client; hier helfen Debugging-Hilfsmittel.

## Status

Angenommen.
