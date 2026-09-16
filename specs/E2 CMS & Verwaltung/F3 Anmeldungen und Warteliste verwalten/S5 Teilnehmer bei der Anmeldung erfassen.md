# Teilnehmer bei der Anmeldung erfassen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich bei der Anmeldung zu einer Veranstaltung einen bisher
unbekannten Teilnehmer direkt in derselben Maske erfassen können, damit ich nicht erst zur
Teilnehmermaske wechseln und danach die Anmeldung erneut öffnen muss.

## Description
In der Maske „Teilnehmer anmelden" lässt sich neben der Auswahl aus der Teilnehmerliste ein
neuer Teilnehmer direkt erfassen. Wird die Erfassung eingeschaltet, treten an die Stelle der
Auswahl die Felder für die Erfassung; die Anmeldung wird in einem Durchgang angelegt —
Teilnehmerdatensatz (`Participant`) und Anmeldung (`Registration`) — und der neue Teilnehmer
erscheint anschließend in der Teilnehmerliste. Die Erfassung beschränkt sich auf die
Angaben, die für die Anmeldung unmittelbar gebraucht werden (Vorname, Nachname, E-Mail,
Telefon); die übrigen Stammdaten werden über „Teilnehmer bearbeiten" nachgepflegt.

## Akzeptanzkriterien
- In der Maske „Teilnehmer anmelden" ist die Schaltfläche „Neuen Teilnehmer erfassen" sichtbar.
- Nach dem Einschalten der Erfassung werden die Felder „Vorname" und „Nachname" als
  Pflichtfelder sowie „E-Mail" und „Telefon" als optionale Felder angezeigt; die
  Teilnehmerauswahl ist dann nicht sichtbar.
- Der Vorgang `CreateParticipant` legt beim Absenden einen neuen `Participant` an und der
  Vorgang `RegisterParticipant` meldet ihn mit der gewählten Zahlungsart und der Herkunft
  `Verwaltung` zur Veranstaltung an.
- Scheitert das Anlegen des Teilnehmers (z. B. E-Mail bereits vorhanden, Fehlercode
  `PARTICIPANT_EMAIL_ALREADY_EXISTS`), wird keine Anmeldung angelegt und die Meldung des
  Vorgangs wird in der Maske angezeigt.
- Nach erfolgreicher Anmeldung sind die Erfassungsfelder geleert, die Erfassung ist
  geschlossen und der neue Teilnehmer ist in der Teilnehmerliste sichtbar.