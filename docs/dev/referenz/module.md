> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [harness-php/_design.md](../../../harness-php/_design.md)
> **Stand:** 2026-09-10

# Module und Schichten

## Webseite

| Schicht | Inhalt |
|---|---|
| `Domain` | Activity, Session, Participant, Registration, WaitingList, Payment, HealthNote |
| `Application` | Vorgänge: Kurs/Event/Workshop anlegen, veröffentlichen, anmelden, Warteliste verwalten |
| `Persistence` | Eloquent-Modelle und Migrationen mit binären UUID-Primärschlüsseln |
| `Ui` | Blade-Views, Livewire-Komponenten für öffentliche Darstellung und Anmeldung |

## Verwaltung

| Schicht | Inhalt |
|---|---|
| `Domain` | Benutzer, Rollen, Rechte, Verwaltungsmodelle |
| `Application` | Vorgänge: Anmeldung bearbeiten, Zahlung erfassen, Bareinnahme ohne Anmeldung erfassen (`RecordWalkInCashPayment`), Barentnahme erfassen (`RecordCashWithdrawal`), Beleg erzeugen (PDF nach der Papiervorlage mit `AmountInWords`), gemischte Kassenbewegungsliste mit laufendem Bestand (`ListCashMovementsQuery`) |
| `Persistence` | Verwaltungsmigrationen, Eloquent-Modelle, Query-Objekte |
| `Ui` | Livewire-basiertes Admin-UI, Login, Listen und Masken (u. a. „Bareinnahme erfassen“ für Laufkundschaft und Nachpflege) |

## Platform

- `Identity`: UUID v7 Erzeugung und Casting.
- `NumberSequence`: Transaktionsgesicherte, fortlaufende Nummern.
- `Shared`: Gemeinsame Wertobjekte (`Money`, `Duration`, `DbValue`) und das `Result<T>`-Konstrukt.

## Siehe auch

- [Datenmodell](datenmodell.md)
- [Einen neuen Vorgang anlegen](../how-to/neuer-vorgang.md)
