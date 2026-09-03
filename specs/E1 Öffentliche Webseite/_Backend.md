# Backend — E1 Öffentliche Webseite

Vorgänge, Abfragen und Ereignisse des Epics. Fachliche Felder siehe `_Datenstruktur.md`.

## Abfragen

### `ListPublishedActivities`
Liefert alle zukünftigen, veröffentlichten Veranstaltungen mit `type`, `title`,
`shortDescription`, `price`, `freeSeats`, `waitingListCount`, `isBookable`, `image`.

### `GetActivityDetails`
Liefert die Detailansicht einer einzelnen Veranstaltung inkl. aller öffentlichen
Termine (`Session`) und Langbeschreibung.

### `ListPublishedPages`
Liefert alle veröffentlichten Seiten mit `slug`, `title`.

### `GetPageContent`
Liefert Inhalt und Meta-Daten einer Seite anhand von `slug`.

### `ListNavigationItems`
Liefert die öffentliche Navigation sortiert nach `sortOrder`.

## Vorgänge

E1 enthält keine schreibenden Vorgänge; diese liegen in E2.

## Ereignisse

E1 veröffentlicht keine Domain Events.
