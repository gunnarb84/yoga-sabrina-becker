# Wiederkehrende Kurse aus Vorlage erzeugen

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich aus einer Kursvorlage automatisch Termine erzeugen können,
damit ich wiederkehrende Kurse nicht jedes Mal manuell anlegen muss.

## Description
Eine Kursvorlage (`CourseTemplate`) enthält Wochentag, Startzeit, Dauer und Anzahl der
Termine. Aus ihr wird eine neue Veranstaltung vom Typ Kurs erzeugt, die Termine werden
automatisch berechnet.

## Akzeptanzkriterien
- Im Backend können Kursvorlagen verwaltet werden (anlegen, bearbeiten, löschen).
- Eine Vorlage hat die Pflichtfelder `title`, `weekday`, `startTime`, `durationMinutes`,
  `sessionCount`, `price`, `maxParticipants`.
- Der Vorgang `GenerateSessionsFromTemplate` erzeugt eine neue Veranstaltung vom Typ
  `COURSE` mit dem Status `DRAFT`.
- Die Termine beginnen am nächstmöglichen `weekday` ab heute, zur angegebenen `startTime`.
- Jedes Terminende ergibt sich aus `startTime` + `durationMinutes`.
- Die erzeugte Veranstaltung kann vor der Veröffentlichung manuell korrigiert werden.
- Der Vorgang scheitert mit `INVALID_TEMPLATE`, wenn die Vorlage ungültige Werte enthält.
