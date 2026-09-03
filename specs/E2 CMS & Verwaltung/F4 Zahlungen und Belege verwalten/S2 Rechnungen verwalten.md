# Rechnungen verwalten

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich Rechnungen einsehen und erneut versenden können, damit ich den
Überblick über Überweisungszahlungen behalte.

## Description
Rechnungen werden bei Anmeldungen mit Zahlungsart „Überweisung" automatisch erzeugt. Sie
haben eine fortlaufende, unveränderliche Nummer im Format `R-YYYY-NNNNN`.

## Akzeptanzkriterien
- Die Rechnungsliste zeigt alle `Invoice` mit Datum, Nummer, Empfänger, Betrag und Status.
- Die Rechnungsnummer ist eindeutig und lückenlos innerhalb eines Kalenderjahres.
- Eine Rechnung kann als PDF heruntergeladen werden.
- Eine Rechnung kann erneut per E-Mail an die `email` des Teilnehmers/der Teilnehmerin
  versendet werden.
- Rechnungen sind nach der Erstellung unveränderlich.
- Der Status zeigt, ob die zugehörige Zahlung als eingegangen markiert ist.
