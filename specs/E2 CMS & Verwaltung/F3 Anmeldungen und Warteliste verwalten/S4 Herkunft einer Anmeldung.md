# Herkunft einer Anmeldung

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich in den Anmeldungen sehen, ob sie über die Webseite oder in
der Verwaltung entstanden sind, damit ich Laufkunden-Anmeldungen sofort von
Web-Anmeldungen unterscheiden kann.

## Description
Jede Anmeldung trägt die Herkunft (`source`): Anmeldungen aus dem Online-Anmeldeprozess der
Webseite erhalten `Webseite`, Anmeldungen, die über die Verwaltung angelegt werden
(z. B. Bareinnahme ohne Anmeldung, siehe F4-S8), erhalten `Verwaltung`. Die
Anmeldungslisten weisen die Herkunft aus.

## Akzeptanzkriterien
- Jede Anmeldung trägt die Herkunft `source` mit dem Wert `Webseite` oder `Verwaltung`.
- Anmeldungen aus dem Online-Anmeldeprozess erhalten die Herkunft `Webseite`.
- Anmeldungen, die über die Verwaltung angelegt werden, erhalten die Herkunft `Verwaltung`.
- Die Liste aller Anmeldungen und die Veranstaltungs-Anmeldeliste zeigen die Spalte
  „Herkunft" mit dem Wert der Anmeldung.