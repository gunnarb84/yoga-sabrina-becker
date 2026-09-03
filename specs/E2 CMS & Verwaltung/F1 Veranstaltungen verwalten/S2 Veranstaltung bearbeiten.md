# Veranstaltung bearbeiten

## Meta
- **State:** Implemented

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
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `activity.not_found`, wenn die
  Veranstaltung nicht existiert.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `activity.already_completed`, wenn der
  Status `COMPLETED` ist.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `activity.already_cancelled`, wenn der
  Status `CANCELLED` ist.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `activity.title_empty`, wenn der
  Titel leer ist.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `activity.price_negative`, wenn der
  Preis negativ ist.
- Der Vorgang `UpdateActivity` scheitert mit dem Fehlercode `activity.max_participants_too_low`,
  wenn die maximale Teilnehmerzahl kleiner als 1 ist.
- Nach dem Speichern wird die Liste aktualisiert.
