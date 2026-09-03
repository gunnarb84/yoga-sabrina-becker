# Anmeldebestätigung per E-Mail

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich nach meiner Anmeldung eine E-Mail-Bestätigung erhalten, damit ich
meine Anmeldung und gegebenenfalls die Rechnung dokumentiert habe.

## Description
Nach erfolgreicher Anmeldung versendet das System eine E-Mail an die angegebene `email`.
Bei Zahlung per Überweisung enthält die E-Mail die Rechnung als PDF-Anhang. Die E-Mail wird
als ausgehende Nachricht (`OutboundMessage`) protokolliert.

## Akzeptanzkriterien
- Nach erfolgreicher Anmeldung wird eine E-Mail an die angegebene `email` versendet.
- Die E-Mail enthält Titel der Veranstaltung, Anmeldestatus (Bestätigt/Warteliste), Preis
  und Zahlungsart.
- Bei Zahlungsart „Überweisung" enthält die E-Mail die Rechnung (`Invoice`) als PDF-Anhang.
- Die versendete E-Mail wird als `OutboundMessage` mit Status `SENT` oder `FAILED`
  protokolliert.
- Bei fehlgeschlagenem Versand wird im Backend eine lesbare Fehlerinformation hinterlegt.
- Die E-Mail enthält keine Gesundheitsinformationen.
