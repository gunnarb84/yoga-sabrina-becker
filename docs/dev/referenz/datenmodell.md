> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [harness-php/_data.md](../../../harness-php/_data.md), [projekt/_domaene.md](../../../projekt/_domaene.md)
> **Stand:** 2026-09-08

# Datenmodell

## Überblick

Die Datenbank ist eine MySQL/MariaDB-Datenbank pro Installation. Alle Primärschlüssel sind UUID v7 als `binary(16)`, außen als String dargestellt.

## Zentrale Entitäten

| Entität | Bedeutung |
|---|---|
| `activities` | Veranstaltung (Kurs, Event, Workshop) |
| `sessions` | Einzelner Termin einer Veranstaltung |
| `participants` | Teilnehmer/in |
| `registrations` | Anmeldung einer/m Teilnehmer/in zu einer Veranstaltung |
| `waiting_lists` | Wartelistenplätze über der Kapazität |
| `payments` | Zahlung (Bar oder Überweisung) zu einer Anmeldung |
| `cash_receipts` | Bareinnahmenbeleg |
| `invoices` | Rechnung |
| `credit_notes` | Gutschrift |
| `cash_returns` | Rückgabebestätigung |
| `outbound_messages` | Versandte E-Mail |
| `webseite_kontaktanfragen` | Kontaktanfrage aus dem Webformular (Modul Webseite) |
| `users` | Backend-Benutzer/in |

## Datentypen

- **Preis:** Ganzzahl in der kleinsten Währungseinheit (Cent), gespeichert als `int`, Währung in einer separaten Spalte (Hauswährung `EUR`).
- **Dauer:** Minuten als Ganzzahl, Anzeige als Stunden/Minuten.
- **Maximale Teilnehmerzahl:** Positive Ganzzahl.
- **Belegnummern:** Lückenlos fortlaufend pro Belegrolle.

## Siehe auch

- [Konfiguration und Umgebung](konfiguration.md)
- [ADR-0002: Binäre UUID v7 als Primärschlüssel](../entscheidungen/0002-binaere-uuid-v7.md)
