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

## Lösungsidee
Vor dem Absenden des Anmeldeformulars bestätigt die anmeldende Person die
Datenverarbeitung mit einem Pflicht-Haken; der zugehörige Text verweist auf
die Datenschutzerklärung der Webseite. Zweiter Punkt aus der Anforderungsnotiz:
„Datenschutz von Vorlage übernehmen" — wie die Datenschutzerklärung selbst
entsteht bzw. verwaltet wird (CMS-Seite, Vorlage, Verweis in der Anmeldung),
ist im Dialog zu klären.

## Scope
- **In Scope:** Einwilligungs-Haken als Pflichtangabe im Online-Anmeldeformular
  mit Verweis auf die Datenschutzerklärung; Klärung des Vorlagen-Mechanismus.
- **Out of Scope:** Noch offen — ergibt sich aus dem Dialog.

## Auswirkungen auf den Bestand
- **Specs:** Betrifft die Online-Anmeldung der Webseite (Epic E1, Feature
  Registrierung) — betroffene Stories und ein ggf. neues Kriterium werden im
  Dialog festgelegt.
- **Datenstruktur:** Möglicherweise ein Feld zur Protokollierung des
  Einverständnisses (Zeitpunkt) an der Anmeldung — im Dialog klären.
- **Backend:** Mögliche Pflichtprüfung des Einverständnisses im Vorgang
  Registrierung — im Dialog klären.
- **Quellcode:** Anmeldeformular der Webseite
  (`modules/Webseite/Ui/**`), ggf. Vorgang in `modules/Webseite/Application/**`.

## Entscheidungen
- 19.09.2026: Idee angelegt, Nummer I9 reserviert.
- 19.09.2026: Als eigene Idee geführt, nicht als Zusatzkriterium in einer
  bestehenden Story — der Einwilligungs-Haken ist eine Pflichtangabe mit
  Protokollierungsfrage, „Datenschutz von Vorlage übernehmen" ist ein
  eigener Mechanismus.

## Offene Punkte
- Dialog noch nicht geführt. Erste Fragen:
  - Ist mit „Datenschutz von Vorlage übernehmen" gemeint, dass die
    Datenschutzerklärung als CMS-Seite aus einer Textvorlage erzeugt wird,
    oder dass die Anmeldung nur auf die bestehende Datenschutzseite verweist?
  - Muss das Einverständnis nachweisbar protokolliert werden (Zeitpunkt an
    der Anmeldung), oder reicht der Haken im Formular?
  - Gilt der Haken auch für das Kontaktformular oder nur für die Anmeldung?