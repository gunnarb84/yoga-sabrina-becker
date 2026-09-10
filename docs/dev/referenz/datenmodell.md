> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [harness-php/_data.md](../../../harness-php/_data.md), [projekt/_domaene.md](../../../projekt/_domaene.md)
> **Stand:** 2026-09-10

# Datenmodell

## Überblick

Die Datenbank ist eine MySQL/MariaDB-Datenbank pro Installation. Alle Primärschlüssel sind UUID v7 als `binary(16)`, außen als String dargestellt.

## Zentrale Entitäten

| Entität | Bedeutung |
|---|---|
| `activities` | Veranstaltung (Kurs, Event, Workshop) |
| `sessions` | Einzelner Termin einer Veranstaltung |
| `participants` | Teilnehmer/in (`email` optional, Pflicht nur im Online-Anmeldeprozess) |
| `registrations` | Anmeldung einer/m Teilnehmer/in zu einer Veranstaltung (mit `herkunft` = `webseite`/`verwaltung`) |
| `waiting_lists` | Wartelistenplätze über der Kapazität |
| `payments` | Zahlung (Bar oder Überweisung) zu einer Anmeldung |
| `cash_receipts` | Bareinnahmenbeleg |
| `verwaltung_barentnahmen` | Barentnahme aus der Barkasse (Datum, Betrag, Zweck, optionale Fremdbelegnummer; ohne Belegnummer und PDF) |
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
- **Belegnummern:** Lückenlos fortlaufend pro Belegrolle. Der Nummernkreis der
  Bareinnahmenbelege trägt kein Präfix und formatiert als `YYYY-NNNNN`
  (z. B. `2026-00012`); über den Vorgang `RecordWalkInCashPayment` lässt sich eine
  Belegnummer für die Nachpflege handschriftlicher Belege vorgeben, wobei der
  Nummernkreis hinter der übernommenen Nummer weiterschaltet.

## Siehe auch

- [Konfiguration und Umgebung](konfiguration.md)
- [ADR-0002: Binäre UUID v7 als Primärschlüssel](../entscheidungen/0002-binaere-uuid-v7.md)
