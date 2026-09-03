# Termine erfassen

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich zu einer Veranstaltung einzelne Termine erfassen können, damit
Teilnehmer/innen wissen, wann die Veranstaltungen stattfinden.

## Description
Einer Veranstaltung können mehrere Termine (`Session`) hinzugefügt werden. Ein Termin hat
Beginn, Ende, optional Ort und Hinweis. Die Termine sind in der Veranstaltungsmaske in einer
Unterliste sichtbar.

## Akzeptanzkriterien
- In der Veranstaltungsmaske können Termine hinzugefügt, bearbeitet und entfernt werden.
- Jeder Termin hat die Pflichtfelder `startsAt` und `endsAt`.
- `endsAt` muss nach `startsAt` liegen.
- `location` und `note` sind optional.
- Termine werden mit der Veranstaltung gespeichert.
- Termine in der Vergangenheit werden in der Liste als vergangen gekennzeichnet.
- Eine Veranstaltung ohne zukünftige Termine wird auf der Webseite nicht angezeigt.
