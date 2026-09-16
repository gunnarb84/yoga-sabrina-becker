# Offene Aufgaben anzeigen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich auf dem Dashboard die Zähler der Aufgaben sehen, die
Arbeit brauchen, damit ich nichts verliere.

## Description
Das Dashboard zeigt unter „Offene Aufgaben" je Zeile einen Zähler mit Sprung in die
zugehörige Liste: unbezahlte Rechnungen, fehlgeschlagene ausgehende E-Mails und
Anmeldungen auf der Warteliste. Kontaktanfragen erscheinen als Link ohne Zähler,
weil die Kontaktanfragen dem Webseite-Modul angehören und die Modulgrenze keinen
Zugriff der Verwaltung auf die Kontaktanfragen-Abfrage vorsieht (siehe Idee I7).

## Akzeptanzkriterien
- Das Dashboard zeigt unter „Offene Aufgaben" die Anzahl der Rechnungen mit Status
  `Offen`.
- Das Dashboard zeigt die Anzahl der ausgehenden Nachrichten mit Status
  `Fehlgeschlagen`.
- Das Dashboard zeigt die Anzahl der Anmeldungen mit Status `Warteliste`.
- Ein Klick auf den Rechnungs-Zähler öffnet die Rechnungsliste.
- Ein Klick auf den Nachrichten-Zähler öffnet die Liste der ausgehenden Nachrichten.
- Ein Klick auf den Warteliste-Zähler öffnet die Anmeldungsliste.
- Der Kontaktanfragen-Eintrag zeigt keinen Zähler; sein Klick öffnet die
  Kontaktanfragen-Liste.
- Ist ein Zähler `0`, ist die Zeile nicht sichtbar; ein Zähler größer als `0` ist
  sichtbar.

## Siehe auch
- [S1 Nächste Termine anzeigen](S1%20N%C3%A4chste%20Termine%20anzeigen.md)