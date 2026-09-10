# Backend — E2 CMS & Verwaltung

Vorgänge, Abfragen und Ereignisse des Epics. Fachliche Felder siehe `_Datenstruktur.md`.

## Abfragen

### `ListActivities`
Liefert alle Veranstaltungen mit Filter nach `type`, `status`, Titel und Datum.

### `GetActivity`
Liefert eine einzelne Veranstaltung inkl. aller Termine.

### `ListParticipants`
Liefert alle Teilnehmer/innen mit Filter nach Name/E-Mail.

### `GetParticipant`
Liefert einen Teilnehmer/eine Teilnehmerin inkl. Historie der Anmeldungen.

### `ListRegistrations`
Liefert alle Anmeldungen zu einer Veranstaltung oder eines Teilnehmers/einer Teilnehmerin.

### `ListWaitingListEntries`
Liefert die aktuelle Warteliste einer Veranstaltung sortiert nach `rank`.

### `ListPayments`
Liefert alle Zahlungen mit Filter nach Veranstaltung, Teilnehmer/in, Zahlungsart und Belegnummer.

### `ListCashReceipts`
Liefert alle Bareinnahmenbelege mit Filter nach Belegnummer und Empfänger, absteigend
sortiert nach Ausstellungsdatum und Belegnummer.

### `ListOpenCashRegistrations`
Liefert alle Anmeldungen einer Veranstaltung mit Zahlungsart `Bar`, Status `Bestätigt`
und ohne erfasste Zahlung.

### `GenerateCashReceiptPdf`
Erzeugt das PDF eines Bareinnahmenbelegs. Fehlercode: `RECEIPT_NOT_FOUND`.

### `ListOutboundMessages`
Liefert alle ausgehenden Nachrichten mit Filter nach Empfänger, Status und Anmeldung.

## Vorgänge

### `CreateActivity`
Legt eine neue Veranstaltung an. Fehlercodes: `TITLE_EMPTY`, `PRICE_NEGATIVE`,
`MAX_PARTICIPANTS_TOO_LOW`.

### `UpdateActivity`
Ändert eine Veranstaltung, solange sie nicht abgeschlossen oder storniert ist. Fehlercodes:
`NOT_FOUND`, `ALREADY_COMPLETED`, `ALREADY_CANCELLED`.

### `PublishActivity`
Veröffentlicht eine Veranstaltung. Fehlercode: `ALREADY_PUBLISHED`.

### `UnpublishActivity`
Setzt eine Veranstaltung auf Entwurf zurück, sofern keine Anmeldungen vorliegen. Fehlercode:
`HAS_REGISTRATIONS`.

### `GenerateSessionsFromTemplate`
Erzeugt Termine für einen Kurs aus einer Vorlage (`CourseTemplate`). Fehlercode:
`INVALID_TEMPLATE`.

### `RegisterParticipant`
Meldet eine Teilnehmerin/einen Teilnehmer zu einer Veranstaltung an. Erkennt bestehende
Teilnehmer/innen anhand der `email` und aktualisiert ggf. Stammdaten. Erzeugt `Registration`,
ordnet der Anmeldung je nach freien Plätzen den Status `CONFIRMED` oder `WAITING_LIST` zu.
Erzeugt bei Überweisung automatisch `Invoice` und `OutboundMessage` mit PDF-Anhang.
Fehlercodes: `ACTIVITY_NOT_BOOKABLE`, `EMAIL_INVALID`, `HEALTH_NOTES_WITHOUT_CONSENT`.

### `CancelRegistration`
Storniert eine Anmeldung über das Backend. Erzeugt ggf. `CreditNote` oder `CashReturn`. Löst
`PromoteWaitingListEntry` aus, wenn die Warteliste nicht leer ist. Fehlercodes:
`NOT_FOUND`, `ALREADY_CANCELLED`.

