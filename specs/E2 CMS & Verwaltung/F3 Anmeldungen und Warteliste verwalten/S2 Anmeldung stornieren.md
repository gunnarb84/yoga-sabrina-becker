# Anmeldung stornieren

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich eine Anmeldung im Backend stornieren können, damit die
Verwaltung die Hoheit über Stornierungen behält.

## Description
Teilnehmer/innen können ihre Anmeldung nicht selbst stornieren. Eine Stornierung im Backend
entfernt den Platz oder den Wartelisten-Eintrag. Wenn ein fester Platz frei wird, rückt der
erste Wartelisten-Eintrag automatisch nach.

## Akzeptanzkriterien
- Die Aktion „Stornieren" ist in der Anmeldungsmaske verfügbar.
- Der Vorgang `CancelRegistration` setzt den Status auf `CANCELLED`.
- `CancelRegistration` scheitert mit `registration.not_found`, wenn die Anmeldung nicht existiert.
- `CancelRegistration` scheitert mit `registration.already_cancelled`, wenn der Status bereits `CANCELLED`
  ist.
- Bei einer Stornierung eines festen Platzes wird der erste Eintrag der Warteliste
  automatisch nachgerückt (`PromoteWaitingListEntry`).
- Der nachgerückte Teilnehmer/die nachgerückte Teilnehmerin erhält eine E-Mail-Benachrichtigung.
- Nach der Stornierung ist in der Liste der Status „Storniert" sichtbar.
