> **Typ:** ADR · **Für:** Entwickler · **Bezug:** [harness-php/_architecture.md](../../../../harness-php/_architecture.md)
> **Stand:** 2026-09-03

# ADR-0001: Modulgrenzen und Plattformschicht

## Kontext

Das Projekt hat zwei klar getrennte Nutzungsbereiche:

- eine öffentliche Webseite für Besucher/innen und Teilnehmer/innen,
- ein geschütztes Backend/CMS für Sabrina Becker und ggf. Helfer/innen.

Beide Bereiche teilen aber grundlegende technische Hilfsmittel (UUID-Generierung, Nummernsequenzen, gemeinsame Werttypen).

## Entscheidung

Wir gliedern das System in zwei Fachmodule und eine Plattformschicht:

- `modules/Webseite` – öffentliche Fachlichkeit und Anmeldung,
- `modules/Verwaltung` – CMS und Verwaltung,
- `platform/` – technische Hilfsmittel ohne fachlichen Bezug.

Jedes Modul besteht aus den vier Schichten `Domain`, `Application`, `Persistence` und `Ui`. Plattformcode hat seine eigenen Schichten und darf von Modulen genutzt werden, aber nicht umgekehrt.

## Konsequenzen

- Klarer fachlicher Schnitt; Verwaltungslogik kann später erweitert werden, ohne die Webseite zu beeinflussen.
- Gemeinsame Hilfsmittel liegen zentral und sind testbar.
- Deptrac stellt die erlaubten Abhängigkeiten zwischen den Schichten sicher.

## Status

Angenommen.
