# Veranstaltung bearbeiten

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich eine bestehende Veranstaltung bearbeiten können, damit ich
Details oder Preise nachträglich korrigieren kann.

## Description
Die Veranstaltungsmaske ermöglicht das Bearbeiten aller fachlichen Felder. Änderungen werden
über den Vorgang `UpdateActivity` persistiert. Eine Änderung ist nur möglich, solange die
Veranstaltung nicht abgeschlossen oder storniert ist.

## Akzeptanzkriterien
- Die ausgewählte Veranstaltung wird in der Maske angezeigt.
- Alle Felder aus S1 können bearbeitet werden.
- Ungespeicherte Änderungen werden im Tab markiert.
- Der Vorgang `UpdateActivity` speichert die Änderungen.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `NOT_FOUND`, wenn die
  Veranstaltung nicht existiert.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `ALREADY_COMPLETED`, wenn der
  Status `COMPLETED` ist.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `ALREADY_CANCELLED`, wenn der
  Status `CANCELLED` ist.
- Nach dem Speichern wird die Liste aktualisiert.
