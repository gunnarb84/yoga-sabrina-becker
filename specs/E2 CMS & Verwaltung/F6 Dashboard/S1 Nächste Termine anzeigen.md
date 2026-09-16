# Nächste Termine anzeigen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich auf dem Dashboard die Termine des laufenden Monats mit
Belegung sehen, damit ich weiß, was unmittelbar ansteht und wo ich hineingehen muss.

## Description
Das Dashboard zeigt unter „Nächste Termine" alle Termine vom heutigen Datum bis zum
Ende des laufenden Monats, aufsteigend nach Beginn. Jede Zeile nennt die Veranstaltung
mit Beginn und Belegung; ein Sprung öffnet die Anmeldungsliste der Veranstaltung. Das
Dashboard ist die Startseite der Verwaltung.

## Akzeptanzkriterien
- Das Dashboard zeigt unter „Nächste Termine" alle `Termin`-Zeilen mit `startsAt` vom
  heutigen Datum bis zum Ende des laufenden Monats, aufsteigend nach `startsAt`.
- Termine mit `startsAt` vor dem heutigen Datum werden nicht angezeigt.
- Jede Terminzeile zeigt `title` der `Veranstaltung`, das Datum mit Uhrzeit von
  `startsAt` und die Anzahl der Anmeldungen der Veranstaltung mit Status `Bestätigt`.
- Jede Terminzeile zeigt die freien Plätze: `maxParticipants` der Veranstaltung
  abzüglich der bestätigten Anmeldungen.
- Ein Klick auf eine Terminzeile öffnet die Anmeldungsliste der Veranstaltung.
- Sind keine Termine im Zeitraum vorhanden, zeigt der Block den Hinweis
  „Keine Termine im laufenden Monat."
- Die Abfrage `ListUpcomingSessions` liefert die Termine des Zeitraums mit
  Veranstaltungstitel, `maxParticipants` und Anzahl bestätigter Anmeldungen.

## Siehe auch
- [S2 Offene Aufgaben anzeigen](S2%20Offene%20Aufgaben%20anzeigen.md)