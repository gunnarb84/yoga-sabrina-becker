# Veranstaltung veröffentlichen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich eine Veranstaltung veröffentlichen oder auf Entwurf zurücksetzen
können, damit ich steuere, was auf der Webseite sichtbar ist.

## Description
Veröffentlichte Veranstaltungen erscheinen auf der öffentlichen Webseite. Eine Rücknahme der
Veröffentlichung ist nur möglich, solange keine Anmeldungen bestehen.

## Akzeptanzkriterien
- Die Aktion „Veröffentlichen" ist in der Veranstaltungsmaske verfügbar, solange der Status
  `DRAFT` ist.
- Der Vorgang `PublishActivity` setzt den Status auf `PUBLISHED`.
- `PublishActivity` scheitert mit `activity.already_published`, wenn der Status bereits
  `PUBLISHED` ist.
- Die Aktion „Veröffentlichung zurückziehen" ist verfügbar, solange keine Anmeldungen
  (`Registration` mit Status `CONFIRMED` oder `WAITING_LIST`) existieren.
- Der Vorgang `UnpublishActivity` setzt den Status auf `DRAFT`.
- `UnpublishActivity` scheitert mit `activity.has_registrations`, wenn bereits Anmeldungen
  vorliegen.
- Nach dem Veröffentlichen ist die Veranstaltung auf der öffentlichen Webseite sichtbar.
