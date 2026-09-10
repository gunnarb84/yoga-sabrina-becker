# Bareinnahme ohne Anmeldung erfassen

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich eine Bareinnahme für jemanden erfassen, der nicht über die
Webseite angemeldet war, damit auch Laufkundschaft mit Barquittung im System abgebildet ist
und ich meine bisher handschriftlich vergebenen Bareinnahmenbelege nachpflegen kann.

## Description
In der Verwaltung gibt es eine eigene Erfassungsmaske „Bareinnahme erfassen": Veranstaltung
wählen, Vor- und Nachname, E-Mail-Adresse optional, Betrag frei. Der Vorgang
`RecordWalkInCashPayment` legt in einem Rutsch Teilnehmer/in (falls nicht schon vorhanden),
Anmeldung zur Veranstaltung, Barzahlung und Bareinnahmenbeleg an — ohne
Kapazitäts- und Wartelistenprüfung, weil die Person anwesend ist und gezahlt hat.
Die dieselbe Maske dient der Nachpflege handschriftlicher Belege: mit eingeschalteter
Nachpflege werden Belegnummer und Ausstellungsdatum vorgegeben, statt eine neue Nummer zu
ziehen. Es gilt die Regel: erst nachpflegen, dann die erste neue Barzahlung erfassen —
der Nummernkreis läuft automatisch hinter der höchsten vorgegebenen Nummer weiter.

## Akzeptanzkriterien

### Erfassung ohne Anmeldung
- Der Vorgang `RecordWalkInCashPayment` legt zu einer Veranstaltung Teilnehmer/in, Anmeldung,
  `Payment` mit der Methode `CASH` und einen `CashReceipt` an.
- `RecordWalkInCashPayment` scheitert mit dem Fehlercode `ACTIVITY_NOT_FOUND`, wenn die
  Veranstaltung nicht existiert.
- `RecordWalkInCashPayment` scheitert mit dem Fehlercode `PARTICIPANT_NAME_REQUIRED`, wenn
  Vorname oder Nachname leer sind.
- `RecordWalkInCashPayment` scheitert mit dem Fehlercode `PARTICIPANT_EMAIL_INVALID`, wenn
  eine E-Mail-Adresse angegeben ist, die nicht dem Format einer E-Mail-Adresse entspricht.
- `RecordWalkInCashPayment` scheitert mit dem Fehlercode `AMOUNT_INVALID`, wenn der Betrag
  fehlt oder kleiner gleich null ist.
- `RecordWalkInCashPayment` scheitert mit dem Fehlercode `REGISTRATION_ALREADY_EXISTS`, wenn
  für die Person bereits eine nicht stornierte Anmeldung zu derselben Veranstaltung besteht.
- Existiert bereits eine Teilnehmerin/ein Teilnehmer mit exakt übereinstimmendem Vor- und
  Nachnamen, verwendet `RecordWalkInCashPayment` diesen Datensatz, ohne die Stammdaten zu
  ändern; andernfalls legt er eine neue Teilnehmerin/einen neuen Teilnehmer an.
- Die von `RecordWalkInCashPayment` angelegte Anmeldung erhält den Status `Bestätigt`, die
  Zahlungsart `Bar` und die Herkunft `Verwaltung`, ohne dass eine Kapazitäts- oder
  Wartelistenprüfung stattfindet.
- Der Zahlungsstatus der angelegten Anmeldung wird auf „bezahlt" gesetzt.
- Der Vorgang `RecordCashPaymentBatch` (S7) bleibt von der Erfassung ohne Anmeldung
  unberührt: Laufkunden-Anmeldungen erscheinen dort nicht, weil sie bereits bezahlt sind.
- Die Barquittung wird bei vorhandener E-Mail-Adresse automatisch per E-Mail versandt
  (siehe S6); ohne E-Mail-Adresse entsteht nur der Beleg mit PDF-Download.
- Der Menüpunkt „Bareinnahme erfassen" ist unter der Modulgruppe „Verwaltung" erreichbar
  (siehe `guidelines/_Menüstruktur.md`).
- In der Erfassungsmaske lässt sich eine Veranstaltung aus allen Veranstaltungen auswählen
  und der Betrag frei erfassen; das Erfassungsdatum ist mit dem heutigen Datum vorbelegt.

### Nachpflege handschriftlicher Belege
- Mit eingeschalteter Nachpflege erstellt `RecordWalkInCashPayment` den `CashReceipt` mit
  der vorgegebenen Belegnummer statt mit der nächsten Nummer aus dem Nummernkreis.
- Die vorgegebene Belegnummer entspricht dem Format `YYYY-NNNNN`; bei Abweichung scheitert
  der Vorgang mit dem Fehlercode `RECEIPT_NUMBER_INVALID`.
- Die vorgegebene Belegnummer ist eindeutig; bei einer bereits vergebenen Nummer scheitert
  der Vorgang mit dem Fehlercode `RECEIPT_NUMBER_TAKEN`.
- Mit eingeschalteter Nachpflege trägt der Beleg das vorgegebene Ausstellungsdatum;
  ohne Nachpflege das Erfassungsdatum.
- Eine vorgegebene Belegnummer setzt den Stand ihres Nummernkreises mindestens auf ihre
  Nummer, sodass nachfolgende Belege hinter ihr fortlaufend weiterzählen.

### Belegnummernkreis
- Belegnummern des Bareinnahmenbelegs entsprechen dem Format `YYYY-NNNNN` und sind
  lückenlos fortlaufend je Jahr.