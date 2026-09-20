# Datenschutzerklärung in der Online-Anmeldung

## Meta
- **State:** Draft

## Problem
Das Anmeldeformular der öffentlichen Webseite nimmt Gesundheitsinformationen
und Kontaktdaten von Teilnehmenden an, ohne dass ein Einverständnis zur
Datenverarbeitung eingeholt wird. Für den Betrieb besteht eine
Aufbewahrungs- und Informationspflicht: Die anmeldende Person muss vor dem
Absenden erkennen, welche Daten zu welchem Zweck verarbeitet werden und wo
die Datenschutzerklärung lesbar ist.

## Zielgruppe
Anmeldende Personen auf der öffentlichen Webseite; Sabrina Becker als
Verantwortliche für die Datenverarbeitung.

## Lösungsidee
Vor dem Absenden des Anmeldeformulars bestätigt die anmeldende Person die
Datenverarbeitung mit einem **Pflicht-Haken**. Der Haken-Text verweist mit
einem Link auf die bestehende Datenschutzerklärung unter `/datenschutz`.
Ohne gesetzten Haken lässt sich das Formular nicht absenden.

Die Datenschutzerklärung selbst bleibt, wie sie ist: eine CMS-Seite
(`/datenschutz`, in der Verwaltung pflegbar), deren Inhalt aus dem
`LegalPagesSeeder` stammt und auf der Vorlage `vorlagen/datenschutz.md`
beruht. **Kein neuer Vorlagen-Mechanismus** — die Anmeldung verweist nur auf
die bestehende Seite.

**Nachweisbarkeit:** Das System speichert an der Anmeldung, ob die
Einwilligung erteilt wurde und zu welchem Zeitpunkt („Datenschutz-Einwilligung
am", Datum und Uhrzeit) — automatisch beim Absenden, keine manuelle Erfassung.

## Scope
- **In Scope:** Pflicht-Haken mit Verweis auf `/datenschutz` im
  Online-Anmeldeformular; Speicherung von Einwilligung (Ja/Nein) und
  Zeitpunkt an der Anmeldung.
- **Out of Scope:** Inhalt und Pflege der Datenschutzerklärung selbst
  (bestehende CMS-Seite); Kontaktformular (bleibt beim bestehenden Link
  neben der Versandaktion); Verwaltungserfassung des Einwilligungszeitpunkts
  (geschieht systemseitig); Haken zu Gesundheitsinformationen (bestehend).

## Auswirkungen auf den Bestand
- **Specs:** Betrifft die Online-Anmeldung (Epic E1, Feature Registrierung,
  Stories zum Anmeldeformular und -absenden) — ein neues Pflichtkriterium und
  Anpassung der Formular-Story.
- **Datenstruktur:** Neue Felder an der Anmeldung:
  Datenschutz-Einwilligung (Ja/Nein) und Datenschutz-Einwilligung am
  (Datum/Zeit).
- **Backend:** Pflichtprüfung der Einwilligung im Vorgang Registrierung
  (Webseite) — ohne Haken wird die Anmeldung nicht angenommen.
- **Quellcode:** Anmeldeformular (`modules/Webseite/Ui/Registration/**`),
  Vorgang Registrierung (`modules/Webseite/Application/**`), Migration an der
  Anmeldung.

## Entscheidungen
- 19.09.2026: Idee angelegt, Nummer I9 reserviert.
- 19.09.2026: Als eigene Idee geführt, nicht als Zusatzkriterium in einer
  bestehenden Story — der Einwilligungs-Haken ist eine Pflichtangabe mit
  Protokollierungsfrage, „Datenschutz von Vorlage übernehmen" ist ein
  eigener Mechanismus.
- 20.09.2026: Kein neuer Vorlagen-Mechanismus — die Datenschutzerklärung
  existiert bereits als CMS-Seite `/datenschutz` (aus dem `LegalPagesSeeder`,
  Vorlage `vorlagen/datenschutz.md`); die Anmeldung verweist nur darauf.
- 20.09.2026: Einwilligung wird mit Zeitpunkt protokolliert (Option a) —
  Ja/Nein und Datum/Zeit an der Anmeldung, systemseitig beim Absenden gesetzt.
- 20.09.2026: Geltungsbereich nur das Anmeldeformular (Option a) — das
  Kontaktformular ist mit dem bestehenden Link und seinen Kriterien
  (E1/F4/S1) abgedeckt; keine Änderung dort.

## Offene Punkte
- (leer — Stakeholder-Bestätigung der finalen Fassung steht aus)