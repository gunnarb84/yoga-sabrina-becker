# Anfrage absenden

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich meine Anfrage absenden können, damit sie bei der Inhaberin ankommt
und nicht in einem privaten Postfach verloren geht.

## Description
Beim Absenden wird die Anfrage als `ContactInquiry` gespeichert, damit sie in der Verwaltung
gesehen und bearbeitet werden kann. Der Vorgang ist gegen Spam geschützt: verstecktes Feld
und Ratenbegrenzung, ohne externen Dienst.

## Akzeptanzkriterien
- Das Absenden ist nur möglich, wenn alle Pflichtfelder gültig ausgefüllt sind.
- Der Vorgang prüft das Format von `email`; bei ungültigem Format scheitert er mit dem
  Fehlercode `contact_inquiry.invalid_email` und die Maske zeigt die Meldung an.
- Die gespeicherte Anfrage trägt den Status `Neu`.
- Eine Anfrage, bei der das versteckte Spamschutz-Feld ausgefüllt ist, wird nicht
  persistiert und nicht per E-Mail versendet; die Oberfläche zeigt dennoch die
  Erfolgsmeldung an.
- Der Vorgang erlaubt höchstens 5 Anfragen innerhalb von 10 Minuten je IP-Adresse;
  darüber hinaus scheitert er mit dem Fehlercode `contact_inquiry.rate_limited` und die
  Maske zeigt die Meldung an.
- Nach erfolgreichem Absenden erscheint eine Bestätigungsmeldung mit dem Hinweis auf die
  Eingangsbestätigung per E-Mail.