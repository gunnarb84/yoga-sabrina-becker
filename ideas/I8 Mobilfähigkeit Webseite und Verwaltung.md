# Mobilfähigkeit Webseite und Verwaltung

## Meta
- **State:** Übernommen

## Specs
- [specs/E1 Öffentliche Webseite/F1 Seiten und Navigation/S3 Navigation anzeigen.md](../specs/E1%20%C3%96ffentliche%20Webseite/F1%20Seiten%20und%20Navigation/S3%20Navigation%20anzeigen.md)
- [specs/E2 CMS & Verwaltung/F7 Mobilfähigkeit/S1 Burger-Navigation am Smartphone.md](../specs/E2%20CMS%20%26%20Verwaltung/F7%20Mobilf%C3%A4higkeit/S1%20Burger-Navigation%20am%20Smartphone.md)
- [specs/E2 CMS & Verwaltung/F7 Mobilfähigkeit/S2 Login und Formulare am Smartphone.md](../specs/E2%20CMS%20%26%20Verwaltung/F7%20Mobilf%C3%A4higkeit/S2%20Login%20und%20Formulare%20am%20Smartphone.md)
- [specs/E2 CMS & Verwaltung/F7 Mobilfähigkeit/S3 Listen am Smartphone.md](../specs/E2%20CMS%20%26%20Verwaltung/F7%20Mobilf%C3%A4higkeit/S3%20Listen%20am%20Smartphone.md)
- [specs/E2 CMS & Verwaltung/F7 Mobilfähigkeit/S4 PDF-Ausgaben am Smartphone.md](../specs/E2%20CMS%20%26%20Verwaltung/F7%20Mobilf%C3%A4higkeit/S4%20PDF-Ausgaben%20am%20Smartphone.md)

## Problem
Die Verwaltung wird auch vom Smartphone aus genutzt, ist dort aber kaum
bedienbar: Die Topbar mit zehn Links stapelt in der Mobilansicht unübersichtlich
über mehrere Zeilen, die Login-Karte hat eine feste Breite von 404 px und läuft
auf schmalen Geräten über den Rand, Listen und Formulare sind nicht auf
Touch-Eingabe eingestellt. Die öffentliche Webseite ist überwiegend responsiv
(drei Breakpoints), aber das Burger-Menü funktioniert nicht: Der Button setzt
die Klasse `open` auf das umgebende `<nav>` statt auf die Navigationsliste —
das CSS schaltet nur `.yoga-nav-list.open` sichtbar, der Klick zeigt also
keine Wirkung. Damit erfüllt die Story `specs/E1 Öffentliche Webseite/F1
Seiten und Navigation/S3 Navigation anzeigen.md` (State `Implemented`) ihr
Kriterium „Auf mobilen Geräten wird die Navigation in ein Burger-Menü
zusammengefasst" in der Praxis nicht. Auch Beleg- und Monatsdruck-Ausgaben
müssen vom Smartphone aus nutzbar sein (herunterladen und über das Teilen-Menü
bzw. AirPrint drucken lassen).

## Lösungsidee
Beide Bereiche werden mobil vollständig bedienbar; die Desktop-Darstellung
bleibt unverändert.

**Verwaltung am Smartphone:**
- Die Topbar-Navigation wird unterhalb eines Breakpoints zu einem
  Burger-Menü zusammengefasst (aufklappbar, wie von der Webseite bekannt).
- Formulare werden einspaltig dargestellt, die Login-Karte passt sich der
  Gerätebreite an (maximale Breite statt fester Breite).
- Listen bleiben Tabellen mit horizontalem Scrollen — die Scroll-Wrapper
  existieren an allen Listen bereits; sie werden auf Mobil korrekt dargestellt
  und bedienbar.
- Alle Masken und Vorgänge bleiben am Smartphone vollständig ausführbar
  (ansehen, erfassen, ändern, speichern).

**Webseite:**
- Der Burger-Fehler wird behoben (Klasse auf das richtige Element setzen),
  die Story S3 wird entsprechend korrigiert und erneut umgesetzt.
- Kleinere Lücken der bestehenden Breakpoints werden nachgezogen.

