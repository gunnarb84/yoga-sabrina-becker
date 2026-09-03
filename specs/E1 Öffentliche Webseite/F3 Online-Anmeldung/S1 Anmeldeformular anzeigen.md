# Anmeldeformular anzeigen

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich mich für eine Veranstaltung über ein Formular anmelden können, damit
ich meine Daten bequem übermitteln kann.

## Description
Das Anmeldeformular ist auf der Detailseite einer Veranstaltung erreichbar. Es fragt die
für die Anmeldung erforderlichen Daten ab, ohne dass ein Benutzer-Account nötig ist.

## Akzeptanzkriterien
- Das Formular ist von der Veranstaltungsdetailseite aus erreichbar.
- Das Formular zeigt Titel und nächsten Termin der Veranstaltung an.
- Das Formular enthält die Pflichtfelder `firstName`, `lastName` und `email`.
- Das Formular enthält die optionalen Felder `addressLine1`, `addressLine2`, `postalCode`,
  `city`, `phone` und `dateOfBirth`.
- Das Formular fragt die `paymentMethod` ab: „Bar", „Überweisung" oder „Kostenlos".
- Bei Preis 0 (`price` = 0) ist die Zahlungsauswahl ausgeblendet und der Wert „Kostenlos"
  vorausgewählt.
- Das Formular bietet optional die Eingabe von `healthNotes` mit einer expliziten
  EinwilligungsCheckbox (`healthNotesConsent`).
- Sind `healthNotes` ausgefüllt, ohne dass die Einwilligung gesetzt ist, wird das Formular
  nicht übermittelt und der Hinweis auf die erforderliche Zustimmung angezeigt.
