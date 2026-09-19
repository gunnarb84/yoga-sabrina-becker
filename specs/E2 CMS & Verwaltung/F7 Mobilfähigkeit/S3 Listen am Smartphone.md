# Listen am Smartphone

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich die Listen der Verwaltung (Veranstaltungen, Anmeldungen,
Teilnehmer, Bareinnahmen, Rechnungen, Nachrichten, Kontaktanfragen) am Smartphone lesen
und dort die Aktionen einer Zeile auslösen, damit ich die Verwaltung vollständig vom
Telefon aus bedienen kann.

## Description
Die Listen der Verwaltung bleiben am Smartphone Tabellen und werden horizontal
gescrollt; die Seite selbst scrollt nur vertikal. Alle Spalten und die Aktionen einer
Zeile sind durch horizontales Scrollen erreichbar. Die Desktop-Darstellung bleibt
unverändert. Grundlage: Idee
[I8](../../../ideas/I8%20Mobilf%C3%A4higkeit%20Webseite%20und%20Verwaltung.md).

## Akzeptanzkriterien
- Listen der Verwaltung sind bei einer Viewport-Breite von höchstens 480 px innerhalb
  eines Scroll-Bereichs horizontal scrollbar.
- Beim horizontalen Scrollen einer Liste bewegt sich nur der Listen-Scroll-Bereich; die
  Seite als Ganzes wird nicht horizontal gescrollt.
- Alle Spalten einer Liste — einschließlich der Aktionen einer Zeile — sind durch
  horizontales Scrollen erreichbar.
- Die Kopfzeile der Liste ist im Scroll-Bereich sichtbar.
- Bei einer Viewport-Breite von mehr als 480 px bleibt die Desktop-Darstellung der
  Listen unverändert.