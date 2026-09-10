# Barentnahmen erfassen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich Barentnahmen aus der Barkasse erfassen (z. B. private
Entnahme oder Barkauf), damit die Bareinnahmenliste immer den korrekten Bestand ausweist.

## Description
Auf der Bareinnahmen-Seite lässt sich über den Button „Barentnahme erfassen" eine
Erfassungsmaske öffnen. Eine Barentnahme ist kein Belegvorgang: Sie erhält keine
Belegnummer und kein PDF. Erfasst werden Datum, Betrag, Zweck und optional eine
Fremdbelegnummer (z. B. die Kassenquittung eines Barkaufs). Nach dem Speichern erscheint
die Barentnahme als Abgang in der Bareinnahmenliste (siehe S5).

## Akzeptanzkriterien
- Der Vorgang `RecordCashWithdrawal` legt eine `CashWithdrawal` mit `date`, `amount`,
  `purpose` und `externalReference` an.
- Der Vorgang scheitert mit dem Fehlercode `PURPOSE_REQUIRED`, wenn `purpose` leer ist.
- Der Vorgang scheitert mit dem Fehlercode `AMOUNT_INVALID`, wenn `amount` nicht größer
  als 0 ist.
- Der Vorgang scheitert mit dem Fehlercode `DATE_INVALID`, wenn `date` kein gültiges
  Datum ist.
- Das Feld `externalReference` ist optional.
- Eine Barentnahme erhält keine Belegnummer und kein PDF.
- Auf der Bareinnahmen-Seite öffnet der Button „Barentnahme erfassen" die Maske mit den
  Feldern `date`, `amount`, `purpose` und `externalReference`.
- Das Feld `date` ist in der Maske mit dem heutigen Datum vorbelegt.
- Nach dem Speichern erscheint die Barentnahme in der Bareinnahmenliste.