**Ausgaben:**
- Die Beleg-PDFs (Bareinnahmenbeleg, Kassenbuch-Monatsdruck, Rechnung)
  öffnen am Smartphone in der Systemvorschau und lassen sich von dort über
  das Teilen-Menü (AirPrint) drucken; die bestehenden Download-/PDF-Links
  werden auf Mobil verifiziert.

**Tablet:** Beide Bereiche funktionieren mit der bestehenden Desktop- bzw.
Breakpoint-Darstellung; die Ansichten werden auf Tablet-Breite geprüft und
feinjustiert.

## Scope
- **In Scope:** Mobil-Darstellung der Verwaltungs-Shell (Burger-Menü,
  einspaltige Formulare, anpassbare Login-Karte, bedienbare Listen-Tabellen),
  Burger-Fix der Webseite, Nachziehen kleinerer Responsivitätslücken der
  Webseite, Mobil-Tauglichkeit der PDF-Ausgaben (Download + Systemvorschau/
  AirPrint), Tablet-Prüfung beider Bereiche.
- **Out of Scope:** Listen als gestapelte „Karten"-Darstellung am Smartphone
  (Option B — mögliche Stufe 2, dann separate Idee), native App,
  Umgestaltung der Desktop-Shell (keine Sidebar/Tabbar im Desktop),
  Änderungen an der Drucklogik selbst (PDF-Erzeugung bleibt unverändert).

## Auswirkungen auf den Bestand
- **Specs:** Die Story `specs/E1 Öffentliche Webseite/F1 Seiten und
  Navigation/S3 Navigation anzeigen.md` wird auf `Modified` gesetzt (Kriterium
  ist als umgesetzt markiert, wird aber nicht erfüllt). Für die Verwaltung
  entsteht ein neues Feature in `specs/E2 CMS & Verwaltung/` mit Stories zu
  Burger-Navigation, Formularen/Login und Listen am Smartphone; für die
  Ausgaben am Smartphone entstehen Stories im zuständigen Feature
  (Bareinnahmen bzw. Rechnungen).
- **Datenstruktur:** Keine Änderungen — keine neuen Entitäten oder Felder.
- **Backend:** Keine Änderungen — die PDF-Endpunkte
  (`verwaltung.rechnung.pdf`, `verwaltung.bareinnahme.pdf`,
  `verwaltung.bareinnahmen.monat.pdf`) existieren und bleiben unverändert.
- **Quellcode:** `src/public/css/yoga-ui.css` (Mobil-Breakpoints der
  Verwaltungs-Shell, Login-Karte), `src/public/css/theme.css` (Formular-Raster
  am Smartphone), `modules/Verwaltung/Ui/layouts/app.blade.php` (Burger-Menü),
  `modules/Verwaltung/Ui/layouts/guest.blade.php` (Login), ggf. Anpassungen in
  den elf Listen-Blades der Verwaltung, `modules/Webseite/Ui/layouts/
  app.blade.php` (Burger-Fix) und `src/resources/css/app.css` (Breakpoints
  der Webseite). Risiken gering — reine Darstellungsänderungen, kein
  Fachcode; die Desktop-Darstellung darf sich nicht ändern.

## Entscheidungen
- 19.09.2026: Idee angelegt, Nummer I8 reserviert.
- 19.09.2026: Alles muss mobil gehen — die Verwaltung ist am Smartphone
  vollständig bedienbar (erfassen, ändern, speichern), nicht nur lesend.
- 19.09.2026: Umfang Stufe 1 = Option A: Listen bleiben am Smartphone
  Tabellen mit horizontalem Scrollen; Karten-Darstellung ist mögliche Stufe 2
  (Out of Scope).
- 19.09.2026: Drucken vom Handy muss möglich sein (AirPrint über
  Systemvorschau/Teilen-Menü der PDF-Ausgaben); Drucken ist keine
  Desktop-Sache mehr.
- 19.09.2026: Die Desktop-Darstellung beider Bereiche bleibt unangetastet —
  nur Breakpoints und Mobil-Regeln kommen hinzu.
- 19.09.2026: Ursache des Burger-Fehlers der Webseite im Code bestätigt
  (`modules/Webseite/Ui/layouts/app.blade.php:25` — `nextElementSibling`
  trifft `<nav>`, nicht die Liste).

## Offene Punkte
- (keine)