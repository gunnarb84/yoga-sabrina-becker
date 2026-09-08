# Backend — E1 Öffentliche Webseite

Vorgänge, Abfragen und Ereignisse des Epics. Fachliche Felder siehe `_Datenstruktur.md`.

## Abfragen

### `ListPublishedActivities`
Liefert alle zukünftigen, veröffentlichten Veranstaltungen mit `type`, `title`,
`shortDescription`, `price`, `freeSeats`, `waitingListCount`, `isBookable`, `image`.

### `GetActivityDetails`
Liefert die Detailansicht einer einzelnen Veranstaltung inkl. aller öffentlichen
Termine (`Session`) und Langbeschreibung.

### `ListPublishedPages`
Liefert alle veröffentlichten Seiten mit `slug`, `title`.

### `GetPageContent`
Liefert Inhalt und Meta-Daten einer Seite anhand von `slug`.

### `ListNavigationItems`
Liefert die öffentliche Navigation sortiert nach `sortOrder`.

### `ListContactInquiries`
Liefert Kontaktanfragen mit `receivedAt`, `name`, `email`, `topic`, `status`; Filter nach
`status` (Verwaltung, siehe F4 S4).

### `GetContactInquiry`
Liefert eine einzelne Kontaktanfrage inklusive `message` und `note`.

## Vorgänge

### `RecordContactInquiry`
Nimmt eine Kontaktanfrage vom Anfrageformular entgegen und speichert sie mit dem Status
`Neu`. Prüft Pflichtfelder und das Format von `email`. Verwirft Anfragen mit ausgefülltem
verstecktem Spamschutz-Feld stillschweigend (Erfolgsmeldung). Erlaubt höchstens 5 Anfragen
innerhalb von 10 Minuten je IP-Adresse.

Fehlercodes:
- `contact_inquiry.invalid_email` — `email` entspricht nicht dem Format.
- `contact_inquiry.invalid_input` — ein Pflichtfeld fehlt oder ein Feld überschreitet
  seine Länge.
- `contact_inquiry.rate_limited` — mehr als 5 Anfragen innerhalb von 10 Minuten je
  IP-Adresse.

Ebenso in der Verwaltung (F4 S4):
- `UpdateContactInquiry` — ändert `status` und `note` einer Anfrage.
- `contact_inquiry.not_found` — Anfrage nicht gefunden.
- `contact_inquiry.invalid_status` — `status` ist keiner der drei gültigen Werte.

## Ereignisse

E1 veröffentlicht keine Domain Events. Die E-Mails nach `RecordContactInquiry` werden als
`OutboundMessage` protokolliert und sind in der Verwaltung über E2 F5 einsehbar.
