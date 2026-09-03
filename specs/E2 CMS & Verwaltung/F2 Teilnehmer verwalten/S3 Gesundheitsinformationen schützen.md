# Gesundheitsinformationen schützen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich Gesundheitsinformationen besonders geschützt speichern, damit
ich die DSGVO-Anforderungen erfülle.

## Description
Gesundheitsinformationen sind besondere personenbezogene Daten. Sie werden nur bei expliziter
Einwilligung erfasst, nicht in allgemeinen Listen angezeigt und nicht per E-Mail versendet.

## Akzeptanzkriterien
- Das Feld `healthNotes` ist in der Teilnehmerliste nicht sichtbar.
- `healthNotes` werden in der Detailmaske erst nach einem zusätzlichen Klick/Zugriffsschritt
  angezeigt (z. B. eigenes Detail-Tab).
- `healthNotes` werden nicht in E-Mails an Teilnehmer/innen eingebettet.
- `healthNotes` werden nicht in Ausgehende-Nachrichten-Ansichten als Vorschau angezeigt.
- Das Feld `healthNotesConsent` wird gespeichert und ist jederzeit einsehbar.
- Ohne gesetzte `healthNotesConsent` kann das Feld `healthNotes` nicht gespeichert werden.
