# Datenstruktur — E2 CMS & Verwaltung

Fachliche Gegenstände und Felder des Epics. Technische Schlüssel, Pflichtspalten, Indizes und
Migrationen regelt `_data.md` und gehören nicht hierher.

## Veranstaltung (`Activity`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `type` | Typ (Kurs/Event/Workshop) | Aufzählung | ja |
| `title` | Titel | Text | ja |
| `shortDescription` | Kurzbeschreibung | Text | nein |
| `longDescription` | Langbeschreibung | formatierter Text | nein |
| `price` | Preis | Geld | ja |
| `maxParticipants` | Maximale Teilnehmerzahl | Ganzzahl | ja |
| `isPublished` | Veröffentlicht | Ja/Nein | ja |
| `image` | Bild | Medienreferenz | nein |
| `status` | Status (Entwurf/Veröffentlicht/Abgeschlossen/Storniert) | Aufzählung | ja |

## Termin (`Session`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `activity` | Veranstaltung | Referenz | ja |
| `startsAt` | Beginn | Zeitpunkt | ja |
| `endsAt` | Ende | Zeitpunkt | ja |
| `location` | Ort | Text | nein |
| `note` | Hinweis | Text | nein |

## Vorlage (`CourseTemplate`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `title` | Titel | Text | ja |
| `shortDescription` | Kurzbeschreibung | Text | nein |
| `longDescription` | Langbeschreibung | formatierter Text | nein |
| `price` | Preis | Geld | ja |
| `maxParticipants` | Maximale Teilnehmerzahl | Ganzzahl | ja |
| `weekday` | Wochentag | Aufzählung | ja |
| `startTime` | Startzeit | Uhrzeit | ja |
| `durationMinutes` | Dauer in Minuten | Ganzzahl | ja |
| `sessionCount` | Anzahl Termine | Ganzzahl | ja |

## Teilnehmer/in (`Participant`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `email` | E-Mail-Adresse | E-Mail | ja |
| `firstName` | Vorname | Text | ja |
| `lastName` | Nachname | Text | ja |
| `addressLine1` | Adresszeile 1 | Text | nein |
| `addressLine2` | Adresszeile 2 | Text | nein |
| `postalCode` | Postleitzahl | Text | nein |
| `city` | Stadt | Text | nein |
| `phone` | Telefonnummer | Text | nein |
| `dateOfBirth` | Geburtsdatum | Datum | nein |
| `healthNotes` | Gesundheitsinformationen | Text | nein |
| `healthNotesConsent` | Einwilligung Gesundheitsdaten | Ja/Nein | ja, wenn `healthNotes` gesetzt |

## Anmeldung (`Registration`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `activity` | Veranstaltung | Referenz | ja |
| `participant` | Teilnehmer/in | Referenz | ja |
| `registeredAt` | Anmeldezeitpunkt | Zeitpunkt | ja |
| `status` | Status (Bestätigt/Warteliste/Storniert) | Aufzählung | ja |
| `paymentMethod` | Zahlungsart (Bar/Überweisung/Kostenlos) | Aufzählung | ja |
| `payment` | Zahlung | Referenz | nein |

## Warteliste (`WaitingList`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `registration` | Anmeldung | Referenz | ja |
| `rank` | Rang | Ganzzahl | ja |
| `promotedAt` | Nachgerückt am | Zeitpunkt | nein |

## Zahlung (`Payment`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `registration` | Anmeldung | Referenz | ja |
| `method` | Zahlungsart (Bar/Überweisung) | Aufzählung | ja |
| `amount` | Betrag | Geld | ja |
| `paidAt` | Zahlungszeitpunkt | Zeitpunkt | nein |
| `document` | Beleg (Rechnung/Bareinnahmenbeleg) | Referenz | nein |

## Rechnung (`Invoice`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `number` | Rechnungsnummer | Text | ja |
| `payment` | Zahlung | Referenz | ja |
| `issuedAt` | Ausgestellt am | Zeitpunkt | ja |
| `recipient` | Empfänger | Text | ja |
| `amount` | Betrag | Geld | ja |

## Bareinnahmenbeleg (`CashReceipt`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `number` | Belegnummer | Text | ja |
| `payment` | Zahlung | Referenz | ja |
| `issuedAt` | Ausgestellt am | Zeitpunkt | ja |
| `recipient` | Empfänger | Text | ja |
| `amount` | Betrag | Geld | ja |

## Gutschrift (`CreditNote`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `number` | Gutschriftsnummer | Text | ja |
| `invoice` | Rechnung | Referenz | ja |
| `issuedAt` | Ausgestellt am | Zeitpunkt | ja |
| `recipient` | Empfänger | Text | ja |
| `amount` | Betrag | Geld | ja |

## Rückgabebestätigung (`CashReturn`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `number` | Belegnummer | Text | ja |
| `cashReceipt` | Bareinnahmenbeleg | Referenz | ja |
| `issuedAt` | Ausgestellt am | Zeitpunkt | ja |
| `recipient` | Empfänger | Text | ja |
| `amount` | Betrag | Geld | ja |

## Ausgehende Nachricht (`OutboundMessage`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `recipient` | Empfänger | E-Mail | ja |
| `subject` | Betreff | Text | ja |
| `body` | Inhalt | Text | ja |
| `sentAt` | Versendet am | Zeitpunkt | nein |
| `status` | Status (Versandt/Fehlgeschlagen/Ausstehend) | Aufzählung | ja |
| `registration` | Anmeldung | Referenz | nein |
