# Datenstruktur — E1 Öffentliche Webseite

Fachliche Gegenstände und Felder des Epics. Technische Schlüssel, Pflichtspalten, Indizes und
Migrationen regelt `_data.md` und gehören nicht hierher.

## Seite (`Page`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `slug` | URL-Schlüssel | Text | ja |
| `title` | Titel | Text | ja |
| `content` | Inhalt | formatierter Text | nein |
| `isPublished` | Veröffentlicht | Ja/Nein | ja |
| `metaDescription` | Meta-Beschreibung | Text | nein |

## Navigation (`NavigationItem`)

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `label` | Bezeichnung | Text | ja |
| `target` | Ziel (Seite oder externe URL) | Text | ja |
| `sortOrder` | Reihenfolge | Ganzzahl | ja |
| `isExternal` | Externer Link | Ja/Nein | ja |

## Veranstaltung (`Activity`) — Sicht auf öffentliche Darstellung

Nur lesende Sicht; fachliche Felder siehe E2 `_Datenstruktur.md`.

| Bezeichner | Oberfläche | Datentyp | Pflicht |
|---|---|---|---|
| `type` | Typ (Kurs/Event/Workshop) | Aufzählung | ja |
| `title` | Titel | Text | ja |
| `shortDescription` | Kurzbeschreibung | Text | nein |
| `longDescription` | Langbeschreibung | formatierter Text | nein |
| `price` | Preis | Geld | ja |
| `maxParticipants` | Maximale Teilnehmerzahl | Ganzzahl | ja |
| `freeSeats` | Freie Plätze | Ganzzahl | ja |
| `waitingListCount` | Anzahl Wartelisten-Einträge | Ganzzahl | ja |
| `isBookable` | Buchbar | Ja/Nein | ja |
| `image` | Bild | Medienreferenz | nein |
