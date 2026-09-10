# Zahlungen erfassen

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich Zahlungen zu Anmeldungen erfassen können, damit ich den
Zahlungseingang und Bareinnahmen nachvollziehen kann.

## Description
Für jede Anmeldung wird die gewählte Zahlungsart gespeichert. Bei Barzahlungen erzeugt das
System einen Bareinnahmenbeleg. Bei Überweisungen wurde die Rechnung bereits bei der
Anmeldung erstellt.

## Akzeptanzkriterien
- Der Menüpunkt „Zahlungen" ist unter der Modulgruppe „Verwaltung" erreichbar.
- In der Anmeldungsmaske kann eine Barzahlung erfasst werden.
- Der Vorgang `RecordCashPayment` legt eine `Payment` mit der Methode `CASH` an.
- `RecordCashPayment` erzeugt automatisch einen `CashReceipt` mit der nächsten freien
  lückenlosen Nummer im Format `B-YYYY-NNNNN`.
- `RecordCashPayment` scheitert mit `REGISTRATION_NOT_FOUND`, wenn die Anmeldung nicht
  existiert.
- `RecordCashPayment` scheitert mit `ALREADY_PAID`, wenn bereits eine Zahlung erfasst wurde.
- Der Zahlungsstatus der Anmeldung wird auf „bezahlt" gesetzt.
- Die Bareinnahmenliste zeigt alle `CashReceipt` mit Datum, Nummer, Empfänger und Betrag
  (siehe S5).
