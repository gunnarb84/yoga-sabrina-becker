# Teilnehmer bearbeiten

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich Teilnehmerdaten bearbeiten können, damit ich falsche oder
veraltete Daten korrigieren kann.

## Description
Die Detailmaske eines Teilnehmers/einer Teilnehmerin zeigt alle Stammdaten. Bearbeitungen
werden persistiert und wirken sich auf zukünftige Anmeldungen und Belege aus; bereits
ausgestellte Belege bleiben unverändert.

## Akzeptanzkriterien
- Die Maske zeigt alle Felder aus `_Datenstruktur.md` für `Participant`.
- Die Felder `firstName`, `lastName` und `email` sind Pflichtfelder.
- Die `email` muss ein gültiges E-Mail-Format haben.
- Die Adressfelder, Telefonnummer, Geburtsdatum und Gesundheitsinformationen sind optional.
- `healthNotes` dürfen nur gespeichert werden, wenn `healthNotesConsent` gesetzt ist.
- Bei Änderung der `email` wird geprüft, ob die neue Adresse bereits einem anderen
  Teilnehmer/einer anderen Teilnehmerin gehört. Bei Konflikt zeigt die Maske die Meldung des
  Vorgangs an.
- Die Historie der Anmeldungen des Teilnehmers/der Teilnehmerin ist in der Maske sichtbar.
