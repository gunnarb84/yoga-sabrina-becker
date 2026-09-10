# Bareinnahmenliste und Beleg-PDF

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich alle Kassenbewegungen in einer gemischten Liste mit
laufendem Bestand einsehen und Bareinnahmenbelege als PDF herunterladen, damit ich meine
Barkasse ohne handschriftliche Liste nachvollziehen und eine Quittung jederzeit
nachweisen kann.

## Description
Die Bareinnahmenliste ersetzt die bisherige manuelle Bareinnahmenliste. Sie zeigt alle
Kassenbewegungen — Bareinnahmenbelege, Bar-Rückzahlungen und Barentnahmen — gemischt-
chronologisch mit dem laufenden Bestand je Zeile (Kassenbuch-Prinzip) und ist über einen
eigenen Menüpunkt in der Modulgruppe „Verwaltung" erreichbar. Jeder Bareinnahmenbeleg
kann als PDF heruntergeladen werden — das PDF folgt exakt dem Aufbau der Papiervorlage
„Barquittung" (A4 mit zwei identischen Quittungshälften „Original – für Teilnehmer:in"
und „Durchschrift – für Unterlagen", getrennt durch eine Trennlinie mit Scherensymbol),
im Design der Vorlage (warmes Beige, Gold-Akzente, dunkle Farbfläche, Schrift Lato).

## Akzeptanzkriterien
- Die Bareinnahmenliste zeigt alle `CashReceipt` mit Ausstellungsdatum, Belegnummer,
  Empfänger und Betrag.
- Die Bareinnahmenliste zeigt alle `CashReturn` als Abgang mit Ausstellungsdatum,
  Belegnummer, Empfänger und Betrag.
- Die Bareinnahmenliste zeigt alle `CashWithdrawal` als Abgang mit Datum, Zweck und
  optionaler Fremdbelegnummer.
- Die Abfrage `ListCashMovements` liefert alle Kassenbewegungen gemischt-chronologisch,
  neueste zuerst.
- Die Bareinnahmenliste zeigt je Zeile den laufenden `Bestand`: Bareinnahmen als Zugang,
  Bar-Rückzahlungen und Barentnahmen als Abgang.
- Der Menüpunkt „Bareinnahmen" ist unter der Modulgruppe „Verwaltung" erreichbar.
- Die Bareinnahmenliste kann nach Beleg- bzw. Fremdbelegnummer und nach Empfänger/in
  bzw. Zweck gefiltert werden.
- Die Abfrage `GenerateCashReceiptPdf` erzeugt das PDF eines `CashReceipt`; sie scheitert
  mit dem Fehlercode `RECEIPT_NOT_FOUND`, wenn der Beleg nicht existiert.
- Ein Bareinnahmenbeleg kann aus der Bareinnahmenliste als PDF heruntergeladen werden;
  Bar-Rückzahlungen und Barentnahmen erhalten keinen PDF-Download.
- Das Beleg-PDF nennt die Belegnummer im Format `YYYY-NNNNN` sowie Ausstellungsdatum,
  Empfänger und Betrag.
- Das Beleg-PDF enthält zwei identische Quittungshälften („Original – für Teilnehmer:in"
  oben, „Durchschrift – für Unterlagen" unten), getrennt durch eine Trennlinie mit
  Scherensymbol, jeweils im Aufbau der Papiervorlage.
- Jede Quittungshälfte trägt die Felder Beleg-Nr., Datum, Erhalten von, Betrag, „In
  Worten", „Für folgende Leistung / Kurs", den Hinweis „Kleinunternehmer gemäß § 19 UStG –
  kein gesonderter Umsatzsteuerausweis." sowie „Betrag dankend bar erhalten.".
- Der Betrag wird „in Worten" automatisch ausgeschrieben (z. B. „fünfundvierzig Euro und
  null Cent").
- Die Zeile „Ort, Datum" ist mit Bergen und dem Ausstellungsdatum vorausgefüllt; die
  Unterschrift bleibt unausgefüllt und wird handschriftlich ergänzt.