### `PromoteWaitingListEntry`
Rückt den ersten Eintrag der Warteliste nach und benachrichtigt die Teilnehmerin/den
Teilnehmer per `OutboundMessage`. Fehlercode: `WAITING_LIST_EMPTY`.

### `RecordCashPayment`
Erfasst eine Barzahlung zu einer bestehenden Anmeldung, erzeugt `CashReceipt` samt PDF und
versendet die Barquittung per E-Mail an die Teilnehmerin/den Teilnehmer (sofern eine
E-Mail-Adresse vorhanden ist). Unterstützt die Nachpflege mit vorgegebener Belegnummer und
Ausstellungsdatum (siehe `RecordWalkInCashPayment`). Fehlercode:
`REGISTRATION_NOT_FOUND`, `ALREADY_PAID`.

### `RecordWalkInCashPayment`
Erfasst eine Bareinnahme ohne vorherige Anmeldung (Laufkundschaft) und legt in einem
Vorgang Teilnehmer/in (Wiederverwendung bei exakt übereinstimmendem Vor- und Nachnamen),
Anmeldung (Status `Bestätigt`, Zahlungsart `Bar`, Herkunft `Verwaltung`, ohne
Kapazitäts- und Wartelistenprüfung), `Payment` und `CashReceipt` an. Die Barquittung wird
bei vorhandener E-Mail-Adresse per E-Mail versandt. Mit eingeschalteter Nachpflege erhält
der Beleg die vorgegebene Belegnummer (Format `YYYY-NNNNN`, eindeutig) und das
vorgegebene Ausstellungsdatum; der Stand des Nummernkreises wird mindestens auf die
vorgegebene Nummer gesetzt. Fehlercodes: `ACTIVITY_NOT_FOUND`,
`PARTICIPANT_NAME_REQUIRED`, `PARTICIPANT_EMAIL_INVALID`, `AMOUNT_INVALID`,
`REGISTRATION_ALREADY_EXISTS`, `RECEIPT_NUMBER_INVALID`, `RECEIPT_NUMBER_TAKEN`.

### `RecordCashPaymentBatch`
Erfasst die angehakten Barzahlungen einer Veranstaltung en bloc: je Anmeldung `Payment`
und `CashReceipt` mit lückenloser Nummer, Zahlungsstatus „bezahlt" und Barquittung per
E-Mail. Scheitert eine einzelne Anmeldung (z. B. `ALREADY_PAID`), werden die übrigen
trotzdem erfasst. Fehlercodes: `ACTIVITY_NOT_FOUND`, `REGISTRATION_NOT_FOUND`,
`ALREADY_PAID`, `PAYMENT_METHOD_NOT_SUPPORTED`.

### `CreateCreditNote`
Erzeugt eine Gutschrift zu einer Rechnung. Fehlercode: `INVOICE_NOT_FOUND`.

### `CreateCashReturn`
Erzeugt eine Rückgabebestätigung zu einem Bareinnahmenbeleg. Fehlercode:
`CASH_RECEIPT_NOT_FOUND`.

### `ResendOutboundMessage`
Versucht eine fehlgeschlagene oder ausstehende Nachricht erneut zu senden. Fehlercode:
`MESSAGE_NOT_FOUND`.

## Ereignisse

- `ActivityPublished` — Veranstaltung wurde veröffentlicht.
- `RegistrationCreated` — Neue Anmeldung angelegt.
- `RegistrationCancelled` — Anmeldung storniert.
- `WaitingListEntryPromoted` — Wartelistenplatz nachgerückt.
- `InvoiceIssued` — Rechnung ausgestellt.
- `CashReceiptIssued` — Bareinnahmenbeleg ausgestellt.
- `CreditNoteIssued` — Gutschrift ausgestellt.
- `CashReturnIssued` — Rückgabebestätigung ausgestellt.
- `OutboundMessageSent` — E-Mail wurde versendet.
- `OutboundMessageFailed` — E-Mail-Versand ist fehlgeschlagen.
