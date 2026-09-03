# Modulabnahme — Prüfliste

Vor der Abnahme eines Moduls Punkt für Punkt prüfen. Jeder Punkt verweist auf den
Abschnitt in `_uiux.md`, der ihn verbindlich macht. Ein „nein" ist ein Fehler im Modul,
nicht im Leitfaden.

## Shell und Navigation
- [ ] Modulnummer im Sprungfeld erreichbar, Nummer in Tab und Navigation sichtbar (§4)
- [ ] Modul öffnet als Tab, nie als modales Fenster (§1.1)
- [ ] Kein eigener Anfänger-/Profimodus, kein zweites Menü (§1.3)
- [ ] Split-View als Standard, Umschalten auf Vollbreite funktioniert (§3)
- [ ] Zurück/Vorwärts wechseln die aktive Ansicht entlang des Besuchsverlaufs; kein Tab wird geschlossen oder geöffnet (§4a)
- [ ] Verlaufseintrag bei Modul öffnen, Satz öffnen, Tabwechsel — keiner bei Pfeiltasten-Satzwechsel, Filteränderung, `F6` (§1.2, §4a)
- [ ] Zurück ohne Rückfrage; ungespeicherter Zustand ist nach Vorwärts unverändert da (§1.8, §4a)
- [ ] Einträge geschlossener Tabs werden übersprungen; am Verlaufsanfang bleibt Zurück wirkungslos, die Anwendung wird nicht verlassen (§4a)
- [ ] Adresszeile bleibt unverändert; Neuladen startet auf der Startseite, `beforeunload` greift (§4a, §5.3, §10)
- [ ] `Strg+Alt+Enter` und der Menüeintrag im Panelkopf öffnen das Ziel in einem neuen Fenster; dort ist genau diese Ansicht vorn (§3a, §4, §5.2)
- [ ] Übergabe ohne Adresszeile: beide Fenster zeigen dieselbe unveränderte Adresse, kein Deep-Link, kein Lesezeichen (§4a, §10)
- [ ] Fenstertitel nennt die vordere Ansicht (Modulnummer, Modulname, bei geöffnetem Satz dessen Nummer) (§3a, §10)
- [ ] Kein zweiter Arbeitsbereich im Fenster; das Modul läuft in Split-View oder Vollbreite (§3, §3a)

## Liste
- [ ] Fünf Spalten im Split-View, Spaltenraster über `--au-cols` am Panel (§6)
- [ ] Feste Zeilenhöhe, einzeilige Zellen, virtualisiert (§6, §9)
- [ ] Kein Zebra, Auswahl über `au-table__row--sel` (§6)
- [ ] Doppelklick öffnet den Satz (§5.2, §6)
- [ ] Suchfeld auf `F7`, gesetzte Filter als entfernbare Chips (§6)
- [ ] Summen und Satzposition im Fuß, keine Tastenlegende (§6)
- [ ] Gruppen — falls vorhanden — als Spalte, Filter und Gruppierkriterium; nicht hier verwaltbar (§6)
- [ ] Massenaktionen erst nach Mehrfachauswahl (§6)

## Maske
- [ ] Reihenfolge Identität → Tabs → Kennzahlen → Feldgruppen → Statuszeile (§7)
- [ ] Höchstens drei Feldgruppen je Tab, höchstens vier Kennzahlen (§7)
- [ ] Jedes Eingabefeld auch leer als Feld erkennbar, Platzhalter beschreibt den Inhalt (§7)
- [ ] Zustände geändert / fehlerhaft / gesperrt korrekt gesetzt (§7)
- [ ] Branchenspezifische Felder ausschließlich in einem eigenen Tab, nie im Kernraster (§1.7, §8.4)
- [ ] Speichern explizit, `Esc` verwirft, `beforeunload`-Schutz greift (§5.3, §10)
- [ ] Panelkopf: höchstens zwei ausgeschriebene Buttons plus Burger (§1.4)
- [ ] Burger-Menü in fester Reihenfolge, höchstens neun Einträge, Löschen zuletzt (§7)

## Belege und Strukturlisten (falls zutreffend)
- [ ] Kopf als abgegrenzter Block, Statusfluss als Kette (§7a)
- [ ] Rasterzelle ohne Dauerkontur, Zustände wie Feldbox (§7a)
- [ ] `Enter`/`Tab` gehen zellenweise, speichern nie (§7a)
- [ ] `F3`/`F4` im Raster positionsbezogen, als Buttons mit Kürzel sichtbar (§7a)
- [ ] Endzustand vollständig gesperrt, Korrekturvorgang statt Änderung (§7a)
- [ ] Ausgabe: `Strg+P` öffnet die Vorschau, nicht den Druckdialog (§7a)
- [ ] Bestandteile in einem Raster mit Gruppenzeilen, nicht in mehreren Tabellen (§7d)
- [ ] Summen und verdichtete Merkmale gerechnet, nirgends doppelt gepflegt (§7d)
- [ ] Fixierte und aktuelle Werte nebeneinander, Abweichung sichtbar, „–" ohne Fixierung (§7d)
- [ ] Vorlagenzeilen tragen Zeilenstatus, entfernte bleiben sichtbar (§7e)

## Übergreifende Muster
- [ ] Wertehilfe auf `F5`, ein Treffer wird sofort übernommen, Anlegen im Overlay (§7b)
- [ ] Löschdialog nennt Nummer und Bezeichnung; bei Verwendung Nachweis und Deaktivieren (§7c)
- [ ] Werte aus Rangfolgen zeigen Ergebnis und geprüfte Stufen mit Grund (§7f)
- [ ] Erzeugte Texte schreibgeschützt, Zusatztext als eigenes Feld (§7h)
- [ ] Meldungen im richtigen Format: Feldmeldung, Inline-Hinweis, Dialog, nicht-modale Meldung (§3a)

## Tastatur
- [ ] Belegung aus §5 unverändert, nur `F9` modulbezogen; `F2`/`F12` frei (§5.1, §5.3)
- [ ] Kürzel nur am Auslöser sichtbar, keine Tastenübersicht in der Maske (§1.4, §7a)
- [ ] Nicht verfügbare Funktionen grau im Menü, nicht entfernt (§5.3)
- [ ] Vorbelegte Browsertasten werden abgefangen (§5.3)

## Qualitätsschranken
- [ ] Text und bedeutungstragende Zeichen ≥ 4,5:1, Linien ≥ 3:1 (§9)
- [ ] Status als Punkt + Wort, nichts allein über Farbe (§1.5, §9)
- [ ] `:focus-visible`-Ring vorhanden, Tabreihenfolge folgt der Leserichtung (§9)
- [ ] Klickziele ≥ `--au-control-h`, in Touch-Kontexten ≥ `--au-touch-h` (§9)
- [ ] Erste Listenzeile < 300 ms, Satzwechsel < 100 ms (§9)
- [ ] Keine externe Ressource zur Laufzeit (§9)

## Code
- [ ] Keine Farb-, Größen- oder Abstandsliterale im Modul-CSS — nur `var(--au-*)` (§2.3)
- [ ] Keine neue Klasse außerhalb des Klassenverzeichnisses (§10, §12)
- [ ] Formatierung von Zahlen, Datum und Mengen zentral, nicht in der View (§10)
- [ ] Zustand je Tab isoliert, Änderungsmerker vorhanden (§10)
