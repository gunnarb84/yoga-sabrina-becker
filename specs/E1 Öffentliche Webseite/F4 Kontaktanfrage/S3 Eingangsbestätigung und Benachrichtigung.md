# Eingangsbestätigung und Benachrichtigung

## Meta
- **State:** Modified

## User Story
Als Besucher möchte ich eine Bestätigung meiner Anfrage erhalten, damit ich weiß, dass sie
angekommen ist. Als Administratorin möchte ich über jede neue Anfrage per E-Mail informiert
werden, damit ich zeitnah reagieren kann.

## Description
Nach erfolgreichem Absenden versendet das System zwei E-Mails: eine Eingangsbestätigung an
die anfragende Person und eine Benachrichtigung mit den Anfragedaten an die hinterlegte
Empfängeradresse der Inhaberin. Beide werden als `OutboundMessage` protokolliert; die
lesende Nachvollziehbarkeit ist durch das Feature „Ausgehende Nachrichten" (E2 F5)
abgedeckt.

## Akzeptanzkriterien
- Nach erfolgreichem Absenden wird eine Eingangsbestätigung an die angegebene `email`
  versendet.
- Die Eingangsbestätigung enthält keine Gesundheitsinformationen.
- Nach erfolgreichem Absenden wird eine Benachrichtigung an die hinterlegte Empfängeradresse
  der Inhaberin versendet, die `name`, `email`, `phone`, `topic` und `message` enthält.
- Beide E-Mails werden als `OutboundMessage` mit Status `SENT` oder `FAILED` protokolliert.
- Bei fehlgeschlagenem Versand wird im Backend eine lesbare Fehlerinformation hinterlegt.