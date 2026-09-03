# Nachrichtendetail anzeigen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich den Inhalt und den Kontext einer versendeten E-Mail einsehen
können, damit ich sie gezielt nachbearbeiten oder dem Support weitergeben kann.

## Description
Die Detailansicht zeigt den vollständigen Inhalt der E-Mail, den zugehörigen Vorgang und
Sprungmarken zur Anmeldung oder Veranstaltung.

## Akzeptanzkriterien
- Die Detailansicht zeigt `recipient`, `subject`, `body`, `status`, `sentAt` und
ggf. Fehlermeldung.
- Bei einer an eine Anmeldung geknüpften Nachricht gibt es einen Sprung zur Anmeldung und zur
  Veranstaltung.
- Bei fehlgeschlagenem Versand wird der Grund angezeigt.
- Gesundheitsinformationen werden in der Nachrichtenvorschau nicht angezeigt.
