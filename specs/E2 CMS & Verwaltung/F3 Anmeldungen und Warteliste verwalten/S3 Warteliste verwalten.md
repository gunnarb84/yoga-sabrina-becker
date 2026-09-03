# Warteliste verwalten

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich die Warteliste einer Veranstaltung einsehen und bei Bedarf
manuell steuern können, damit ich Sonderfälle behandeln kann.

## Description
Die Warteliste ist eine Rangfolge von Anmeldungen, die über der maximalen Teilnehmerzahl
liegen. Sie wird automatisch verwaltet; bei manuellem Eingriff kann ein Eintrag nach vorn oder
nach hinten verschoben werden.

## Akzeptanzkriterien
- Die Warteliste einer Veranstaltung wird in der Anmeldungsansicht angezeigt.
- Die Liste zeigt Rang, Name, E-Mail und Anmeldedatum.
- Die Reihenfolge ist standardmäßig nach Anmeldedatum (`registeredAt`).
- Ein Wartelisten-Eintrag kann manuell in der Rangfolge verschoben werden.
- Ein manuell nachgerückter Eintrag löst die E-Mail-Benachrichtigung aus.
- Ein Wartelisten-Eintrag kann direkt storniert werden.
