# Nachricht erneut senden

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich eine fehlgeschlagene oder ausstehende Nachricht erneut
senden können, damit der Empfänger doch noch erreicht wird.

## Description
Für E-Mails mit Status `FAILED` oder `PENDING` gibt es eine Wiederholungsfunktion. Der
erneute Versand wird ebenfalls protokolliert.

## Akzeptanzkriterien
- Die Aktion „Erneut senden" ist für Nachrichten mit Status `FAILED` oder `PENDING`
  verfügbar.
- Der Vorgang `ResendOutboundMessage` versucht den Versand erneut.
- `ResendOutboundMessage` scheitert mit `MESSAGE_NOT_FOUND`, wenn die Nachricht nicht
  existiert.
- Nach dem erneuten Versand wird ein neuer `OutboundMessage`-Eintrag mit aktuellem Zeitpunkt
  angelegt.
- Der ursprüngliche Eintrag bleibt unverändert; die Historie ist nachvollziehbar.
