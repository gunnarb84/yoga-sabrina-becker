# Barquittung per E-Mail versenden

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich, dass Teilnehmende ihre Barquittung per E-Mail erhalten,
damit ich sie nicht mehr handschriftlich ausstellen und aushändigen muss.

## Description
Bei der Erfassung einer Barzahlung (einzeln oder massenweise) erzeugt das System die
Barquittung als PDF und versendet sie automatisch an die E-Mail-Adresse der Teilnehmerin
oder des Teilnehmers. Der Versand wird als ausgehende Nachricht protokolliert; scheitert
er, bleibt die Zahlung trotzdem bestehen.

## Akzeptanzkriterien
- Bei der Erfassung einer Barzahlung wird die Barquittung als PDF automatisch per E-Mail
  an die `email` der Teilnehmerin/des Teilnehmers versendet.
- Die E-Mail enthält das Beleg-PDF als Anhang.
- Der Versand wird als `OutboundMessage` mit dem Betreff und dem Status `Versandt`
  protokolliert.
- Scheitert der E-Mail-Versand, wird die Zahlung trotzdem erfasst und die ausgehende
  Nachricht erhält den Status `Fehlgeschlagen`.
- Eine fehlgeschlagene Nachricht kann über den Vorgang `ResendOutboundMessage` erneut
  versendet werden (siehe F5).
- Kostenlose Anmeldungen erhalten keine Barquittung und keine E-Mail.