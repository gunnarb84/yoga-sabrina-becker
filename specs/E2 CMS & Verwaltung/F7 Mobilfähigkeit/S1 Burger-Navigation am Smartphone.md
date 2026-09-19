# Burger-Navigation am Smartphone

## Meta
- **State:** Modified

## User Story
Als Administratorin möchte ich die Verwaltungsnavigation am Smartphone über ein
Burger-Menü erreichen, damit ich alle Verwaltungsbereiche auch vom Telefon aus aufrufen
kann.

## Description
Die horizontale Topbar-Navigation der Verwaltung (Dashboard, Aktivitäten, Kursvorlagen,
Anmeldungen, Teilnehmer, Kontaktanfragen, Rechnungen, Bareinnahmen, Bareinnahme erfassen,
Nachrichten) wird auf Smartphone-Breiten zu einer Burger-Schaltfläche mit aufklappbarer
Liste zusammengefasst. Die Desktop-Darstellung bleibt unverändert. Grundlage: Idee
[I8](../../../ideas/I8%20Mobilf%C3%A4higkeit%20Webseite%20und%20Verwaltung.md).

## Akzeptanzkriterien
- Bei einer Viewport-Breite von höchstens 640 px ist die Topbar-Navigation ausgeblendet
  und eine Burger-Schaltfläche sichtbar.
- Die Betätigung der Burger-Schaltfläche zeigt alle Navigationseinträge — einschließlich
  Abmelden — als aufklappbare Liste in der festgelegten Reihenfolge.
- Jeder Navigationseintrag ist im geöffneten Menü per Antippen aufrufbar.
- Die Betätigung der Burger-Schaltfläche bei geöffnetem Menü schließt es.
- Der aktive Bereich ist im geöffneten Menü visuell hervorgehoben.
- Bei einer Viewport-Breite von mehr als 640 px bleibt die Desktop-Darstellung der
  Topbar unverändert (keine Burger-Schaltfläche).