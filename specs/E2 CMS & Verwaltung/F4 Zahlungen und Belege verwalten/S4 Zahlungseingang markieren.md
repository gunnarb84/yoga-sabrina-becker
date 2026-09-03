# Zahlungseingang markieren

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich den Eingang einer Überweisung markieren können, damit ich
sehe, welche Rechnungen bereits beglichen sind.

## Description
Da keine Online-Zahlung erfolgt, prüft Sabrina Becker den Kontoauszug manuell und markiert
die zugehörige Rechnung in der Verwaltung als bezahlt.

## Akzeptanzkriterien
- In der Rechnungsmaske kann der Zahlungseingang für eine Rechnung markiert werden.
- Das `paidAt`-Feld der Zahlung wird auf das aktuelle Datum gesetzt.
- Der Status der Rechnung ändert sich auf „bezahlt".
- Die Markierung ist protokolliert (Person, Zeitpunkt).
- Eine einmal als bezahlt markierte Rechnung kann wieder auf „offen" gesetzt werden.
