# Barzahlungen massenweise erfassen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich die Barzahlungen einer Veranstaltung in einem Durchgang
erfassen, damit ich nach einem Termin nicht je Teilnehmerin/je Teilnehmer einzeln die
Zahlungsmaske öffnen muss.

## Description
Die Massenerfassung listet alle offenen Bar-Anmeldungen einer Veranstaltung. Pro Anmeldung
setzt die Administratorin einen „bezahlt"-Haken; das Zahlungsdatum ist vorbelegt. Beim
Speichern werden für jede angehakte Anmeldung Zahlung und Bareinnahmenbeleg erzeugt
(samt Belegnummer) und die Barquittung per E-Mail versandt. Die Einzelerfassung bleibt
bestehen; Anmeldungen, die inzwischen bereits bezahlt sind, werden übersprungen.

## Akzeptanzkriterien
- Der Vorgang `RecordCashPaymentBatch` nimmt eine Veranstaltung und je Anmeldung einen
  „bezahlt"-Vermerk mit Zahlungszeitpunkt entgegen.
- Die Abfrage `ListOpenCashRegistrations` liefert alle Anmeldungen einer Veranstaltung
  mit Zahlungsart `Bar`, Status `Bestätigt` und ohne erfasste Zahlung.
- Für jede angehakte Anmeldung erzeugt `RecordCashPaymentBatch` eine `Payment` mit der
  Methode `CASH` und einen `CashReceipt` mit der nächsten freien lückenlosen Nummer im
  Format `YYYY-NNNNN`.
- Der Zahlungsstatus jeder erfassten Anmeldung wird auf „bezahlt" gesetzt.
- Anmeldungen mit Zahlungsart `Überweisung` oder `Kostenlos` erscheinen nicht in der
  Massenerfassung.
- Der Vorgang scheitert für eine einzelne Anmeldung mit dem Fehlercode `ALREADY_PAID`,
  wenn zwischen Laden und Speichern bereits eine Zahlung erfasst wurde; die übrigen
  Anmeldungen werden trotzdem erfasst.
- Der Vorgang scheitert mit dem Fehlercode `ACTIVITY_NOT_FOUND`, wenn die Veranstaltung
  nicht existiert.
- Für jede erfasste Zahlung wird die Barquittung per E-Mail versandt (siehe S6).