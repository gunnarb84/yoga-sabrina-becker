# Anfragen in der Verwaltung bearbeiten

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich alle Kontaktanfragen einsehen und bearbeiten können, damit
ich den Überblick über offene Anfragen behalte und keine verloren geht.

## Description
Alle Anfragen erscheinen in einer Liste in der Verwaltung. Jede Anfrage wird in einer
Detailansicht gelesen, mit Notiz versehen und über den Status bearbeitet (Neu →
In Bearbeitung → Erledigt). Die Klärung und Antwort an die anfragende Person läuft
außerhalb des Systems. Anfragen werden nicht gelöscht.

## Akzeptanzkriterien
- Der Menüpunkt „Kontaktanfragen" ist unter der Modulgruppe „Verwaltung" erreichbar.
- Die Liste zeigt `receivedAt`, `name`, `email`, `topic` und `status`.
- Die Liste lässt sich nach `status` filtern.
- Klick auf eine Anfrage öffnet die Detailansicht mit allen Feldern und der `note`.
- Die `note` wird persistiert.
- Der Status kann in der Detailansicht auf jeden der drei Werte (`Neu`, `In Bearbeitung`,
  `Erledigt`) geändert werden.
- Anfragen werden nicht gelöscht; die Liste zeigt erledigte Anfragen weiterhin an.