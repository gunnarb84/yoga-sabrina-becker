# Einwilligung Foto- und Videoaufnahmen

## Meta
- **State:** Draft

## Problem
Für die Veröffentlichung von Foto- und Videoaufnahmen aus dem Yogaunterricht
liegt eine unterschriebene Papiervorlage („Einwilligungserklärung für Foto-
und Videoaufnahmen im Yogauterricht", unter `vorlagen/`) vor. Diese
Einwilligung wird im System bisher nicht erfasst: Weder die Online-Anmeldung
noch die Verwaltung bilden ab, ob eine Teilnehmer/in die Veröffentlichung von
Fotos und/oder Videos auf der Website und in Social Media erlaubt hat. Ohne
Erfassung ist keine nachweisbare Veröffentlichung möglich, und Widerrufe
erfassen nur den Papierstapel.

## Zielgruppe
Teilnehmer/innen geben die Einwilligung; Sabrina Becker als Inhaberin erfasst
und pflegt sie in der Verwaltung und ist bei Veröffentlichungen an sie
gebunden.

## Lösungsidee
Die Einwilligung wird als eigene, **freiwillige** und **widerrufliche**
Einwilligung **je Teilnehmer/in** geführt — getrennt von der
Datenschutz-Einwilligung (Idee I9) und vom vorhandenen Haken zu
Gesundheitsinformationen. Sie gilt für alle Veranstaltungen dieser
Teilnehmer/in, nicht je einzelner Anmeldung. Sie wird an zwei Stellen
erfasst:

- **Online-Anmeldeformular:** zwei getrennte, optionale Haken „Fotos" und
  „Videos"; Verwendungskanäle pauschal „Website und Social Media der
  Yoga-Angebote". Beide Haken sind freiwillig; aus einer verweigerten
  Einwilligung entstehen keine Nachteile. Wird mit dem Formular kein
  Geburtsdatum unter 18 Jahre angegeben, setzt das System beim
  Zustimmungs-Haken automatisch den Zeitpunkt. Für Minderjährige werden die
  Haken ausgeblendet und ein Hinweis auf den Papierbogen angezeigt (dort
  unterschreiben die Sorgeberechtigten).
- **Teilnehmermaske der Verwaltung:** Sabrina sieht und pflegt die
  Einwilligung je Teilnehmer/in — z. B. wenn ein unterschriebener Papierbogen
  vorliegt oder per E-Mail widerrufen wurde.

**Nachweisbarkeit:** Zu jedem der beiden Haken wird der Zeitpunkt der
Einwilligung protokolliert (Datum „Einwilligung am"). Bei der Online-Anmeldung
setzt das System ihn automatisch; in der Teilnehmermaske setzt Sabrina ihn
zusammen mit dem Haken.

**Widerruf:** Ein Widerruf (laut Vorlage per E-Mail an Sabrina, mit Wirkung
für die Zukunft) wird in der Teilnehmermaske als **eigener Vermerk**
protokolliert: Datum des Widerrufs und kurzer Text (z. B. „per E-Mail,
betrifft Fotos"). Beim Widerruf setzt Sabrina die betroffenen Haken zurück;
der Vermerk dokumentiert, wann und worauf sich der Widerruf bezog. Mehrere
Widerrufe überschreiben den Vermerk; der vollständige Ablauf bleibt in
Sabrinas E-Mail-Postfach und dem Papierbogen.

Die Kanäle Flyer/Plakate, Newsletter und Sonstiges aus der Papiervorlage
werden online nicht abgebildet; dafür bleibt der Papierbogen gültig.

## Scope
- **In Scope:** Freiwillige Foto-/Video-Einwilligung mit Zeitpunkten im
  Online-Anmeldeformular (außer für Minderjährige); Pflege, Sichtbarkeit und
  Widerrufsvermerk in der Teilnehmermaske; Speicherung je Teilnehmer/in.
- **Out of Scope:** Datenschutzerklärung und deren Haken (Idee I9); Haken zu
  Gesundheitsinformationen (bestehend); Erzeugung oder Upload der
  unterschriebenen Papiervorlage; Abbildung der Kanäle Flyer/Newsletter;
  automatischer Widerruf per E-Mail-Link; Widerrufshistorie (nur der
  jüngste Vermerk wird geführt).

## Auswirkungen auf den Bestand
- **Specs:** Betrifft die Online-Anmeldung (Epic E1, Feature Registrierung)
  und die Teilnehmerverwaltung (Epic E2) — betroffene Stories werden bei der
  Überführung festgelegt.
- **Datenstruktur:** Neue Felder am Teilnehmer: Einwilligung Fotos
  (Ja/Nein), Einwilligung Fotos am (Datum), Einwilligung Videos (Ja/Nein),
  Einwilligung Videos am (Datum), Widerrufsvermerk (Datum + Text).
- **Backend:** Freiwillige Aufnahme der Einwilligung im Vorgang
  Registrierung (Webseite, ohne Minderjährigen-Zweig); Änderung der
  Einwilligung und des Widerrufsvermerks im Vorgang Teilnehmer bearbeiten.
- **Quellcode:** Anmeldeformular (`modules/Webseite/Ui/Registration/**`),
  Teilnehmermaske (`modules/Verwaltung/Ui/Participant/**`), Vorgänge
  Registrierung und Teilnehmer bearbeiten, Migration am Teilnehmer.

## Entscheidungen
- 20.09.2026: Idee angelegt, Nummer I10 reserviert.
- 20.09.2026: Als eigene Idee geführt, getrennt von I9 (Datenschutz) —
  anderes Problem (Öffentlichkeit statt Rechtmäßigkeit der Verarbeitung),
  andere Datenstruktur (differenzierte, widerrufliche Einwilligung).
- 20.09.2026: Erfassung online **und** in der Verwaltung (Option a) — die
  Online-Anmeldung fängt die Einwilligung ein, die Teilnehmermaske bildet den
  Stand inklusive Papierbogen und Widerruf ab.
- 20.09.2026: Zwei getrennte Haken „Fotos" und „Videos" mit pauschalem Kanal
  „Website und Social Media" (Option a) — deckt den Hauptfall ab und bleibt
  am Smartphone verständlich; Flyer/Newsletter bleiben dem Papierbogen
  vorbehalten.
- 20.09.2026: Zeitpunkt der Einwilligung wird protokolliert (Option a) —
  Nachweisbarkeit nach Art. 6 Abs. 1 lit. a DSGVO; je Haken ein Datum.
- 20.09.2026: Keine Online-Einwilligung für Minderjährige (Option a) — die
  Vorlage verlangt die Unterschrift der Sorgeberechtigten; die Haken werden
  bei Geburtsdatum unter 18 Jahren ausgeblendet, es gilt der Papierbogen.
- 20.09.2026: Widerruf wird als eigener Vermerk protokolliert (Option b) —
  Datum und kurzer Text in der Teilnehmermaske, betroffene Haken werden
  zurückgesetzt; keine Widerrufshistorie, der Ablauf bleibt im Postfach.

## Offene Punkte
- (leer — Stakeholder-Bestätigung der finalen Fassung steht aus)