# Veranstaltung anlegen

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich eine neue Veranstaltung anlegen können, damit sie später auf
der Webseite angezeigt und für Anmeldungen geöffnet werden kann.

## Description
Im Backend gibt es eine Maske, um Kurse, Events oder Workshops neu anzulegen. Die Maske
zeigt Liste und Detail nebeneinander (Split-View). Beim Anlegen erhält die Veranstaltung
den Status „Entwurf".

## Akzeptanzkriterien
- Der Menüpunkt „Veranstaltungen" ist unter der Modulgruppe „Verwaltung" erreichbar.
- Die Maske enthält die Pflichtfelder `title`, `type`, `price`, `maxParticipants`.
- Die Maske enthält die optionalen Felder `shortDescription`, `longDescription`, `image`.
- Der Vorgang `CreateActivity` legt die Veranstaltung mit dem Status `DRAFT` an.
- Der Vorgang `CreateActivity` verweigert die Anlage mit dem Fehlercode `TITLE_EMPTY`, wenn
  `title` leer ist.
- Der Vorgang `CreateActivity` verweigert die Anlage mit dem Fehlercode `PRICE_NEGATIVE`,
  wenn `price` kleiner als 0 ist.
- Der Vorgang `CreateActivity` verweigert die Anlage mit dem Fehlercode
  `MAX_PARTICIPANTS_TOO_LOW`, wenn `maxParticipants` kleiner als 1 ist.
- Nach erfolgreicher Anlage erscheint die neue Veranstaltung in der Liste.
