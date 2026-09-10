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
erreichbar. Jeder Beleg kann als PDF heruntergeladen werden — die Vorlage folgt der
Rechnungs-PDF mit den Belegangaben.

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
- Das Beleg-PDF nennt die Belegnummer im Format `B-YYYY-NNNNN` sowie Ausstellungsdatum,
  Empfänger und Betrag.