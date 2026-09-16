# Kennzahlen anzeigen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich auf dem Dashboard die Kennzahlen meines Geschäfts sehen,
damit ich den Verlauf und die Barkasse im Blick behalte.

## Description
Das Dashboard zeigt unter „Kennzahlen" drei Kennzahlen: die im laufenden Monat
entstandenen Anmeldungen (ohne stornierte), die Bareinnahmen des laufenden Monats
(Summe der Bareinnahmenbelege) und den aktuellen Kassenbestand über alle Zeiten.
Die Kacheln sind rein informativ und tragen keine Aktion.

## Akzeptanzkriterien
- Das Dashboard zeigt die Anzahl der Anmeldungen, die im laufenden Monat entstanden
  sind und nicht storniert sind.
- Das Dashboard zeigt die Summe der Beträge der Bareinnahmenbelege, die im laufenden
  Monat ausgestellt wurden, mit Währungsangabe.
- Das Dashboard zeigt den Kassenbestand: Summe aller Bareinnahmenbelege abzüglich
  aller Rückgabebestätigungen und Barentnahmen, ohne Zeitbegrenzung.
- Die Abfrage `ListDashboardMetrics` liefert die drei Kennzahlen.

## Siehe auch
- [S1 Nächste Termine anzeigen](S1%20N%C3%A4chste%20Termine%20anzeigen.md)