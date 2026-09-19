# Navigation anzeigen

## Meta
- **State:** Modified

## User Story
Als Besucher möchte ich jederzeit eine klare Navigation sehen, damit ich zwischen den
Rubriken der Webseite wechseln kann.

## Description
Die Navigation wird im CMS gepflegt (`NavigationItem`) und auf jeder öffentlichen Seite
oben angezeigt. Sie enthält Links zur Startseite, zur Veranstaltungsübersicht und zu den
statischen Seiten. Auf mobilen Geräten wird sie zu einem Burger-Menü zusammengefasst.
(Die Burger-Funktion wurde als umgesetzt markiert, ist in der Praxis aber ohne Wirkung —
die Klasse wird auf ein falsches Element gesetzt; die Umsetzung wird nachgeholt, siehe
Idee [I8](../../../ideas/I8%20Mobilf%C3%A4higkeit%20Webseite%20und%20Verwaltung.md).)

## Akzeptanzkriterien
- Die Navigation wird auf jeder öffentlichen Seite oben dargestellt.
- Die Navigation zeigt alle aktiven Navigationseinträge in der festgelegten Reihenfolge
  (`sortOrder`).
- Externe Links (`isExternal` = true) öffnen in einem neuen Tab und sind als extern
  gekennzeichnet.
- Der aktive Eintrag ist visuell hervorgehoben.
- Bei einer Viewport-Breite von höchstens 800 px ist die Navigation ausgeblendet und eine
  Burger-Schaltfläche sichtbar.
- Die Betätigung der Burger-Schaltfläche zeigt alle Navigationseinträge als aufklappbare
  Liste; erneute Betätigung schließt die Liste.
- Bei einer Viewport-Breite von mehr als 800 px wird die Navigation ohne
  Burger-Schaltfläche wie bisher angezeigt.
