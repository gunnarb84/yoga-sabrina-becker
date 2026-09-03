# Teilnehmerliste anzeigen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich alle Teilnehmer/innen in einer Übersicht sehen, damit ich
schnell auf die Daten zugreifen kann.

## Description
Die Teilnehmerverwaltung zeigt alle erfassten Teilnehmer/innen in einer Liste. Über die Liste
ist eine Suche und Filterung möglich.

## Akzeptanzkriterien
- Der Menüpunkt „Teilnehmer" ist unter der Modulgruppe „Verwaltung" erreichbar.
- Die Liste zeigt `firstName`, `lastName`, `email`, `phone` und `city`.
- Die Liste ist nach `lastName` sortiert.
- Es gibt ein Suchfeld, das in `firstName`, `lastName` und `email` sucht.
- Klick auf einen Teilnehmer öffnet die Detailmaske.
- Gesundheitsinformationen (`healthNotes`) werden in der Liste nicht angezeigt.
