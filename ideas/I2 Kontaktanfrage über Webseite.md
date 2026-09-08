# Kontaktanfrage über Webseite

## Meta
- **State:** Übernommen

## Problem
Die Kontaktseite (/kontakt) bietet derzeit keinen schriftlichen Anfrageweg auf der
Webseite selbst — Besucher/innen werden auf E-Mail verwiesen. Sabrina Becker verpasst
damit Anfragen, die spontan über die Webseite entstehen, und hat keine strukturierte
Übersicht offener Anfragen; alles läuft über ihr privates Postfach.

## Lösungsidee
Ein Kontaktformular auf der Seite /kontakt im Stil der Entwurfs-Maske (weiße
Formularkarte neben der dunklen Kontaktkarte). Die Anfrage wird als
„Kontaktanfrage" (Kontaktanfrage) gespeichert und zusätzlich per E-Mail an
info@yoga-sabrinabecker.de gemeldet. Die Bearbeitung (Klärung, Antwort) erfolgt
außerhalb des Systems in der Verwaltung; dort sieht Sabrina alle Anfragen mit
Status.

## Scope
- **In Scope:**
  - Formular auf /kontakt (Ersatz für den bisherigen E-Mail-Hinweis-Platzhalter)
    mit Feldern Name, E-Mail (Pflicht), Telefon (optional), Anlass/Gruppe
    (optional), Nachricht (Pflicht).
  - Speichern der Anfrage als Kontaktanfrage mit Status Neu → In Bearbeitung →
    Erledigt (kein Löschen).
  - E-Mail-Benachrichtigung an Sabrina bei jeder neuen Anfrage
    (als ausgehende Nachricht protokolliert).
  - Eingangsbestätigung per E-Mail an die anfragende Person.
  - Spam-Schutz ohne externen Dienst: Honeypot-Feld und Ratenbegrenzung.
  - Anfragenliste in der Verwaltung mit Statuswechsel und Notiz.
- **Out of Scope:**
  - Antwort an die anfragende Person aus der Verwaltung heraus (Klärung läuft
    außerhalb des Systems, z. B. telefonisch/per persönlichem E-Mail-Konto).
  - Zuordnung der Anfrage zu Veranstaltungen/Anmeldungen.
  - Gesundheitsinformationen — das Formular darf keine aufnehmen.
  - Externe Spam-Dienste (reCAPTCHA & Co.), Newsletter-Anmeldung.

## Auswirkungen auf den Bestand
- **Specs:** Neues Feature `E1/F4 Kontaktanfrage` mit Stories S1 (Formular
  anzeigen), S2 (Anfrage absenden), S3 (Eingangsbestätigung und Benachrichtigung),
  S4 (Anfragen in der Verwaltung bearbeiten).
- **Datenstruktur:** Neue Tabelle `webseite_kontaktanfragen` (Status, Name, E-Mail,
  Telefon, Anlass, Nachricht, Notiz, Zeitstempel).
- **Backend:** Neuer schreibender Vorgang in E1 (Kontaktanfrage aufnehmen) samt
  Prüfung (Pflichtfelder, E-Mail-Format, Honeypot, Ratenbegrenzung); Versand der
  beiden E-Mails als ausgehende Nachrichten.
- **Quellcode:** Neues Livewire-Formular auf /kontakt; neue Verwaltungsliste
  mit Detailmaske (Statuswechsel, Notiz); Datenschutzerklärung um die
  Verarbeitung der Anfragedaten ergänzen.

## Entscheidungen
- 08.09.2026: Idee angelegt, Nummer I2 reserviert.
- 08.09.2026: Variante (B) gewählt — Anfrage speichern + E-Mail-Benachrichtigung,
  Klärung in der Verwaltung (statt reinem E-Mail-Versand ohne Ablage).
- 08.09.2026: Eingangsbestätigung an die anfragende Person wird verschickt.
- 08.09.2026: Feldumfang festgelegt: Name, E-Mail (Pflicht), Telefon (optional),
  Anlass/Gruppe (optional), Nachricht (Pflicht).
- 08.09.2026: Spam-Schutz mit eigenen Mitteln (Honeypot + Ratenbegrenzung),
  kein externer Dienst.
- 08.09.2026: Statusmodell Neu → In Bearbeitung → Erledigt, ohne Löschen.
- 08.09.2026: Stakeholder hat die Idee als „ready" bestätigt; bestehendes
  GitHub-Repository wird weitergenutzt.

## Offene Punkte
- Keine — die Idee ist als Spec umgesetzt:
  - `specs/E1 Öffentliche Webseite/F4 Kontaktanfrage/S1 Anfrageformular anzeigen.md`
  - `specs/E1 Öffentliche Webseite/F4 Kontaktanfrage/S2 Anfrage absenden.md`
  - `specs/E1 Öffentliche Webseite/F4 Kontaktanfrage/S3 Eingangsbestätigung und Benachrichtigung.md`
  - `specs/E1 Öffentliche Webseite/F4 Kontaktanfrage/S4 Anfragen in der Verwaltung bearbeiten.md`