# Veranstaltungsübersicht anzeigen

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich alle aktuellen Yoga-Angebote in einer Übersicht sehen, damit ich
einen passenden Kurs, Event oder Workshop finden kann.

## Description
Die Veranstaltungsübersicht listet alle zukünftigen, veröffentlichten Veranstaltungen
(`Activity`) auf. Jeder Eintrag führt zur Detailseite. Die Übersicht ist nach Datum sortiert.

## Akzeptanzkriterien
- Die Übersicht ist unter `/kurse` oder `/veranstaltungen` erreichbar.
- Es werden nur zukünftige, veröffentlichte Veranstaltungen (`isPublished` = true,
  `status` = Veröffentlicht) angezeigt.
- Jede Veranstaltung zeigt Titel, Typ (`type`), Kurzbeschreibung, nächsten Termin, Preis,
  Bild und Hinweis auf Buchbarkeit.
- Ausgebuchte Veranstaltungen zeigen den Hinweis „Warteliste" mit der Anzahl der
tplatzierten Einträge (`waitingListCount`).
- Klick auf eine Veranstaltung öffnet deren Detailseite.
- Es gibt eine Filtermöglichkeit nach Typ (Kurs/Event/Workshop).
- Veranstaltungen ohne zukünftigen Termin werden nicht angezeigt.
