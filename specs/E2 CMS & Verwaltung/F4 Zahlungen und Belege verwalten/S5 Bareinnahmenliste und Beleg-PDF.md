# Bareinnahmenliste und Beleg-PDF

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich alle Bareinnahmenbelege in einer Liste einsehen und als PDF
herunterladen, damit ich meine Bareinnahmen ohne handschriftliche Liste nachvollziehen und
eine Quittung jederzeit nachweisen kann.

## Description
Die Bareinnahmenliste ersetzt die bisherige manuelle Bareinnahmenliste. Sie zeigt alle
Bareinnahmenbelege und ist über einen eigenen Menüpunkt in der Modulgruppe „Verwaltung"
erreichbar. Jeder Beleg kann als PDF heruntergeladen werden — das PDF folgt exakt dem
Aufbau der Papiervorlage „Barquittung" (A4 mit zwei identischen Quittungshälften
„Original – für Teilnehmer:in" und „Durchschrift – für Unterlagen", getrennt durch eine
Trennlinie mit Scherensymbol), im Design der Vorlage (warmes Beige, Gold-Akzente, dunkle
Farbfläche, Schrift Lato).

## Akzeptanzkriterien
- Die Bareinnahmenliste zeigt alle `CashReceipt` mit Ausstellungsdatum, Belegnummer,
  Empfänger und Betrag.
- Der Menüpunkt „Bareinnahmen" ist unter der Modulgruppe „Verwaltung" erreichbar.
- Die Abfrage `ListCashReceipts` liefert die Belege, absteigend sortiert nach
  `issuedAt` und `number`.
- Die Bareinnahmenliste kann nach Belegnummer und Empfänger gefiltert werden.
- Die Abfrage `GenerateCashReceiptPdf` erzeugt das PDF eines `CashReceipt`; sie scheitert
  mit dem Fehlercode `RECEIPT_NOT_FOUND`, wenn der Beleg nicht existiert.
- Ein Bareinnahmenbeleg kann aus der Bareinnahmenliste als PDF heruntergeladen werden.
- Das Beleg-PDF nennt die Belegnummer im Format `YYYY-NNNNN` sowie Ausstellungsdatum,
  Empfänger und Betrag.
- Das Beleg-PDF enthält zwei identische Quittungshälften („Original – für Teilnehmer:in"
  oben, „Durchschrift – für Unterlagen" unten), getrennt durch eine Trennlinie mit
  Scherensymbol, jeweils im Aufbau der Papiervorlage.
- Jede Quittungshälfte trägt die Felder Beleg-Nr., Datum, Erhalten von, Betrag, „In
  Worten", „Für folgende Leistung / Kurs", den Hinweis „Kleinunternehmer gemäß § 19 UStG –
  kein gesonderter Umsatzsteuerausweis.", „Betrag dankend bar erhalten." sowie leere
  Linien für „Ort, Datum" und „Unterschrift (Kursleitung)".
- Der Betrag wird „in Worten" automatisch ausgeschrieben (z. B. „fünfundvierzig Euro und
  null Cent").
- Unterschrift und „Ort, Datum" bleiben im PDF unausgefüllt und werden handschriftlich
  ergänzt.