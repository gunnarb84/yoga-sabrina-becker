# Anmeldung absenden

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich meine Anmeldung absenden können, damit ich einen festen Platz oder
einen Wartelistenplatz erhalte.

## Description
Beim Absenden des Formulars wird die Anmeldung im Backend gespeichert. Bereits bekannte
Teilnehmer/innen (Erkennung über `email`) werden nicht neu angelegt, sondern deren
Stammdaten aktualisiert. Je nach freien Plätzen erhält die Anmeldung den Status „Bestätigt"
oder „Warteliste".

## Akzeptanzkriterien
- Das Absenden ist nur möglich, wenn alle Pflichtfelder gültig ausgefüllt sind.
- Bei einer bereits bekannten `email` wird der bestehende Teilnehmerdatensatz (`Participant`)
  aktualisiert, es wird kein neuer Datensatz angelegt.
- Bei einer neuen `email` wird ein neuer `Participant` angelegt.
- Ist ein freier Platz verfügbar, wird die Anmeldung (`Registration`) mit dem Status
  `CONFIRMED` angelegt.
- Ist die Veranstaltung ausgebucht, wird die Anmeldung mit dem Status `WAITING_LIST` angelegt
  und auf der Warteliste (`WaitingList`) mit dem nächsten Rang eingetragen.
- Bei der Zahlungsart „Überweisung" wird automatisch eine Rechnung (`Invoice`) erzeugt.
- Bei der Zahlungsart „Kostenlos" wird keine Zahlung und kein Beleg erzeugt.
- Nach erfolgreicher Anmeldung wird der Bestätigungsbildschirm angezeigt.
- Der Bestätigungsbildschirm zeigt den Anmeldestatus (Bestätigt/Warteliste) und den Preis an.
