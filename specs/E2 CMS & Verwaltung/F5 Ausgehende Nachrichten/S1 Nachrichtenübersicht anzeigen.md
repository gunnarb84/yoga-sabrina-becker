# Nachrichtenübersicht anzeigen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich alle aus dem System versendeten E-Mails einsehen können,
damit ich im Störungsfall nachvollziehen kann, was herausgegangen ist.

## Description
Jede versendete E-Mail wird als `OutboundMessage` protokolliert. Die Übersicht ist
sortier- und filterbar und zeigt Empfänger, Betreff, Status und Versandzeitpunkt.

## Akzeptanzkriterien
- Der Menüpunkt „Ausgehende Nachrichten" ist unter der Modulgruppe „Verwaltung" erreichbar.
- Die Liste zeigt `recipient`, `subject`, `status` und `sentAt`.
- Die Liste lässt sich nach `status` (Versandt/Fehlgeschlagen/Ausstehend) und `recipient`
  filtern.
- Klick auf eine Nachricht öffnet die Detailansicht.
