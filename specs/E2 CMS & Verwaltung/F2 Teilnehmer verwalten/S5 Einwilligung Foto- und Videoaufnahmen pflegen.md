# Einwilligung Foto- und Videoaufnahmen pflegen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich die Foto- und Video-Einwilligung einer
Teilnehmerin/eines Teilnehmers in der Teilnehmermaske sehen und pflegen, damit
Veröffentlichungen nur mit gültiger Einwilligung erfolgen und Widerrufe
nachvollziehbar sind.

## Description
Die Teilnehmermaske zeigt die freiwillige Einwilligung zu Foto- und
Videoaufnahmen (Verwendung auf Website und Social Media der Yoga-Angebote)
mitsamt den Zeitpunkten der Erteilung. Die Einwilligung kann hier auch für
Minderjährige erfasst werden, wenn der unterschriebene Papierbogen vorliegt.
Ein Widerruf (z. B. per E-Mail eingegangen) wird als Widerrufsvermerk mit
Datum und Text erfasst; die betroffenen Einwilligungen werden zurückgesetzt.
Grundlage: Idee
[I10](../../../ideas/I10%20Einwilligung%20Foto-%20und%20Videoaufnahmen.md).

## Akzeptanzkriterien
- Die Maske zeigt die Einwilligung Fotos (`photoConsent`), den Zeitpunkt
  `photoConsentAt`, die Einwilligung Videos (`videoConsent`) und den
  Zeitpunkt `videoConsentAt` an.
- Die Maske erlaubt das Setzen und Zurücksetzen von `photoConsent` und
  `videoConsent`; beim Setzen wird der jeweilige Zeitpunkt mit dem erfassten
  Datum gespeichert, beim Zurücksetzen wird er geleert.
- Die Einwilligungen sind freiwillig: Die Speicherung der Maske gelingt auch
  ohne gesetzte Einwilligungen.
- Die Maske erlaubt das Erfassen des Widerrufsvermerks aus `revocationAt`
  (Datum) und `revocationNote` (Text); beide Felder sind optional.
- Die Foto- und Video-Einwilligung ist unabhängig vom Geburtsdatum erfassbar
  (auch für Minderjährige mit unterschriebenem Papierbogen).