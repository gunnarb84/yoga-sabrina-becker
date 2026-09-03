# Navigation anzeigen

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich jederzeit eine klare Navigation sehen, damit ich zwischen den
Rubriken der Webseite wechseln kann.

## Description
Die Navigation wird im CMS gepflegt (`NavigationItem`) und auf jeder öffentlichen Seite
oben angezeigt. Sie enthält Links zur Startseite, zur Veranstaltungsübersicht und zu den
statischen Seiten.

## Akzeptanzkriterien
- Die Navigation wird auf jeder öffentlichen Seite oben dargestellt.
- Die Navigation zeigt alle aktiven Navigationseinträge in der festgelegten Reihenfolge
  (`sortOrder`).
- Externe Links (`isExternal` = true) öffnen in einem neuen Tab und sind als extern
  gekennzeichnet.
- Der aktive Eintrag ist visuell hervorgehoben.
- Auf mobilen Geräten wird die Navigation in ein Burger-Menü zusammengefasst.
