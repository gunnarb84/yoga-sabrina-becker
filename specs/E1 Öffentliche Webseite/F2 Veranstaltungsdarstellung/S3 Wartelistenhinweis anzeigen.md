# Wartelistenhinweis anzeigen

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich erkennen, ob eine Veranstaltung ausgebucht ist und ich mich auf die
Warteliste setzen kann, damit ich keine Chance verpasse.

## Description
Wenn eine Veranstaltung die maximale Teilnehmerzahl erreicht hat, wird das System auf der
Übersichts- und Detailseite transparent kommunizieren, dass neue Anmeldungen auf die
Warteliste kommen.

## Akzeptanzkriterien
- Eine ausgebuchte Veranstaltung (`freeSeats` = 0) zeigt in der Übersicht den Hinweis
  „Warteliste".
- Auf der Detailseite wird bei ausgebuchter Veranstaltung erklärt, dass eine Anmeldung auf die
  Warteliste erfolgt.
- Der Hinweis enthält keine personenbezogenen Daten anderer Teilnehmer/innen.
- Der Hinweis „Warteliste" ist visuell vom Status „Buchbar" unterscheidbar (Farbe + Text,
  nicht Farbe allein).
- Der Hinweis verschwindet, sobald freie Plätze verfügbar sind.
