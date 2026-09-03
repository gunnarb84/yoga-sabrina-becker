# Veranstaltungsdetails anzeigen

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich zu einer Veranstaltung alle Details einsehen können, damit ich
entscheiden kann, ob sie zu mir passt.

## Description
Die Detailseite einer Veranstaltung zeigt Langbeschreibung, alle Termine, Preis, Ort und
Buchungsmöglichkeit. Sie ist öffentlich erreichbar.

## Akzeptanzkriterien
- Die Detailseite ist unter einem eindeutigen URL-Muster erreichbar, z. B. `/veranstaltung/{slug}`.
- Die Detailseite zeigt Titel, Typ, Langbeschreibung (`longDescription`), Bild und Preis.
- Die Detailseite listet alle zukünftigen Termine (`Session`) mit Datum, Uhrzeit, Ort und
  Hinweis.
- Die Detailseite zeigt die Anzahl freier Plätze (`freeSeats`) an.
- Ist die Veranstaltung ausgebucht, wird der Hinweis „Warteliste" angezeigt.
- Die Detailseite enthält einen Button zur Anmeldung.
- Ist die Veranstaltung nicht mehr buchbar (abgeschlossen, storniert oder in der Vergangenheit),
  ist der Anmeldebutton deaktiviert und ein Hinweis wird angezeigt.
