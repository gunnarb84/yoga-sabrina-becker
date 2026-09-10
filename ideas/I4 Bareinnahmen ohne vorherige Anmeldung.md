# Bareinnahmen ohne vorherige Anmeldung

## Meta
- **State:** Ready

## Problem
Barzahlungen sind im System bisher fest an eine Anmeldung gekettet, die über die Webseite
entstanden ist. Kundinnen und Kunden, die ohne vorherige Web-Anmeldung bar zahlen (z. B.
Drop-in am Kurstag), lassen sich dadurch nicht erfassen — für sie müsste Sabrina Becker
weiterhin handschriftliche Quittungen schreiben und die Bareinnahme bleibt aus der
Bareinnahmenliste und der Einnahmenübersicht heraus. Außerdem will sie ihre bereits
handschriftlich vergebenen Bareinnahmenbelege nachträglich ins System einbuchen, damit alle
Bareinnahmen an einem Ort nachvollziehbar sind.

## Lösungsidee
Ein eigener schneller Vorgang in der Verwaltung (eine Maske, ein Vorgang): Veranstaltung
wählen, Name der Person, E-Mail-Adresse optional, Betrag frei erfassbar. Der Vorgang legt
in einem Rutsch an: Teilnehmer/in (falls nicht schon vorhanden), Anmeldung zur
Veranstaltung (direkt bestätigt und bezahlt), Barzahlung und Bareinnahmenbeleg.
Die gleiche Maske dient der Nachpflege der handschriftlichen Belege: dort werden
Original-Belegnummer und Original-Datum vorgegeben, statt eine neue Nummer zu ziehen.

## Scope
- **In Scope:**
  - Neue Erfassungsmaske „Bareinnahme ohne Anmeldung" (Laufkundschaft) in der Verwaltung.
  - Automatisches Anlegen von Teilnehmer/in und Anmeldung im selben Vorgang.
  - Teilnehmertreffer: Bei exakt übereinstimmendem Vor- und Nachname wird der bestehende
    Teilnehmerdatensatz wiederverwendet, sonst neu angelegt.
  - E-Mail-Adresse optional; Barquittung per E-Mail nur bei vorhandener Adresse (Verhalten
    wie in F4-S6, sonst nur PDF-Download).
  - Keine Kapazitäts-/Wartelistenprüfung für so angelegte Anmeldungen (Person ist
    physisch anwesend).
  - Nachpflege handschriftlicher Belege mit vorgegebener Belegnummer und Datum.
  - Herkunft-Feld an der Anmeldung („Webseite" oder „Verwaltung"), ausgewiesen in den
    Anmeldelisten.
  - Umstellung des Belegnummernformats auf `YYYY-NNNNN` (lückenlos, startend bei 1;
    Nummernkreis läuft weiter hinter den nachgepflegten Nummern); eindeutige Belegnummer
    wird technisch erzwungen.
- **Out of Scope:**
  - Barzahlungen ohne jeden Veranstaltungsbezug (z. B. Materialverkauf).
  - Belegung einzelner Termine (Drop-in-Termine bleiben nicht nachvollziehbar; die
    Anmeldung gilt der ganzen Veranstaltung, der Betrag ist frei).
  - Änderungen am Online-Anmeldeflow der Webseite (Webseitenvorgänge setzen Herkunft
    „Webseite" unverändert).

## Auswirkungen auf den Bestand
- **Specs:** Neue Story in `specs/E2 CMS & Verwaltung/F4 Zahlungen und Belege verwalten/`
  (Erfassung für Laufkundschaft inkl. Nachpflege); Anpassung von S1 (Belegnummernformat
  `YYYY-NNNNN` statt `B-YYYY-NNNNN`) und S7 (Massenerfassung betrifft nur bestehende
  Anmeldungen); Meta-`State` der betroffenen Stories auf `Modified`. Betrifft zudem die
  Teilnehmerverwaltung (F2: E-Mail optional) und die Anmeldungsverwaltung (F3: Anmeldungen
  ohne Kapazitätsprüfung durch die Verwaltung).
- **Datenstruktur:** `Participant.email` wird optional (Pflicht nur noch im
  Web-Anmeldeflow); neues Feld `source` (Herkunft: Webseite/Verwaltung) an der Anmeldung;
  Belegnummernkreis `YYYY-NNNNN`.
- **Backend:** Neuer Vorgang (z. B. `RecordWalkInCashPayment`) unter
  `modules/Verwaltung/Application/CashReceipt/`, der Teilnehmer/in, Anmeldung, Barzahlung
  und Beleg in einer Transaktion anlegt; Nummernkreis-Service
  (`Application/NumberSequence`) auf neues Format umgestellt und für Nachpflege mit
  vorgegebener Nummer nutzbar; `GenerateCashReceiptPdf` toleriert Teilnehmer/in ohne
  E-Mail.
- **Quellcode:** Berührungspunkte: `modules/Verwaltung/Application/CashReceipt/`,
  `modules/Verwaltung/Application/NumberSequence/`, `modules/Verwaltung/Domain/Participant/`,
  `modules/Verwaltung/Ui/Payment/` bzw. neue Ui-Maske,
  `src/resources/views/bareinnahmenbelege/`. Risiko: gering — die Belegkette
  Zahlung→Anmeldung bleibt intakt, es kommt nur ein Weg hinzu, wie eine Anmeldung
  entsteht.

## Entscheidungen
- 10.09.2026: Idee angelegt, Nummer I4 reserviert.
- 10.09.2026: Jede Bareinnahme bezieht sich immer auf eine konkrete Veranstaltung;
  Teilnehmer/in und Anmeldung werden vom Vorgang automatisch angelegt (Datenmodell bleibt
  intakt).
- 10.09.2026: Mindestangaben: Name; E-Mail-Adresse optional. Beleg-E-Mail nur bei
  vorhandener Adresse.
- 10.09.2026: Eigener Vorgang mit einer Maske statt Umweg über die Anmeldungsmaske.
- 10.09.2026: Anmeldung gilt der ganzen Veranstaltung, Betrag frei wählbar (keine
  Einzeltermin-Nachverfolgung).
- 10.09.2026: Ohne Kapazitäts-/Wartelistenprüfung — die Person ist anwesend und hat schon
  gezahlt.
- 10.09.2026: Belegnummern werden auf Vorlagenformat `YYYY-NNNNN` umgestellt (rechtlich
  reicht lückenlose Fortlaufendheit, Präfix ist kein Muss).
- 10.09.2026: Nachpflege der handschriftlichen Belege **vor** der ersten neuen Barzahlung
  (feste Reihenfolge schließt Nummernkollisionen aus); danach startet der Nummernkreis
  automatisch hinter der höchsten nachgepflegten Nummer.
- 10.09.2026: Herkunft-Feld an der Anmeldung ergänzt (Webseite/Verwaltung), ausgewiesen in
  den Anmeldelisten — so sind Laufkunden-Anmeldungen sofort erkennbar.

## Offene Punkte
- (leer)