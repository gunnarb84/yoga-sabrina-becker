# _uiux.md — UI/UX-Leitfaden für Fachanwendungen (Laravel Livewire)

Verbindliches Regelwerk für Oberflächen vorgangsgetriebener Fachanwendungen
(Warenwirtschaft, Auftragsabwicklung, Logistik, Disposition) mit genau einer Firma je
Installation (`_data.md` §5) und vielen gleichzeitigen Sitzungen. Zusammen mit dem Referenz-Stylesheet
(Tokens im `:root`, Klassenprefix `au-`) wird diese Datei unverändert in Projekte
übernommen. Projektspezifisches — Farben, Schriften, Modulbenennung — steht in der
Theme-Datei bzw. im Profil des Projekts, niemals hier.

Leitsatz: **viel Information, wenig Maskenwechsel, wenig Klicks.** Tastatur schlägt Maus.
Keine Assistenten, keine Modalflut.

Alle Maß-, Farb- und Größenwerte stehen ausschließlich als Tokens im Stylesheet; dieser
Leitfaden nennt Tokennamen und Rollen, keine Literale.

---

## 1. Nicht verhandelbare Prinzipien

1. **Ein Modul oder Datensatz = ein Tab** — je Fenster (§3a). Öffnen erzeugt nie ein
   modales Fenster. Dialoge nur für Rückfragen mit maximal drei Feldern.
2. **Liste und Maske gleichzeitig sichtbar** (Split-View) als Standard. Satzwechsel per
   Pfeiltasten in der Liste aktualisiert die Maske sofort — kein Klick, kein Zurück.
   Satzwechsel ist Blättern, kein Besuch: er erzeugt keinen Verlaufseintrag (§4a).
3. **Kein Anfänger-/Profi-Modus.** Es gibt *ein* Menü, das sich mit Übung von selbst
   verkürzt: Sprungfeld (Nummer *oder* Name) → Favoriten → Modulübersicht.
4. **Funktionen erscheinen als Buttons, nicht als Tastenlegende.** Im Kopf jedes Panels
   stehen die wichtigsten Aktionen ausgeschrieben (maximal zwei), alles Weitere im
   Burger-Menü daneben. **Das Kürzel steht am Auslöser** — im Button und rechtsbündig im
   Menüeintrag. Keine Tastenleiste in Listenfuß oder Statuszeile.
5. **Status nie über Farbe allein**: Punkt + Wort, damit Schwarzweißdruck und
   Farbfehlsichtigkeit funktionieren.
6. **Zahlen monospaced, rechtsbündig, `tabular-nums`**; Summenzeile im Listenfuß.
7. **Kernfelder branchenneutral.** Branchen- oder kundenspezifische Felder liegen in
   einem eigenen Detail-Tab, nie im Kernraster.
8. **Speichern ist explizit.** Ungespeicherte Sätze blockieren keinen Tabwechsel, gehen
   aber nicht verloren; der Tab markiert den Zustand, die Statuszeile nennt ihn. Auch der
   Besuchsverlauf (§4a) wechselt nur die Ansicht und verwirft nichts.
9. **Dichte vor Weißraum**, jedoch nie unter der Schriftuntergrenze (`--au-fs-sm`) und nie
   unter dem Mindestkontrast (§9).
10. **Rollen ändern die Reihenfolge, nicht die Maske.** Dieselben Felder, dieselbe
    Komponente; rollenabhängig ist allenfalls, welche Gruppe oben steht.

---

## 2. Visuelle Sprache

### 2.1 Tokenrollen (Werte ausschließlich im Stylesheet)

| Rolle | Token |
|---|---|
| Anwendungshintergrund | `--au-canvas` |
| Panelfläche / Eingabefläche | `--au-panel` · `--au-input-bg` |
| abgesenkte Fläche (Fußzeilen, Kennzahlkarten) | `--au-panel-sunken` |
| Hover (Zeile, Menü, offener Button) | `--au-panel-hover` |
| Filter-/Suchfeld | `--au-search-bg` |
| Linien: Panel · Listenzeile · Buttonrand | `--au-line` · `--au-line-soft` · `--au-line-strong` |
| Feldkontur: Ruhe · Hover · Fokus | `--au-line-input` · `--au-line-input-hover` · `--au-line-focus` |
| Eingabezustände: gesperrt · geändert · fehlerhaft · Platzhalter | `--au-input-bg-ro` · `--au-input-bg-dirty` · `--au-input-bg-invalid` · `--au-input-ph` |
| Text: primär · sekundär · Fließtext · Meta · Label · Zierzeichen · Feldsymbol | `--au-text` · `--au-text-2` · `--au-text-3` · `--au-muted` · `--au-label` · `--au-faint` · `--au-adorn` |
| Akzent (Orientierung): **Linie/Fläche** · **Text** · **Fläche mit Text** · Fläche · Zeilenauswahl · Kürzelmarke | `--au-accent` (nie für Text) · `--au-accent-ink` (jeder Akzenttext) · `--au-accent-fill` + `--au-on-accent` (gefüllte Fläche, die Text trägt) · `--au-accent-soft` · `--au-accent-row` · `--au-accent-mark` |
| Aktion (Primärbutton): Fläche · Hover · Schrift · Kürzel | `--au-action` · `--au-action-hover` · `--au-on-action` · `--au-on-action-key` |
| Status | `--au-ok` · `--au-warn` · `--au-warn-ink` (Warnung als Text) · `--au-error` · `--au-neutral` · `--au-ok-soft` (erreichter Statusschritt) · `--au-on-error` · `--au-on-error-key` |
| Overlay: Abdunkelung · Panelschatten · Overlayschatten · Fokusring · weicher Ring | `--au-scrim` · `--au-shadow-panel` · `--au-shadow-pop` · `--au-focus-ring` · `--au-focus-ring-soft` |
| Schriftfamilien | `--au-font` (UI) · `--au-mono` (Zahlen, Codes, Kürzel) |
| Schriftgrade | `--au-fs-xs` · `--au-fs-key` · `--au-fs-sm` · `--au-fs-md` · `--au-fs-lg` · `--au-fs-xl` · `--au-fs-2xl` |
| Zeilenhöhen | `--au-lh-tight` · `--au-lh-text` |
| Radien | `--au-r-sm` · `--au-r-md` · `--au-r-lg` · `--au-r-xl` · `--au-r-chip` |
| Abstandsstufen | `--au-s-1` · `--au-s-2` · `--au-s-3` · `--au-s-4` · `--au-s-5`, daraus `--au-gap` (Panelabstand) und `--au-pad` (Panelinnenabstand) |
| Höhen | `--au-row-h` · `--au-row-h-dense` · `--au-control-h` · `--au-input-h` · `--au-touch-h` |
| Zonen | `--au-topbar-h` · `--au-tabbar-h` · `--au-statusbar-h` · `--au-footer-h` · `--au-sidebar-w` · `--au-rail-w` · `--au-navigator-w` · `--au-list-w` · `--au-list-min-w` · `--au-detail-min-w` · `--au-menu-w` · `--au-palette-w` |
| Feldraster · Listenspalten | `--au-field-cols` · `--au-cols` (je Modul am Panel gesetzt) |
| Detailmaße | `--au-burger-w` · `--au-burger-line-w` · `--au-burger-line-h` · `--au-dot` · `--au-avatar` · `--au-mark` · `--au-cell-pad` · `--au-chip-h` · `--au-arrow-w` · `--au-icon` · `--au-icon-sm` |
| Belegraster, Auswahl, Vorschau | `--au-cellbox-h` · `--au-cellbox-r` · `--au-cellbox-gap` · `--au-sums-w` · `--au-lookup-w` · `--au-preview-w` · `--au-sheet-w` · `--au-refcard-media-h` |
| Anmeldung, Übergang, Zusatzspalten | `--au-login-w` · `--au-facets-w` · `--au-context-w` · `--au-matrix-col` |
| Zeichenmarke · Fortschrittshöhe · Meldungsbreite | `--au-badge` · `--au-progress-h` · `--au-toast-w` |

Akzent = **Orientierung** (Modulnummer, aktiver Tab, Gruppentitel, Auswahl), nicht Aktion.
**Zwei Akzentrollen, nicht eine:** `--au-accent` trägt ausschließlich Linien, Konturen, Punkte
und Balken; **jeder Akzenttext** — Modulnummer, Gruppentitel, Verweis — läuft über
`--au-accent-ink`, weil der hellere Ton den Textkontrast (§9) nicht erreicht.
Trägt eine Akzentfläche selbst Text — der heutige Tag in der Monatsübersicht (§4) —, ist es
**`--au-accent-fill` mit `--au-on-accent` darauf**, nicht `--au-accent`: der hellere
Flächenton erreicht auch als Untergrund den Textkontrast (§9) nicht.
Dieselbe Trennung gilt bei der Warnfarbe: `--au-warn` trägt Rand und Fläche,
`--au-warn-ink` den Text — ein Entwurfsvermerk auf einer Ausgabe ist Text und
kontrastpflichtig (§9).
Die Primäraktion ist neutral-dunkel (`--au-action`), damit „wichtig" und „ausgewählt" nicht
dieselbe Farbe tragen. Genau **ein** Akzentton je Theme.

### 2.2 Typografie und Größenstufen

Zwei Familien: eine UI-Schrift und eine Monospace für Zahlen, Nummern, Codes und Kürzel.
Beide liegen lokal im Projekt; der Stack in `--au-font`/`--au-mono` endet in
Systemschriften. Keine Web-Font-Referenz nach außen (§9 Qualitätsschranken).

| Stufe | Verwendung |
|---|---|
| `--au-fs-key` | Tastenkürzel **und alle uppercase-Titel**: Spaltenkopf der Liste, Gruppentitel der Maske, Abschnittsüberschrift der Navigation, Kennzahllabel, Menüüberschrift, Typkürzel und Kürzelmarke |
| `--au-fs-xs` | Feldlabel, Feldmeldung, Modulnummer im Tab, Hinweistext der Sidebar |
| `--au-fs-sm` | Fußzeile, Chip, Statuswort — **Untergrenze für Fließ- und Wertetext** |
| `--au-fs-md` | Listenzelle, Navigation, Menüeintrag, Meta-Werte |
| `--au-fs-lg` | Feldwert, Tab, Sprungfeldeingabe |
| `--au-fs-xl` | Panelüberschrift, Kennzahlwert |
| `--au-fs-2xl` | Satzkopf, Begrüßung der Startseite |

Diese Zuordnung ist gegen das Stylesheet abzugleichen; Abweichungen sind Fehler im
Leitfaden, nicht in der CSS.

Abstände nur aus `--au-s-1 … --au-s-5`. Radien: Panels und Overlays groß, Steuerelemente
mittel, Chips und Feldboxen klein — nie gemischt innerhalb eines Bereichs.

### 2.3 Verboten

Farbverläufe · farbige Navigationsflächen · mehr als ein Akzentton · Schatten als
Dekoration · Karten mit farbigem Seitenrand · Icon-Sammlungen ohne Systematik · Emoji ·
Farbe als einziger Bedeutungsträger.

**Fließ- und Wertetext unter `--au-fs-sm`.** Ausgenommen sind ausschließlich Feldlabel,
Feldmeldung, Spalten- und Abschnittstitel sowie Tastenkürzel (`--au-fs-xs`,
`--au-fs-key`) — und nur unter drei Bedingungen: kurzer Text, uppercase oder Monospace mit
erhöhter Laufweite, Kontrast ≥ 4,5:1. Fließtext, Feldwerte und Listenzellen liegen niemals
unter `--au-fs-sm`.

**Farb-, Größen- und Abstandswerte direkt in Komponentenregeln.** Ausgenommen sind
ausschließlich: Haarlinien (Rahmen- und Trennlinienstärke), der Unterstrich aktiver Tabs,
Mikroabstände bis 3 px (`gap` innerhalb eines Steuerelements), Laufweite
(`letter-spacing`), `border-radius: 50%` für Kreise sowie die Spaltenzahl fester Gitter
(Kennzahlzeile, Kachelraster). Farbwerte sind **ohne jede Ausnahme** tokenpflichtig.

### 2.4 Symbole (`.au-icon`)

Symbole sind zulässig, aber nur als **ein** systematischer Satz — §2.3 verbietet
Icon-Sammlungen ohne Systematik, nicht das Symbol.

- **Nie der einzige Bedeutungsträger.** Ein Symbol steht neben Text, niemals allein: im
  Button neben der Beschriftung, im Menüeintrag (`.au-menu__item--ico`) neben dem Wort, im
  Navigationseintrag neben dem Namen. Wo kein Text danebensteht, entfällt das Symbol — nicht
  der Text (§9). Deshalb darf es in `--au-adorn` laufen: es wiederholt, was das Wort schon
  sagt.
- **Einzige Ausnahme: die Zeichen-Taste** (`.au-btn--icon`) — quadratisch wie die
  Burger-Taste, zulässig nur für Nebenfunktionen, die keine Leistenbreite rechtfertigen
  (etwa ein selten benutzter Umschalter in der Topbar). Die Bedeutung tragen `title`
  **und** `aria-label`. Eine Hauptaktion, ein Vorgang oder ein Menüeintrag wird nie zur
  Zeichen-Taste.
- **Größe aus dem Token** — `--au-icon`, in kleinen Steuerelementen `--au-icon-sm`. Kein
  Größenwert in der Komponente (§2.3).
- **Farbe erbt.** Das Symbol übernimmt die Schriftfarbe seiner Umgebung, damit Hover, Auswahl
  und gesperrter Zustand es mitnehmen; eine eigene Farbe bekommt es nur dort, wo das
  Stylesheet sie setzt.
- **Die Dateien liegen lokal im Projekt**, wie die Schriften (§2.2): keine Icon-Schrift und
  kein Symbolsatz von außen (§9 Qualitätsschranken).
- **Der Text neben dem Symbol muss schrumpfen dürfen** (`.au-navitem__label`,
  `.au-rail__label`): sonst schiebt ein langer Name das rechtsbündige Tastenkürzel (§1.4) aus
  dem Eintrag heraus, und `.au-navlist` schneidet es ab. Einzeilig mit Ellipsis, wie die
  Listenzelle (§6).

---

## 3. Shell-Aufbau

```
au-app
├─ au-topbar    Marke · Sprungfeld/Suche · Firma · Rolle · Benutzer
├─ au-tabbar    Unterstrich-Tabs: offene Module UND Datensätze, "+" öffnet die Startseite
└─ au-work      Arbeitsbereich, Abstand --au-gap
   ├─ au-sidebar        Sprungfeld · Favoriten · Modulübersicht · au-navfoot (§4)
   ├─ au-panel--list    Liste
   └─ au-panel--detail  Maske
```

Höhen und Breiten der Zonen sind Tokens (§2.1). Der Umschalter für **Split-View ⇄
Vollbreite** liegt auf einer Funktionstaste (§5.1): in der Vollbreite-Ansicht öffnet die
Maske als eigener Datensatz-Tab, die Trefferliste bleibt als Satznavigator
(`.au-navigator`) stehen. Beides ist derselbe Screen, kein zweites Modul.

Umbruchregeln
- Reicht die Breite für Sidebar + Liste + Maske nicht, wird die Sidebar zur Nummern-Rail
  (`.au-rail`) mit Flyout.
- Unterschreitet die Summe von `--au-list-min-w` und `--au-detail-min-w` die verfügbare
  Breite, fällt die Ansicht automatisch auf Vollbreite + Datensatz-Tab. Darunter wird nicht
  gesplittet.

**Kennfarbe der Installation.** Laufen mehrere Installationen (Produktion, Test,
Schulung …), muss auf den ersten Blick erkennbar sein, welche vor einem steht. Dafür darf
die Topbar eine Kennfarbe tragen — die **einzige** Ausnahme vom Verbot farbiger
Navigationsflächen (§2.3), und sie ist eng gefasst: Die Kennfarbe färbt ausschließlich
Topbar und Anmeldekarte, ersetzt nie den Akzent und ist nie allein Bedeutungsträger
(§9) — neben der Farbe steht der Kurzname der Installation als Wort (`.au-kennmark`).
Die Laufzeit setzt `--au-kenn`/`--au-kenn-ink` und das Merkmal `data-kenn` auf `.au-app`;
ohne Kennfarbe bleibt die Topbar neutral, das ist der Auslieferungszustand. Farbsatz und
Vorbelegung je Installationsart definiert das Projekt; jeder Ton muss den Kontrast nach
§9 für seine Zeichenfarbe halten. Auswahl (`.au-swatchgrid`, `.au-swatch`) und Vorschau
(`.au-kennpreview`) stehen im Stylesheet; die Regeln der gefärbten Topbar selbst
entstehen beim Einbau und lesen nur die zwei Werte.

---

## 3a. Anmeldung, Übergang und Sitzung

**Anmeldung** (`.au-login`) — eine Karte auf ruhigem Grund, keine Werbefläche, kein zweiter
Anmeldeweg im Blickfeld. Zentral steht der je Installation festgelegte
**Standard-Anmeldeweg** (`_security.md` §1): bei lokaler Anmeldung die Pflichtangaben
Benutzer und Kennwort, danach — wo ein zweiter Faktor eingerichtet ist — der **Einmalcode
als eigener Schritt** auf derselben Karte; bei Verzeichnisdienst-Anmeldung dessen einzelner
Knopf. Die **Firma der Installation** steht als feste Angabe auf der Karte — sie ist keine
Auswahl: wer in einer anderen Firma arbeiten will, ruft deren Installation auf
(`_data.md` §5). Weitere aktive Wege
(Verzeichnisdienst bzw. lokale Konten, Chipkarte, Notfallzugang) liegen in einem Untermenü
(`.au-login__more`); der Notfallzugang ist dort ausdrücklich als protokollierter Sonderweg
benannt und **nie** auf der Maske selbst sichtbar.

Die Fußzeile (`.au-login__foot`) nennt dauerhaft den Zustand der Umgebung — Anmeldedienst,
Datenbank, letzte Sicherung — jeweils als Punkt + Wort + Wert, dazu die Versionsangabe. Das
ist die erste Frage jedes Supportfalls und darf nicht erst nach dem Anmelden sichtbar werden.

**Übergang** (`.au-splash`) — die Wartezeit nach dem Anmelden zeigt, *was* geschieht: eine
Fortschrittsanzeige und die benannten Ladeschritte (Rechte, Favoriten), dazu eine
kurze Begrüßung. Ein Übergangsbild ist erlaubt; es liegt als Ressource im Projekt, ist rein
gestalterisch und trägt keine Information. Der Übergang endet, sobald die Startseite bereit
ist, und wird bei `prefers-reduced-motion` durch das ruhige Standbild ersetzt.

**Einen Firmenwechsel gibt es nicht** — je Installation genau eine Firma. Die Topbar zeigt
die Firma dauerhaft, damit jederzeit erkennbar bleibt, in welcher Installation man
arbeitet, wenn mehrere Firmen eines Kunden parallel geöffnet sind. Der Weg in eine andere
Firma ist der Aufruf von deren Installation, nie ein Umschalter in der Anwendung.

**Sitzung** — Vor der Leerlaufabmeldung warnt eine nicht-modale Meldung (`.au-toast--warn`) mit
Restzeit und einer Aktion zum Weiterarbeiten. Bricht die Verbindung, erscheint dieselbe
Meldungsform mit Wiederverbindungsstand statt der Framework-Standardeinblendung; sie nennt den
Zustand als Wort, blockiert die Oberfläche nicht und verschwindet erst nach erfolgreicher
Verbindung.

**Mehrere Fenster derselben Anmeldung** — Zwei Arbeitsgänge nebeneinander (etwa Stammdaten
und Belegerfassung) werden über ein **zweites Browserfenster** gelöst, nicht über einen
zweiten Arbeitsbereich in der Shell. Ein zweites Fenster ist **kein zweiter Login**: die
Anmeldung liegt im Cookie, es entsteht allein eine zweite Sitzung (`_security.md` §1.4) —
kein Anmeldeschritt, keine Firmenangabe.

- **Jedes Fenster ist eine eigenständige Shell** — eigene Tab-Leiste, eigenes Sprungfeld,
  eigener Besuchsverlauf (§4a), eigene Leerlaufwarnung. Es gibt keinen geteilten Zustand
  und keine Synchronisierung der Tabs: zwei Fenster sind zwei Arbeitsplätze derselben
  Person, nicht ein aufgeteilter.
- **Das Ziel wird gezielt ins neue Fenster geöffnet** — `Strg+Alt+Enter` (§5.2) oder der
  Menüeintrag im Panelkopf. Die Übergabe läuft ohne Adresszeile (§4a, §10); ohne sie
  startet das neue Fenster auf der Startseite und müsste von Hand nachnavigiert werden.
- **Der Fenstertitel nennt die vordere Ansicht** — Modulnummer und Modulname, bei einem
  geöffneten Satz zusätzlich dessen Nummer. Ohne das heißen alle Fenster gleich und die
  Fensterliste des Betriebssystems ist unbrauchbar.
- **Der Leerlauf zählt je Sitzung** (`_security.md` §1.4). Ein Fenster, in dem lange nicht
  gearbeitet wurde, läuft für sich ab und zeigt seinen Zustand selbst; das andere bleibt
  unberührt. Untätigkeit als Aktivität zu zählen, nur weil daneben ein zweites Fenster
  offen ist, würde die Leerlaufabmeldung aushebeln.

> **Nicht Bestandteil:** zwei Module nebeneinander **in einem** Fenster, also ein zweiter
> Arbeitsbereich in der Shell. Er wäre die schönere Lösung, greift aber in §1.1, §3, §4,
> §4a, §5, §6, §10 und das Stylesheet ein, und je Bereich bliebe nur die
> Vollbreite-Ansicht — `--au-list-min-w` und `--au-detail-min-w` passen auf einem üblichen
> Arbeitsplatzbildschirm nicht zweimal nebeneinander. Wächst der Bedarf über einzelne
> Anwender hinaus, ist das keine Nachbesserung, sondern ein Eingriff in die Shell — dann
> über eine ADR.

**Nicht-modales Feedback** (`.au-toast`) — drittes Rückmeldeformat für abgeschlossene
Hintergrundvorgänge („gespeichert", „Ausgabe fertig"): kurz, am Rand, selbstschließend, mit
höchstens einer Aktion. Alles, was eine Entscheidung braucht, bleibt Dialog; alles, was einen
Satz betrifft, bleibt Feldmeldung.

**Fortschritt** (`.au-progress`) — Hintergrundvorgänge zeigen ihren Stand als schmale Leiste
mit Prozentwert, nie als unbestimmter Kreis. Erledigte Vorgänge bleiben bis zur Kenntnisnahme
sichtbar.

---

## 4. Navigation

**Sprungfeld** (`.au-jump`; in Topbar und Sidebar kompakt, auf der Startseite groß)
- Ziffern → Modulnummer, Bestätigen öffnet. Buchstaben → Fuzzy-Suche über Module,
  Datensätze, Aktionen, Berichte.
- Treffer erscheinen direkt darunter (`.au-jumphits`), der erste ist vorausgewählt.
- Bestätigen öffnet im aktuellen Tab, mit Zusatztaste in einem neuen Tab, mit der
  zweiten Zusatztaste in einem neuen Fenster (§5.2, §3a).
- Dasselbe Feld ist als Overlay-Palette (`.au-palette`) erreichbar; Gruppen in der
  Reihenfolge Datensätze → Module → Aktionen.

**Favoriten** — frei belegbar, mit Zifferkürzeln erreichbar (§5.2), sichtbar in der Sidebar
und als Kacheln auf der Startseite.

**Modulübersicht** — Mechanik: einstellige Gruppen `1…9`, zweistellige Modulnummern der
Form `1.1`; die Nummer ist immer sichtbar, weil sie das gemeinsame Vokabular von Support,
Schulung und Dokumentation ist. Welche Gruppen es gibt und wie sie heißen, legt das
Projektprofil fest — nicht dieser Leitfaden.

**Startseite** (`.au-start`) — Inhalt des ersten und jedes leeren Tabs: kurze Begrüßung mit
Tagesstand, großes Sprungfeld, Favoriten als Kacheln, vollständige Modulliste mit
Vorschau der Untermodule.

**Fußzeile der Sidebar** (`.au-navfoot`) — unten in der Sidebar, bleibt beim
Blättern der Modulliste stehen. Zwei Zustände, umschaltbar über ein Symbol, der gewählte
Zustand benutzerbezogen gespeichert:

1. **Zeitanzeige** — Uhrzeit, darunter Wochentag, Datum und Kalenderwoche in einer Zeile.
2. **Monatsübersicht** (`.au-mini-cal`) — Kalenderwoche als **erste Spalte**, weil sie in
   Terminen, Planung und Auswertungen das gemeinsame Vokabular ist; Nachbarmonate blass, der
   heutige Tag als gefüllte Akzentfläche (nicht nur als Kontur), Monate über Pfeile
   umschaltbar, Rücksprung auf den heutigen Monat als benannte Aktion.

Wochenbeginn, Wochentagskürzel und Datumsformat folgen der Kultur der Installation, nicht der
des Browsers. **Die Zeit kommt vom Server**, nicht vom Arbeitsplatz — sonst weichen Anzeige und
Zeitstempel voneinander ab. Die Übersicht ist eine **Anzeige, kein Planer**: keine Einträge,
keine Auswahl, keine Mehrfachmarkierung; **anklickbar ist die Anzeige als Ganzes, nie die
einzelne Tageszelle** — sonst widersprechen sich „keine Auswahl" und der Sprung ins Modul.
Umschalten und Monatswechsel sind keine Navigation und erzeugen keinen Eintrag im
Besuchsverlauf (§4a).

**Der heutige Tag** trägt zwei Merkmale, nicht nur eines: die gefüllte Akzentfläche **und** eine
von allen anderen Zellen abweichende Form. Zusätzlich nennt die Fußzeile der Übersicht das Datum
als Wort — Farbe allein ist kein Bedeutungsträger (§9).

**In der Rail entfällt die Fußzeile.** Wird die Sidebar zur Nummern-Rail (§3), verschwindet sie
ganz, statt zu schrumpfen; sie ist wie die Vorschauspalte (§6a) nie Pflicht und wird bei knapper
Breite als erstes abgeworfen. Ihr Inhalt wird nicht in das Flyout verschoben.

**Anzeigeelemente, die auf ein Modul verweisen.** Eine Anzeige in Shell, Fußzeile oder
Statuszeile darf als Sprung in das zuständige Modul dienen (Zeit- und Datumsanzeige in ein
Terminmodul, Bestandszahl in ein Bestandsmodul, Meldungszähler in einen Arbeitsvorrat).
Dafür gilt:

- Der Sprung öffnet das Modul **als Tab** wie jeder andere Aufruf (§1.1) — kein Sonderfenster,
  kein Überlagern der Sidebar.
- **Existiert das Zielmodul nicht, ist das Element nicht anklickbar** und zeigt keinen Zeiger
  und keine Hoverfläche. Eine Fläche, die aussieht wie ein Ziel und keines hat, ist ein Fehler.
- Die Anzeige bleibt auch ohne Ziel vollständig nutzbar; ihr Zweck ist die Information, der
  Sprung ist Zugabe. Kein Modul wird eingeführt, weil eine Anzeige darauf verweisen möchte.

---

## 4a. Besuchsverlauf (Browser-Zurück und -Vorwärts)

Die Zurück- und Vorwärtsfunktion des Browsers (Symbolleiste, `Alt+←`/`Alt+→`, Seitentasten
der Maus) bedient den **Besuchsverlauf** der Anwendung: Zurück macht die zuvor aktive
Ansicht wieder aktiv — den Modul- oder Datensatz-Tab, der vorher vorn war, gleich in
welchem Tab. **Shell-Tabs bleiben dabei offen; es wechselt nur, was vorn ist.** Zurück
schließt nichts, öffnet nichts und verwirft nichts. Das ist gewollt: die Zurücktaste ist
die am tiefsten gelernte Geste des Browsers — eine Anwendung, die sie ins Leere laufen
lässt, verliert Vertrauen; eine, die damit Tabs schließt, verliert Arbeit.

**Ein Besuch, ein Eintrag.** Ein Verlaufseintrag entsteht bei jedem gezielten Wechsel der
aktiven Ansicht:
- Modul öffnen (Sprungfeld, Favorit, Modulübersicht, Verweis, neuer Tab mit Startseite),
- Datensatz gezielt öffnen (`Enter`/Doppelklick in der Liste, Verweis, Sprungfeld,
  `Alt+Enter`),
- Tabwechsel (Klick auf den Tab oder `Strg+Tab`).

**Kein Eintrag entsteht bei Bewegungen innerhalb einer Ansicht:**
- Satzwechsel per Pfeiltaste oder `Strg+↑`/`Strg+↓` in der Split-View (§1.2) — Blättern
  ist kein Besuch,
- Filter-, Such- und Sortieränderungen,
- `F6` Split-View ⇄ Vollbreite — derselbe Screen (§3),
- Umschalten der Sidebar-Fußzeile und Monatswechsel in ihrer Übersicht (§4),
- der Tab, der nach dem Schließen eines anderen automatisch aktiv wird.

Zwei aufeinanderfolgende Einträge derselben Ansicht verschmelzen zu einem.

**Zurück fragt nicht nach.** Der Ansichtswechsel ist verlustfrei: ungespeicherte Sätze
bleiben je Tab erhalten und sind mit Vorwärts oder Tabklick sofort wieder da (§1.8). Eine
Rückfrage gäbe vor, dass etwas verloren ginge — das wäre falsch. Der `beforeunload`-Schutz
beim Verlassen der Anwendung bleibt davon unberührt Pflicht (§5.3, §10).

**Geschlossene Ansichten werden übersprungen.** Zeigt ein Eintrag auf einen inzwischen
geschlossenen Tab, wird er in Laufrichtung übersprungen, bis eine offene Ansicht erreicht
ist; bleibt keine, ist Zurück wirkungslos. Das Schließen eines Tabs ist eine ausdrückliche
Entscheidung — Zurück macht sie nicht stillschweigend rückgängig, und ein neu geladener
Satz wäre nicht der besuchte Ort, sondern nur dessen Adresse (vgl. §5.1: ohne Bezugsobjekt
wirkungslos statt Ersatzaktion). Wer den Satz wieder braucht, hat das Sprungfeld.

**Der Verlauf beginnt in der Anwendung.** Ein Wächter-Eintrag beim Sitzungsaufbau sorgt
dafür, dass Zurück die Anwendung nie unbeabsichtigt verlässt: am Anfang des Verlaufs ist
Zurück wirkungslos. Eine Taste, die mal die Ansicht wechselt und mal die Sitzung beendet,
ist nicht lernbar. Verlassen wird die Anwendung über das Schließen des Browser-Tabs; bei
geändertem Zustand greift der `beforeunload`-Schutz.

**Jedes Fenster hat seinen eigenen Verlauf.** Wird ein Ziel in einem neuen Fenster
geöffnet (§3a), entsteht im öffnenden Fenster **kein** Eintrag — dort wechselt die
aktive Ansicht nicht. Das neue Fenster beginnt mit seinem eigenen Wächter-Eintrag, die
übergebene Ansicht ist sein erster Besuch; Zurück führt dort nie in das öffnende
Fenster zurück.

**Keine URLs.** Der Verlauf ist rein intern: die Adresszeile ändert sich nicht, es gibt
keine Deep-Links und keine Lesezeichen — die Orte der Anwendung heißen Modulnummern, und
ihre Adresszeile ist das Sprungfeld (§4). Neuladen (außerhalb der Anwendung ausgelöst,
§5.3) startet wie bisher auf der Startseite; Verlaufseinträge der vorigen Sitzung sind
dann nicht mehr auflösbar und werden übersprungen. Auch die Übergabe an ein neues
Fenster (§3a) läuft ohne Adresszeile: sie ist eine Nachricht zwischen zwei Fenstern
derselben Anwendung (§10), kein Deep-Link, und lässt sich nicht als Lesezeichen
ablegen.

---

## 5. Tastaturbelegung (systemweit gleich)

Eine Taste bedeutet in jedem Modul dasselbe. Was ein Modul nicht anbietet, ist deaktiviert
(im Menü grau sichtbar) — nie anders belegt.

### 5.1 Funktionstasten

| Taste | Wirkung | Gilt in |
|---|---|---|
| `F1` | **Hilfe** — kontextsensitiv: Fokus im Feld → Feldhilfe, sonst Masken-/Modulhilfe | überall |
| `F2` | *frei — reserviert, nicht modulspezifisch belegen* | — |
| `F3` | **Neu anlegen** (leerer Satz im aktuellen Modul) | Liste, Maske |
| `F4` | **Löschen** — immer mit Rückfrage-Dialog, nie sofort | Liste, Maske |
| `F5` | **Auswahl öffnen** — Wertehilfe zum fokussierten Feld; Übernahme mit `Enter`, Abbruch mit `Esc` | Feld mit Bezug |
| `F6` | **Split-View ⇄ Vollbreite** | Liste |
| `F7` | Fokus **Suchen und Filtern** der Liste (gleichwertig zu `Strg+F`) | Liste |
| `F8` | **Duplizieren** (Satz als Vorlage, Nummer bleibt leer) | Liste, Maske |
| `F9` | **Vorgang starten** — die wichtigste Folgeaktion des Moduls | Liste, Maske |
| `F10` | **Speichern und schließen** (speichert und schließt den Satz-Tab) | Maske |
| `F11` | **Details anzeigen** — Detailbereich/Unterpositionen zum markierten Satz ein-/ausblenden | Liste, Maske |
| `F12` | *unbelegt* — bleibt den Entwicklerwerkzeugen des Browsers | — |

`F5` und `F11` sind die einzigen Tasten, die ein Feld bzw. eine Zeile als Bezug brauchen.
Ohne Bezugsobjekt bleiben sie wirkungslos statt eine Ersatzaktion auszulösen.

Es gibt **keinen Anzeige-/Änderungsmodus** und folglich keine Taste dafür: Maskenfelder sind
immer editierbar (§7), eine Änderung markiert die Feldkontur (`--au-input-bg-dirty`),
gespeichert wird ausschließlich explizit (`Strg+S` / `F10`), verworfen mit `Esc`.

### 5.2 Übrige Tasten

| Taste | Wirkung |
|---|---|
| `Enter` | **in Listen und Auswahl:** Satz übernehmen und öffnen · **in Rückfragen:** bestätigen · **in Masken:** ein Feld weiter (wie `Tab`) |
| Doppelklick | **öffnet in jeder Tabelle den Satz** — gleichwertig zu `Enter`, ausnahmslos und in jedem Modul |
| `Alt+Enter` | Satz in neuem Tab öffnen |
| `Strg+Alt+Enter` | Satz oder Modul in **neuem Fenster** öffnen (§3a) |
| `Tab` / `Shift+Tab` | Feld weiter / zurück |
| `Esc` | Änderung verwerfen, Auswahl/Overlay/Rückfrage schließen |
| `Strg+S` | **Speichern**, Satz bleibt geöffnet |
| `Strg+F` | Fokus **Suchen und Filtern** der Liste (gleichwertig zu `F7`) |
| `Strg+P` | **Drucken / Beleg-Vorschau** der aktuellen Ansicht bzw. des Satzes |
| `Strg+E` | **Exportieren** der aktuellen Ansicht |
| `Strg+K` | Sprungfeld / Palette |
| `Alt+1…9` | Favorit öffnen |
| `Strg+↑` / `Strg+↓` | vorheriger / nächster Satz (Liste bleibt stehen) |
| `Strg+Tab` | nächster Tab |
| `Alt+←` / `Alt+→` | Besuchsverlauf zurück / vorwärts (§4a) — Browserfunktion, wird nicht abgefangen |
| `Strg+Shift+…` | Reserve für modulspezifische Kürzel |

**`Enter`-Regel.** `Enter` bestätigt überall dort, wo eine Entscheidung ansteht: Satz in
einer Liste oder in einer `F5`-Auswahl übernehmen, Rückfrage bestätigen, Treffer im
Sprungfeld öffnen.

Nur **innerhalb einer Maske bei der Felderfassung** bestätigt es nicht, sondern springt ein
Feld weiter — dort löst `Enter` **niemals** Speichern oder Absenden aus. Beim letzten Feld
einer Gruppe springt `Enter` in die nächste Gruppe, am Maskenende zurück zum ersten Feld.

Ausnahme: **mehrzeilige Felder** (`.au-field--multiline`). Dort erzeugt `Enter` einen
Zeilenumbruch, verlassen wird das Feld nur mit `Tab`/`Shift+Tab`. Das Feld muss sichtbar
mehrzeilig sein, damit die abweichende Tastenwirkung erkennbar ist.

### 5.3 Regeln

- Module dürfen diese Belegung **nicht** überschreiben; eigene Kürzel ab `Strg+Shift+…`.
  `F2` und `F12` bleiben frei — auch nicht modulspezifisch belegen. `Strg+Alt` gehört
  der Shell (§3a) und bleibt für Module gesperrt.
- Der Doppelklick ist die einzige Mausgeste mit fester Bedeutung; weitere Gesten (Ziehen,
  Rechtsklick-Menü) dürfen sie nicht überschreiben.
- Kürzel stehen **am Auslöser der Funktion**: im Button (`.au-btn__key`), im Filterfeld
  (`.au-search__key`), im Navigationseintrag (`.au-navitem__key`) und rechtsbündig im
  Burger-Menü (`.au-menu__key`). Keine Sammelleiste in Listenfuß oder Statuszeile — dort
  steht Datenkontext.
- Nicht verfügbare Funktionen erscheinen im Menü grau (`aria-disabled`) statt zu
  verschwinden; die Position bleibt lernbar.
- **`F9` wird immer ausgeschrieben.** Es ist die einzige Taste mit modulabhängiger Wirkung,
  deshalb nennt der Button den konkreten Vorgang statt „Vorgang". Hat ein Modul mehrere
  gleichrangige Vorgänge, öffnet `F9` eine kleine Auswahlliste in fester Reihenfolge statt
  einen davon zu raten.
- **`F4` löscht in zwei Tasten** (`F4`, dann `Enter` in der Rückfrage) und liegt neben `F3`
  Neu anlegen. Der Dialog muss deshalb **Nummer und Bezeichnung des betroffenen Satzes
  ausschreiben** — nicht „Wirklich löschen?", sondern der Satz im Klartext. Nur so ist ein
  Fehlgriff sichtbar, bevor der Bestätigungsreflex greift. `Esc` bricht ab.
- `F1`, `F5`, `F11`, `Strg+F`, `Strg+S`, `Strg+P` sind im Browser vorbelegt (Hilfe,
  Neuladen, Vollbild, Seitensuche, Seite speichern, Drucken). Die Shell fängt sie ab
  (`preventDefault`); die Browserfunktionen bleiben über das Browsermenü erreichbar. Das ist
  gewollt — die Anwendung wird wie eine Vollbild-Fachanwendung bedient.
- Zurück und Vorwärts des Browsers werden **nicht** abgefangen: sie bedienen den
  Besuchsverlauf (§4a). Sie sind die einzigen Browserfunktionen mit Bedeutung **in** der
  Anwendung; kein Modul belegt `Alt+←`/`Alt+→` anders.
- `preventDefault` greift nur bei Fokus in der Anwendung. Wird außerhalb neu geladen, gehen
  ungespeicherte Sätze verloren; deshalb ist ein `beforeunload`-Schutz bei geändertem
  Zustand **Pflicht, nicht optional** (§10).

---

## 6. Datenliste (`.au-table`)

- **Spaltensatz Split-View: fünf Spalten** — Nummer · Bezeichnung (mit Kürzelmarke und
  Typkürzel) · Ort/Kontext · eine Kennzahl (rechts) · Status. Mehr Spalten gehören in die
  Vollbreite-Ansicht.
- **Spaltensatz Vollbreite: bis neun Spalten** — zusätzlich Kontaktdaten, weitere
  Kennzahlen, Konditionen. Spaltenbreiten je Modul über `--au-cols` (Grid-Definition am
  Panel), nicht in der Komponente hart.
- Kopfzeile: uppercase, `--au-fs-key`, Farbe `--au-label`. **Kein Zebra** — Trennlinie
  `--au-line-soft`, Hover `--au-panel-hover`, Auswahl `--au-accent-row` mit Nummer in
  Akzentfarbe.
- **Zeilenhöhe fest** (`--au-row-h`, dichte Variante über `.au-dense`), weil jede Liste
  virtualisiert wird. Jede Zelle einzeilig mit Ellipsis — nie umbrechen.
- Kopfzeile des Panels: Titel und Satzzahl links; rechts die Primäraktion (`F3`), eine
  zweite Aktion (`F4`), dann das Burger-Menü.
- Filterzeile: ein Suchfeld (`.au-search`, `F7`) + entfernbare Chips (`.au-chip`) +
  „Ansicht speichern". Gespeicherte Ansichten sind benutzerbezogen und teilbar.
- Fußzeile (`.au-panel__foot`): Summen der numerischen Spalten, Filterzahl, Satzposition —
  keine Tastenlegende.
- **Doppelklick auf eine Zeile öffnet immer den Satz** (§5.2) — in jeder Liste, jeder Auswahl und
  jedem Positionsraster, ohne Ausnahme und ohne Modifikatortaste. Einfacher Klick markiert nur.
  Kein Modul belegt den Doppelklick anders (nicht mit Inline-Bearbeitung, nicht mit Aufklappen);
  wo eine Zeile nichts zu öffnen hat, bleibt er wirkungslos. Der Listenfuß nennt ihn als Hinweis.
- **Gruppen innerhalb einer Listenart:** Ist eine Liste fachlich unterteilbar, erscheinen die
  Gruppen eingerückt unter der Art in der Auswahlspalte mit eigener Anzahl, sind eine eigene
  Listenspalte (Punkt + Wort, wie Status) und gleichzeitig Filter- und Gruppierkriterium. Sätze
  ohne Gruppe bilden eine eigene, sichtbare Gruppe — sie verschwinden nicht.
  **Gruppen sind frei definierbar, werden aber nicht in der Arbeitsmaske verwaltet:** Anlegen,
  Umbenennen, Sortieren und Deaktivieren geschieht ausschließlich im Verwaltungsmodul (§4,
  Gruppe 9). Die Arbeitsmaske zeigt Gruppen nur an — kein „Gruppe anlegen"-Verweis, kein
  Inline-Umbenennen, kein Kontextmenü dafür. Das gilt allgemein für Einordnungsmerkmale
  (Gruppen, Arten, Kennzeichen): **Stammdaten der Ordnung gehören in die Verwaltung, nicht in
  die Liste, die sie benutzt.**
- Massenaktionen erst nach Mehrfachauswahl einblenden, nie dauerhaft.

---

## 6a. Merkmalsfilter, Bezugskontext und Vorschau

Für Listen mit vielen Sätzen und vielen Merkmalen reicht ein Suchfeld nicht. Drei Bausteine,
die zusammen eine Auswahlmaske ergeben:

**Merkmalsfilter** (`.au-facets`) — eigene Spalte links der Liste, Breite `--au-facets-w`.
Je Merkmal ein Abschnitt mit Mehrfachauswahl und Trefferzahl je Wert. Innerhalb eines Merkmals
gilt **oder**, zwischen Merkmalen **und**; hat ein Satz mehrere Positionen, genügt eine
passende. Gesetzte Werte erscheinen zusätzlich als entfernbare Chips über der Liste (§6), damit
niemand nach der Ursache eines leeren Ergebnisses sucht. Zurücksetzen betrifft nur die Filter.

**Volltextsuche** — durchsucht ausdrücklich benannte Felder; welche das sind, steht als Satz im
Feld selbst, nicht in der Hilfe.

**Bezugskontext** — Legt ein Geschäftspartner fest, welche Sätze überhaupt wählbar sind, ist das
**kein Filter unter vielen**, sondern eine eigene Auswahl im Kopf der Liste. Sie bleibt beim
Zurücksetzen der Filter aktiv, nennt in einem Satz, was sie einschließt, und ist beim Aufruf aus
einem Vorgang vorbelegt und nicht änderbar.

**Vorschauspalte** (`.au-context`) — rechts der Liste, Breite `--au-context-w`: der markierte
Satz mit Bild, Bezeichnung und den Merkmalen, die die Auswahl entscheiden. Wurde die Liste aus
einem Vorgang heraus geöffnet, steht hier die Übernahmeaktion neben dem Öffnen. Dieselbe Spalte
trägt in Masken den Arbeitskontext (Kennzahlen, Verlauf, Bestände) — sie ist nie Pflicht und
entfällt als erste, wenn die Breite knapp wird (§3).

---

## 7. Datensatzmaske (`.au-record__*`)

Aufbau von oben nach unten:

1. **Identität** — Kürzelmarke, Bezeichnung, Nummer, bis vier Meta-Werte.
2. **Aktionszeile** (`.au-record__actions`, rechtsbündig) — die Aktionsleiste
   (`.au-actionbar`): Speichern (primär), die `F9`-Folgeaktion ausgeschrieben,
   Burger-Menü. Eine **eigene Zeile**: neben der Identität oder den Tabs passt die
   Leiste nicht — das einzige schrumpfbare Kind wäre die Identitätsspalte, und der
   Satzname würde gekappt. Die Zeile kostet Höhe und beendet den Breitenkonflikt,
   statt ihn zu verschieben.
3. **Tab-Zeile** — Kern-Tab zuerst, dann Beziehungen, dann der branchenspezifische Tab,
   dann Dokumente/Notizen.
4. **Kennzahlzeile** — vier Karten (`.au-kpi`) mit den Zahlen, die man beim Telefonat
   zuerst braucht.
5. **Feldgruppen** — maximal drei je Tab; passt es nicht, ist es ein eigener Tab.
   Beispielmuster (generisch, nicht bindend): Grunddaten · Kontakt · Konditionen.
6. **Statuszeile** (`.au-statusbar`) — Statuspunkt + Wort, Zeitstempel, Benutzer; rechts der
   Satzkontext.

**Feldraster.** **Zwölf Spalten sind fix.** `--au-field-cols` existiert nur, um dieselbe
Zahl an einer Stelle zu halten (Grid und Modifier), nicht als Konfigurationspunkt — die
Spannweiten-Modifier setzen zwölf Spalten voraus. Standardfeld ist ein Drittel der Breite
(vier Spalten). Spannweite folgt dem **Inhalt**, nicht der Symmetrie: ein Wert darf in
seiner Box nie abgeschnitten werden. Modifier:

| Klasse | Anteil |
|---|---|
| `.au-field--2` | ein Sechstel |
| `.au-field--3` | ein Viertel |
| `.au-field--4` (= Standard) | ein Drittel |
| `.au-field--5` | fünf Zwölftel |
| `.au-field--6` | halbe Breite |
| `.au-field--7` | sieben Zwölftel |
| `.au-field--8` | zwei Drittel |
| `.au-field--9` | drei Viertel |
| `.au-field--10` | fünf Sechstel |
| `.au-field--11` | elf Zwölftel |
| `.au-field--12` | volle Breite |

Die Skala ist **vollständig von 2 bis 12** — eine ungenannte Stufe fiele im Grid still
auf die Standardbreite zurück, und die Spannweite wäre viel zu schmal, ohne dass etwas
auffällt.

**Feldbox** (`.au-field__box`) — **jedes Eingabefeld ist als Feld erkennbar, auch leer.**
Label über einer Box mit sichtbarer Kontur (`--au-line-input`) auf `--au-input-bg`. Kein
randloses Feld, kein reiner Unterstrich. Leer heißt *leer mit Platzhalter*
(`--au-input-ph`), nicht unsichtbar; der Platzhalter beschreibt, was hineingehört, und
wiederholt nie das Label. Zustände:

| Zustand | Darstellung |
|---|---|
| Hover | Kontur `--au-line-input-hover` |
| Fokus | Kontur `--au-line-focus` + `--au-focus-ring-soft` |
| geändert | Kontur `--au-warn`, Fläche `--au-input-bg-dirty` |
| fehlerhaft | Kontur `--au-error`, Fläche `--au-input-bg-invalid`, Label rot, Meldung darunter (`.au-field__msg`) |
| gesperrt/berechnet | Fläche `--au-input-bg-ro`, Symbol in `.au-field__adorn` — als **Zustandsmarke** in `--au-label`, nicht im blassen Symbolton (§9) |
| Auswahlfeld | Chevron in `.au-field__adorn` |
| mehrzeilig | `.au-field--multiline`, mindestens doppelte Boxhöhe |

Pflichtfelder tragen kein Sternchen; fehlende Werte werden erst beim Speichern markiert.

**Burger-Menü** (`.au-btn--burger` + `.au-menu`) — Button mit drei gleich dicken Strichen
(`--au-burger-line-*`; ganze Pixel, weil halbe je Strich unterschiedlich runden), kein
Auslassungszeichen. Geöffnet ist der Button hinterlegt. Das Menü trägt eine Überschrift,
zeigt Kürzel rechtsbündig und hat eine **feste Reihenfolge**: Speichern und schließen
(`F10`) · Duplizieren (`F8`) · Details anzeigen (`F11`) · Trenner · Drucken (`Strg+P`) ·
Exportieren (`Strg+E`) · modulspezifische Vorgänge · Trenner · Löschen (`F4`, rot, immer
letzter Eintrag). Maximal neun Einträge; mehr heißt: ein Eintrag gehört in einen Tab.

---

## 7a. Belegmaske (Kopf + Positionsraster)

Belege sind das zweite Grundmuster neben der Datensatzmaske: ein Kopf mit wenigen Feldern,
darunter ein editierbares Positionsraster, rechts der Summenblock.

**Der Kopf ist ein eigener, sichtbar abgegrenzter Block** (`.au-dochead`): abgesenkte
Fläche, Kontur, Radius, darüber der Gruppentitel und ein Satz, was hier geregelt wird. Ohne
diese Abgrenzung verschwimmen Kopffelder und Positionsraster zu einer Fläche. Die Felder
darin folgen dem Feldraster aus §7. Raster darunter mit eigener Werkzeugzeile, Summenspalte
als eigenes Panel rechts (Breite `--au-sums-w`).

**Statusfluss.** Der Zustand steht als Kette im Kopf (`.au-statusflow`), nicht als einzelnes
Etikett: alle Schritte sind sichtbar, der erreichte ist hervorgehoben, spätere bleiben blass.
Daneben steht in einem Satz, was den nächsten Schritt blockiert.

**Editierbare Rasterzelle** (`.au-cellbox`). Anders als die Feldbox (§7) trägt sie **keine
Dauerkontur** — bei mehreren Dutzend Zellen wäre das ein Gitterrauschen. Erkennbar wird sie
durch Hover und Fokus; die Zustände sind dieselben wie beim Feld: fokussiert (Kontur Akzent +
weicher Ring), geändert (`--au-input-bg-dirty`), fehlerhaft (`--au-input-bg-invalid`, Wert in
Fehlerfarbe), gesperrt oder berechnet (`--au-input-bg-ro`). Berechnete Zellen wie die
Zeilensumme sind immer gesperrt. Unter der letzten Position steht dauerhaft eine leere Zeile
(`.au-grid__new`) mit dem Hinweis, wie eine Position entsteht.

**Tastaturfluss im Raster.** `Enter` und `Tab` gehen zur nächsten Zelle derselben Zeile, nach
der letzten Zelle in die erste Zelle der nächsten Zeile. Pfeiltasten bewegen zellenweise ohne
Übernahme. Das ist die Felderfassung aus §5.2 — `Enter` speichert auch hier nichts.

**Bezug der Funktionstasten.** Bezug ist immer der Fokus: liegt er im Raster, wirkt die Taste
auf die Position, sonst auf den Beleg.

Spalten des Rasters: Positionsnummer · Artikel · Bezeichnung · Menge · Einheit · abhängige
Mengengröße (z. B. Gewicht, berechnet und gesperrt) · Einzelpreis · Rabatt · Zeilensumme
(gesperrt) · Kontierung. Abhängige Mengengrößen werden auch im Summenblock summiert.

| Taste | im Raster | außerhalb des Rasters |
|---|---|---|
| `F3` | Position einfügen | neuer Beleg |
| `F4` | Position löschen | Beleg löschen (nur im ersten Status) |
| `F5` | Wertehilfe zur Zelle | Wertehilfe zum Feld |
| `F8` | Position duplizieren | Beleg als Vorlage duplizieren |
| `F9` | — (immer belegbezogen) | Vorgang: nächster Status bzw. Korrekturvorgang |
| `F11` | Detailzeile der Position | Detailbereich des Belegs |
| `Strg+S` | speichert Kopf **und** Positionen gemeinsam | dito |

Positionen werden nie einzeln gespeichert. `F3` und `F4` stehen zusätzlich als beschriftete
Buttons in der Werkzeugzeile des Rasters (mit Kürzel am Button, §1.4); weitere
Rasteraktionen liegen auf `Strg+Shift+…` (§5.3).

Diese Tabelle ist **Dokumentation, nicht Oberfläche**: eine Übersicht der Tastenwirkung wird
nie als Panel in die Maske gebaut. Sichtbar sind Kürzel ausschließlich am Auslöser (§1.4);
die vollständige Übersicht liefert die Hilfe (`F1`).

**Summenblock** (`.au-sums`). Reihenfolge fest: Einzelsummen · Zwischensumme (Trennlinie,
halbfett) · Steuer je Schlüssel · Endsumme (Trennlinie, größte Zahlenstufe). Alle Werte
monospaced und rechtsbündig. Blockiert etwas den nächsten Status, steht darunter ein
Inline-Hinweis (`.au-hint--warn`) mit dem konkreten Grund und der betroffenen Position.

**Vorschau des Bezugssatzes.** Rechts neben dem Raster steht optional ein Panel, das den
Satz der markierten Position zeigt (`.au-refcard`): Bild, Bezeichnung, Nummer und zwei bis
drei Kennzahlen, die bei der Erfassung entscheiden (Verfügbarkeit, Listenpreis, Frist). Es
wechselt mit der Markierung und ersetzt den Sprung ins Bezugsmodul. Fehlt ein Bild, bleibt
die Fläche als Platzhalter stehen — sie springt nicht.

**Ausgabe und Vorschau.** Drucken und Versenden sind Belegfunktionen und stehen als eigener
Block im Panelkopfbereich oder in der rechten Spalte, nicht im Burger-Menü versteckt.
`Strg+P` (§5.2) öffnet **nie** direkt den Druckdialog des Browsers, sondern die
Belegvorschau (`.au-preview`): Vorlage, Sprache und Anzahl stehen als Felder über dem Blatt,
das Blatt selbst zeigt die tatsächliche Ausgabe. Drucken und Versenden nutzen dieselbe
Vorlage. Solange der Beleg nicht freigegeben ist, tragen alle Blätter einen sichtbaren
Entwurfsvermerk (`.au-preview__draft`). Jede Ausgabe wird am Beleg protokolliert und ist
dort als Liste sichtbar; im gesperrten Endzustand bleiben Drucken und Versenden erlaubt.

**Endzustand.** Im letzten Status ist der Beleg vollständig gesperrt: keine Felder, keine
Positionen, kein Löschen — Drucken und Versenden ausgenommen. Dann greift `.au-doc--locked` — Feld- und Zellkonturen entfallen,
gelesen wird als Text statt als gesperrte Box, damit sichtbar ist, dass hier nichts mehr zu
tippen ist. Die Primäraktion wechselt auf den Korrekturvorgang, der einen Nachfolgebeleg
erzeugt; `F3`, `F4` und `Strg+S` erscheinen im Menü grau. Ein abgeschlossener Beleg wird
nie verändert.

## 7b. Wertehilfe (`.au-lookup`)

Ein Muster für alle Masken, ausgelöst mit `F5` (§5.1).

- Overlay über der abgedunkelten Maske (`.au-scrim`), Breite `--au-lookup-w`, oben
  ausgerichtet — nicht mittig, damit der Bezug im Hintergrund sichtbar bleibt.
- Titel nennt **Feld und Bezug**, rechts das Schließzeichen.
- Das Suchfeld hat den Fokus und ist mit dem vorbelegt, was im Feld schon getippt wurde.
- Die Ergebnisliste ist eine normale Datenliste (§6) mit **drei bis fünf
  entscheidungsrelevanten Spalten** — nie die vollständige Tabelle des Zielmoduls. Eine
  Spalte darf ein Status sein (Punkt + Wort).
- Unter dem Suchfeld steht in einem Satz, **welche Einschränkung** gilt: Trefferzahl und
  wirksamer Vorfilter.
- **Anlegen aus der Auswahl** (`F3`) ist Teil des Overlays: legt den Satz an und übernimmt
  ihn direkt in das Feld, ohne Umweg über das Zielmodul.
- **Genau ein Treffer wird sofort übernommen**, das Overlay öffnet dann nicht. Bei keinem
  Treffer öffnet es mit der Suche und dem Hinweis, dass nichts gefunden wurde — Anlegen ist
  dann die naheliegende Aktion.
- Übernehmen mit `Enter`, Abbrechen mit `Esc`; die Fußzeile sagt beides.

## 7c. Löschdialog mit Verwendungsnachweis

Zwei Varianten desselben Rückfrage-Dialogs (§7). Beide nennen **Nummer und Bezeichnung im
Titel** (§5.3).

1. **Löschbar** — ein Satz Folgetext (keinem Vorgang zugeordnet, wird protokolliert, nicht
   rücknehmbar), Aktionen Abbrechen · Löschen (`.au-btn--danger`, `Enter`).
2. **Verwendet** — kein „Löschen nicht möglich". Statt der Sackgasse zeigt der Dialog den
   **Verwendungsnachweis** (`.au-usage`): je Vorgangsart eine Zeile mit Modulnummer,
   Bezeichnung, gezählter Anzahl und einem Verweis, der die gefilterte Liste in einem neuen
   Tab öffnet. Die Primäraktion ist dann **nicht** Löschen, sondern der Ausweg
   „Deaktivieren": der Satz verschwindet aus Auswahlen und bleibt in bestehenden Vorgängen
   erhalten. `Enter` liegt damit auf dem sicheren Weg, die Löschaktion ist gar nicht
   vorhanden.

Der Verwendungsnachweis wird vor dem Öffnen des Dialogs ermittelt — es gibt keinen Dialog,
der erst nach dem Bestätigen scheitert.

## 7d. Strukturliste mit Gruppen und historisierten Werten

Sätze, die aus Bestandteilen bestehen (Positionen, Komponenten, Bestandteile einer Rezeptur),
liegen in **einem** Raster (§7a), nicht in mehreren Tabellen je Art. Fachliche Arten werden
durch **Gruppenzeilen** (`.au-grid__group`) getrennt, die Art, Anzahl, verdichtete Mengen und
die Zwischensumme nennen. Ein Raster hält den Tastaturfluss über den ganzen Satz durchgehend;
mehrere Tabellen brechen ihn an jeder Grenze.

- **Alle Arten teilen die Spalten.** Was eine Art nicht kennt, bleibt leer statt zu verschwinden.
  Die abweichende Berechnungsgrundlage einer Zeile (Stück, Gewicht, Maß) ist eine eigene Spalte.
- **Leere Arten bleiben sichtbar** mit dem Vermerk, dass keine Position erfasst ist — sonst ist
  nicht erkennbar, ob sie fehlt oder nicht vorgesehen ist.
- **Summen und verdichtete Merkmale werden gerechnet, nie gepflegt.** Gruppen-, Fuß- und
  Kopfwerte stammen aus denselben Positionen; kein zweiter Ort, an dem dieselbe Zahl steht.

**Historisierte Werte (Fixierung).** Wird ein Satz zu einem Stichtag festgeschrieben, stehen
**beide** Werte nebeneinander: der fixierte und der aktuelle, dahinter die Abweichung
(`.au-delta`). Fehlt eine Fixierung, zeigt die Spalte „–" statt eines Ersatzwertes.

- Fixiert wird **nicht nur der Betrag, sondern die Grundlage**: Kurse und Bezugsgrößen,
  Einzelwerte je Bestandteil und die Berechnungsart. Wird eine Bezugsgröße später umgestellt,
  bleibt der historische Wert dadurch stabil.
- Die Maske sagt **vor** dem Speichern, was fixiert wird — die Fixierung ist eine benannte
  Aktion mit Liste, kein stiller Nebeneffekt.
- Jeder frühere Stand bleibt als Verlauf lesbar (Zeitpunkt, Werte, Bezugsgrößen).
- Eine Abweichung über der im Projekt gesetzten Schranke erscheint als Inline-Hinweis
  (`.au-hint--warn`) im Fuß, mit Richtung und Betrag.

## 7e. Vererbte Zeilen aus einer Vorlage

Zeilen können aus einer Vorlage stammen und mit ihr verbunden bleiben. Die Verbindung ist im
Kopf des Rasters benannt, samt Wirkung („Änderungen an der Vorlage wirken sofort") und der
Aktion, sie zu lösen. Jede Zeile trägt ihren **Zeilenstatus** (`.au-rowstate`) als Punkt + Wort:

| Status | Bedeutung |
|---|---|
| Vorlage | unverändert übernommen |
| Geändert | lokal überschrieben; die Vorlage bleibt unberührt |
| Neu | nur in diesem Satz vorhanden |
| Entfernt | lokal ausgeblendet, durchgestrichen sichtbar und wiederherstellbar |

Bearbeiten erzeugt eine Abweichung, Löschen blendet aus — die Vorlage wird nie durch die
Verwendung verändert. Entfernte Zeilen verschwinden nicht, sonst ist die Abweichung zur Vorlage
unsichtbar. Trägt eine Zeile einen Wert, der aus mehreren Quellen stammen kann, nennt sie die
**Quelle des angezeigten Werts** in einer eigenen Spalte.

## 7f. Geltungsbereichskette (Ergebnis und Begründung)

Überall, wo ein Wert aus einer Rangfolge von Geltungsbereichen entsteht — je Partner, je Gruppe,
je Standard — wird **dasselbe Muster** verwendet (`.au-chain`), gleich ob es um Werte,
Konditionen, Zeiten oder Rechte geht:

1. **Ergebnis oben**, mit dem gewinnenden Bereich und dem Stichtag.
2. Darunter **jede geprüfte Stufe** in Rangfolge mit Zeichen und Wort: getroffen, nicht
   vorhanden, nicht herangezogen — und je Stufe der Grund.
3. Enger schlägt weiter; bei mehreren gültigen Einträgen gewinnt der zuletzt begonnene. Nur zum
   Stichtag gültige Einträge zählen; abgelaufene und künftige bleiben mit Zustand sichtbar.

Derselbe Text erscheint dem Bediener, wenn eine Aktion verweigert oder ein unerwarteter Wert
gezogen wird. Eine Rangfolge, die man nur der Dokumentation entnehmen kann, ist ein Fehler.

## 7g. Merkmalsmatrix und befristete Ausnahme

Für Zuordnungen zweier Achsen — Gruppe gegen Aktion, Rolle gegen Modul — gilt eine Matrix
(`.au-matrix`): Zeilen sind die Gegenstände, Spalten die immer gleichen Aktionen in fester
Reihenfolge. Zustände tragen **Zeichen und Wort** (Legende an der Matrix), nie nur Farbe:
erteilt, geerbt, verweigert, nicht anwendbar. Zusätzliche Grenzen (Obergrenzen, Gültigkeitsraum)
stehen als eigene Spalte, nicht als Fußnote.

Die Matrix zeigt die Regel; **wirksam** wird sie erst am einzelnen Bediener. Deshalb gehört zu
jeder Matrix eine Bedienersicht mit den wirksamen Zuständen aus allen Zuordnungen und eine
Prüffrage nach dem Muster aus §7f.

**Befristete Ausnahme.** Eine Abweichung von der Regel ist ein eigener, protokollierter Vorgang:

- **Grund und Frist sind Pflicht**; ohne beides bleibt die Aktion gesperrt.
- Erteilen darf nur, wer die Aktion selbst besitzt; jede Grenze kann enger, nie weiter sein.
- Vor Ablauf erinnert das System, danach entfällt die Ausnahme ohne Zutun.
- Beantragen geschieht **aus der verweigerten Aktion heraus** — die Meldung nennt den Grund und
  bietet den Antrag mit vorbelegtem Zusammenhang an.
- Erteilung, Verlängerung und Auslaufen stehen im Protokoll des betroffenen Satzes und sind dort
  lesbar, nicht nur in einem Systemprotokoll.

## 7h. Erzeugte Texte

Werden Bezeichnungen, Kurztexte oder Etiketten aus den Bestandteilen eines Satzes gebildet, sind
sie **sichtbar, aber nicht editierbar** (`.au-derived`, Fläche `--au-input-bg-ro`):

- Erzeugung nach jeder Änderung der Bestandteile, ohne Zutun.
- Verdichtung nach benannter Regel: je Kategorie das Merkmal der ersten Position, Anzahl und
  Mengen als Summe über alle Positionen der Kategorie.
- Feste Anzahl an Textfeldern; unbelegte Felder bleiben leer und sichtbar.
- Ein freier Zusatztext ist ein **eigenes** Feld, das die Erzeugung nicht überschreibt.

---

## 8. Ableitung eines neuen Moduls

1. Modulnummer festlegen und in eine bestehende Gruppe `1…9` einhängen.
2. Liste: fünf Spalten für den Split-View wählen — eine Nummer, eine Bezeichnung, ein
   Kontext, eine Kennzahl, ein Status. Grid-Definition am Panel (`--au-cols`) setzen.
3. Maske: Identitätszeile → höchstens vier Kennzahlen → höchstens drei Feldgruppen → Tabs.
4. Branchen- oder kundenspezifische Felder ausschließlich als zusätzlicher Tab.
5. Tastenbelegung aus §5 unverändert übernehmen; nur `F9` mit der wichtigsten Folgeaktion
   belegen — sie ist zugleich der zweite Button im Maskenkopf. `F2`/`F12` bleiben frei.
6. Panelkopf: maximal zwei ausgeschriebene Buttons plus Burger. Alles Weitere ins Menü.
7. Besteht der Satz aus Bestandteilen, ein Raster mit Gruppenzeilen wählen (§7d), nicht mehrere
   Tabellen; entsteht ein Wert aus einer Rangfolge, die Kette aus §7f verwenden.
8. Viele Merkmale in der Liste ergeben eine Filterspalte (§6a), nicht mehr Spalten im Raster.
9. Keine neuen Farben, Radien oder Schriftgrade — nur bestehende Tokens. Fehlt eine Rolle,
   wird ein Token ergänzt (und hier dokumentiert), kein Literal eingestreut.

---

## 9. Barrierefreiheit und Qualitätsschranken

- **Text ≥ 4,5:1**, Platzhalter eingeschlossen. Das gilt auch für **Zeichen mit Bedeutung** —
  Zustandsmarken („–", „✓", „✕"), Kürzelfolgen einer Vorgangskette, Haken in Auswahlfeldern,
  Vermerke auf Ausgaben: sie sind Text, nicht Zierde, und liegen deshalb nie unter
  `--au-fs-key` und nie in `--au-faint`. Nur `×`, Chevron und Trennzeichen dürfen blass sein;
  `--au-faint` ist ausschließlich für sie.
- **Bedienkonturen ≥ 3:1** gegen ihre Fläche. Bedienkontur ist die Linie, an der ein
  Bedienelement überhaupt als solches erkennbar ist: die Feldbox (§7 — auch leer als Feld
  erkennbar), das Auswahlkästchen, der Fokusring. Dort trägt die Linie die Information, also
  gilt die Schranke.
- **Trennlinien tragen keine Zahl.** Panelrand, Zeilentrenner und Abschnittslinie zeigen keine
  Bedienbarkeit, sondern gliedern; eine 3:1-Linie ergäbe dort ein anderes, unruhigeres Design
  als das gewollte. Und wo ein Bedienelement schon durch Beschriftung, Fläche oder Zeichen
  erkennbar ist — Button mit ausgeschriebenem Wort, Burger mit seinen Strichen —, ist sein Rand
  Gliederung und keine Bedienkontur. Sichtbar bleiben müssen beide.
- **Die Schranken gelten je Theme, nicht in der Vorlage.** Die Tokenwerte des Stylesheets sind
  gegen **seine** Flächen gerechnet. Ein Projekt, das Tokens überschreibt (`[ui] theme`),
  rechnet jedes Paar aus Schrift- und Flächenton gegen seine eigenen Flächen nach: ein auf Weiß
  knapp bestandener Ton fällt auf einer getönten Fläche durch. Deshalb werden Töne mit Luft zur
  Schranke gewählt, nicht auf die Nachkommastelle. Für einen Dunkelmodus gilt dasselbe, und
  beide Fassungen werden **gemeinsam** nachgezogen — eine einseitige Verbesserung lässt hell und
  dunkel auseinanderlaufen.
- Kein Inhalt allein über Farbe, Hover oder Tooltip. Status immer Punkt + Wort.
- `:focus-visible`-Ring ist Bestandteil des Systems und darf nicht entfernt werden;
  Tab-Reihenfolge folgt der Leserichtung, Listenzeilen sind mit Pfeiltasten erreichbar
  (`role="grid"`).
- Klickziele mindestens `--au-control-h`; in Touch-Kontexten mindestens `--au-touch-h`.
- Listen sind virtualisiert, deshalb feste Zeilenhöhe und einzeilige Zellen.
- Zielwerte Interaktion: erste Zeile einer Liste unter 300 ms, Satzwechsel unter 100 ms.
- Keine externen Ressourcen zur Laufzeit: keine CDN-Skripte, keine Web-Fonts von außen.
  Betrieb erfolgt in Netzen ohne Internetzugang und wäre sonst datenschutzrechtlich
  angreifbar.

---

## 10. Umsetzung in Livewire

- **Fußzeile der Sidebar** (§4): Das Stylesheet liefert Fläche, Form und Farbe; **das Markup
  liefert die Zeichen und Wörter** — Wochentagskürzel, Kalenderwochennummern, das Datum als
  Wort in der Fußzeile der Übersicht und das Zustandsmerkmal des heutigen Tages. Uhrzeit und
  Datum kommen aus einem Serverdienst, nicht aus der Zeit des Browsers; Format und Wochenbeginn
  aus der Kultur der Installation. Fehlt das Zielmodul einer Anzeige, setzt das Markup weder
  Verweis noch Zeiger.
- Stylesheet global einbinden (einmal im Shell-Layout); komponenteneigene Styles nur als
  Layout-Abstand innerhalb der Komponenten-View, nie für Farben, Größen oder Radien —
  sonst driften Module auseinander. Farben und Schriften kommen ausschließlich aus den
  Tokens (`§2`).
- Komponentenschnitte (Livewire-Komponenten + Blade-Partials): Shell, Tab-Host, Sprungfeld,
  Sidebar/Rail, generische Datenliste mit Spaltendefinition als Parameter und interner
  Virtualisierung, Satzkopf, Tab-Streifen, Feldgruppe, Feld (Label + Editor + Zustand),
  Kennzahlzeile, Aktionsleiste (Buttons + Burger; nimmt eine Aktionsliste mit Kürzel,
  Verfügbarkeit und Gefahrenkennzeichnung), Menü, Statuszeile, Rückfrage-Dialog.
  Diese Schnitte sind Plattformbausteine (`_design.md` §6), kein Modul baut eigene.
- **Tastaturlogik zentral in der Shell**: ein globaler `keydown`-Handler (Alpine.js im
  Shell-Layout) mit `preventDefault` für alle Funktionstasten außer `F12` sowie für die
  belegten `Strg`-Kombinationen (§5.2). Die Handler lösen Aktionen der aktiven
  Livewire-Komponente aus; Module melden nur ihre `F9`-Aktion(en) und welche Tasten im
  aktuellen Kontext verfügbar sind (steuert die graue Darstellung im Menü).
- **`beforeunload`-Schutz bei geändertem Zustand ist Pflicht** (§5.3).
- Zustand je Tab isoliert halten (eigene Komponenteninstanz je Tab), damit mehrere Sätze
  parallel offen sein können; Änderungsmerker je Tab für Tab-Markierung und `Esc`-Verhalten.
- **Besuchsverlauf ohne URLs (§4a):** direkt über `history.pushState`/`popstate` in einem
  kleinen Alpine-Baustein des Shell-Layouts — nicht über das Laravel-Routing und nicht über
  `wire:navigate` zwischen Fachansichten: die Shell bleibt **eine einzige Route**, und alle
  Ansichtswechsel darin laufen serverseitig über die aktive Komponente. Der Zustand im
  History-Eintrag ist ein interner Schlüssel (Tab-/Satzbezug), nie Fachdaten. Die Shell
  führt den Verlauf, löst Schlüssel gegen die offenen Tabs auf und überspringt unauflösbare
  Einträge in Laufrichtung (geschlossener Tab, neue Sitzung nach Neuladen); der
  Wächter-Eintrag entsteht beim Sitzungsaufbau. Eine eigene Tiefenbegrenzung braucht der
  Verlauf nicht: Einträge tragen nur Schlüssel, und der Browser begrenzt die Zahl seiner
  Einträge selbst. Kein Modul spricht mit der History — Module melden Besuche an die Shell,
  wie sie ihre Tastenverfügbarkeit melden. (`wire:navigate` ist dennoch die Vorgabe für
  einfache, adressierbare Nebenansichten außerhalb der Shell — etwa Hilfeseiten.)
- **Zweites Fenster ohne URL (§3a):** `window.open` auf dieselbe Shell-Adresse ohne
  Parameter, ausgelöst aus einer Benutzeraktion — sonst greift der Popup-Blocker. Das
  neue Fenster meldet über `window.opener` Bereitschaft, sobald seine Shell geladen ist; der
  Öffner antwortet mit dem Ziel. Herkunftsprüfung auf beiden Seiten ist Pflicht, und die
  Nachricht trägt denselben internen Schlüssel wie ein Verlaufseintrag (Tab-/Satzbezug),
  nie Fachdaten. Bleibt die Antwort aus — Öffner geschlossen, Nachricht verloren —,
  bleibt das neue Fenster auf der Startseite, ohne Fehlermeldung: die Startseite ist ein
  gültiger Zustand. Die Brücke liegt in der Shell; kein Modul spricht mit einem anderen
  Fenster.
- **Den Fenstertitel führt die Shell** (§3a): sie setzt ihn beim Wechsel der aktiven
  Ansicht auf Modulnummer, Modulname und — bei geöffnetem Satz — dessen Nummer. Kein
  Modul schreibt den Titel.
- Zahlen-, Datums- und Gewichtsformatierung zentral serverseitig (Kultur der Installation,
  eigene Blade-Auszeichner/Komponenten), nie mit Browser-Formatierung in der View.
- **Schrifteinbindung:** `@font-face` steht in der Theme-Datei des Projekts und lädt lokale
  Dateien; das Projekt **überschreibt** dort `--au-font`/`--au-mono` mit seinen
  Familiennamen. Die Platzhalter-Familien im Stylesheet werden nicht umbenannt und nicht als
  `@font-face`-Name verwendet.
- **Zustandsmarkierung des Tabs:** Das Stylesheet färbt nur; das **Zeichen liefert das
  Markup** — bei geändertem Zustand einen Punkt anstelle des Schließkreuzes (`au-tab--dirty`).
  Kein Modul weicht davon ab.
- **Verbindungsverlust und Leerlauf** werden mit eigenem Markup dargestellt (§3a): die
  eingebaute Offline-/Fehlerbehandlung von Livewire (Standard-Fehlertoast bei einem
  fehlgeschlagenen Request) wird durch den eigenen Wiederverbindungsstand ersetzt, nicht
  ergänzt — zentral in der Shell. Sitzungsrestzeit und Verbindungszustand kommen aus der
  Shell, nicht aus einzelnen Modulen.
- **Fortschritt und Abschluss von Hintergrundvorgängen** meldet der Server an die Shell
  (Warteschlangen-Ereignis, Abfrage beim nächsten Serverkontakt); die
  Anzeige (§3a) gehört der Shell, kein Modul baut eine eigene.
- **Datums- und Zeiteingabe** einheitlich über ein eigenes Feld mit derselben Feldbox (§7): keine
  Browser-Vorgabe, weil Aussehen, Kultur und Tastaturverhalten sonst je Browser abweichen.
  Eingabe ohne Trennzeichen ist zulässig, die Anzeige folgt der Kultur der Installation.
- **Fixierte Werte werden serverseitig festgeschrieben** (§7d), samt Bezugsgrößen und
  Berechnungsart; die Oberfläche zeigt sie, berechnet sie nie neu.
- Jede Klasse des Stylesheets ist in §12 aufgeführt; eine Klasse ohne Eintrag dort gilt als
  nicht vorhanden.
- Dunkelmodus später über `[data-theme="dark"]` mit identischen Tokennamen; in Komponenten
  stehen keine Literale, deshalb genügt der Tokentausch in der Theme-Datei.

---

## 11. Änderungsprotokoll

- **27.08.2026** — Vier Funde aus dem ersten Maskenentwurf eines Projekts:
  **Spannweiten-Skala vervollständigt** (`--4`, `--9`, `--10`, `--11`, §7) — eine
  ungenannte Stufe fiel im Grid still auf die Standardbreite zurück. **Aktionsleiste des
  Satzkopfs in eine eigene Zeile** (`.au-record__actions`, §7): neben der Identität oder
  den Tabs passt die 313 px breite Leiste nicht; das einzige schrumpfbare Kind war die
  Identitätsspalte, der Satzname wurde per Ellipsis gekappt. **Zeichen-Taste**
  (`.au-btn--icon`) als benannte Ausnahme in §2.4: quadratisch, nur für Nebenfunktionen,
  Bedeutung über `title` und `aria-label`. **Kennfarbe der Installation** (§3): einzige
  Ausnahme vom Verbot farbiger Navigationsflächen — färbt nur Topbar und Anmeldekarte,
  Kurzname als Wort daneben; Klassen `au-kennmark`, `au-swatch*`, `au-kennpreview*` samt
  Tokens, Farbsatz definiert das Projekt.
- **26.08.2026 (5)** — §9 nach Rollen getrennt statt nach Linienart: **Bedienkonturen**
  (Feldbox, Auswahlkästchen, Fokusring) tragen die 3:1, **Trennlinien** (Panelrand,
  Zeilentrenner) tragen keine Zahl — die alte Fassung verlangte 3:1 für „Konturen und
  Trennlinien" und war damit im eigenen Stylesheet an jeder Zeile verletzt. Daraus folgt:
  `--au-line-input` von 2,11:1 auf 3,30:1 und `--au-line-input-hover` auf 4,04:1 — die
  Feldbox wird sichtbar fester, was §7 ohnehin verlangt; `.au-check` teilt den Ton und ist
  mit erfasst. Neu `--au-warn-ink` (5,29:1) für die Warnfarbe als Text: der
  Entwurfsvermerk auf der Belegvorschau lief in `--au-warn` mit 3,61:1, und §9 nennt
  Vermerke auf Ausgaben ausdrücklich. Neu außerdem die Regel, dass die Schranken **je
  Theme** gelten und Töne mit Luft gewählt werden — ein auf Weiß knapp bestandener Ton
  fällt auf getönter Fläche durch; Dunkelmodus wird gemeinsam nachgezogen.
- **26.08.2026 (4)** — Platzhalterton auf die Schranke aus §9 gehoben: `--au-input-ph`
  lag bei 3,68:1, obwohl §9 die 4,5:1 ausdrücklich auch für Platzhalter verlangt (jetzt
  4,73:1 auf der Eingabefläche, 4,61:1 auf der abgesenkten Fläche); die Rangfolge
  Platzhalter < Meta < Label bleibt. `.au-rail__label` löst jetzt ein, was §2.4
  verspricht — einzeilig mit Ellipsis, dazu `overflow: hidden` am Eintrag, dessen Breite
  `--au-mark` sonst überschritten wurde. Zwei Positionsselektoren der Monatsübersicht
  durch Namen ersetzt (`au-mini-cal__title`, `au-mini-cal__reset`, §12), weil eine
  Regel über die Position im Markup keinen Vertrag hat.
- **26.08.2026 (3)** — Symbole als eigener Abschnitt (§2.4): ein systematischer Satz,
  nie einziger Bedeutungsträger, Größe über `--au-icon`/`--au-icon-sm`, Dateien lokal im
  Projekt. Klassen `au-icon`, `au-menu__item--ico` und `au-navitem__label` ergänzt —
  ohne schrumpfenden Namen schiebt ein langer Favoriteneintrag das Tastenkürzel aus dem
  Navigationseintrag. Zwei Fehler im Stylesheet behoben: das Burger-Menü zentrierte
  seine Striche nach dem Drehen der Hauptachse nicht mehr senkrecht, und die
  Zustandsmarke am gesperrten Feld und in der gesperrten Rasterzelle lag mit 2,63:1
  unter der Schranke aus §9 (jetzt `--au-label`, 8,08:1) — §7 entsprechend präzisiert.
- **26.08.2026 (2)** — Fußzeile der Sidebar festgeschrieben (§4): Zeitanzeige und
  Monatsübersicht mit Kalenderwoche als erster Spalte, umschaltbar über ein Symbol, Zustand
  benutzerbezogen gespeichert; Serverzeit, Kultur der Installation, Abgrenzung „Anzeige, kein Planer",
  kein Eintrag im Besuchsverlauf (§4a). Allgemeine Regel ergänzt, wie Anzeigeelemente auf ein
  Modul verweisen dürfen — Sprung als Tab, ohne vorhandenes Zielmodul nicht anklickbar.
  Klassen `au-navfoot` und `au-mini-cal` im Stylesheet und in §12; dritte Akzentrolle
  ergänzt (`--au-accent-fill` mit `--au-on-accent`) für eine gefüllte Fläche, die Text
  trägt — `--au-accent` erreicht als Untergrund den Textkontrast nicht. Der heutige Tag trägt Fläche **und**
  abweichende Form, die Fußzeile zusätzlich das Datum als Wort (§9). In der Rail entfällt die
  Fußzeile; Umschalten und Monatswechsel in der Ausnahmeliste des Besuchsverlaufs (§4a).
- **26.08.2026** — Zweites Fenster derselben Anmeldung als Weg für zwei Arbeitsgänge
  nebeneinander festgeschrieben (§3a): je Fenster eine eigenständige Shell mit eigener
  Tab-Leiste und eigenem Besuchsverlauf, `Strg+Alt+Enter` und Menüeintrag zum Öffnen
  (§4, §5.2, §5.3), Übergabe des Ziels ohne Adresszeile (§4a, §10), Fenstertitel nennt
  die vordere Ansicht. Ein zweiter Arbeitsbereich **in einem** Fenster ist ausdrücklich
  nicht Bestandteil (§3a). Sitzungs- und Lizenzseite: `_security.md` §1.4 und §1.5.
- **24.08.2026** — Besuchsverlauf eingeführt (§4a): Browser-Zurück und -Vorwärts wechseln
  die aktive Ansicht entlang der besuchten Module und Datensätze; Shell-Tabs bleiben offen.
  Einträge nur bei gezieltem Öffnen und Tabwechsel, nicht beim Blättern (§1.2 präzisiert).
  Keine URLs, keine Deep-Links; Einträge geschlossener Tabs werden übersprungen, ein
  Wächter-Eintrag verhindert unbeabsichtigtes Verlassen. `Alt+←`/`Alt+→` in §5.2
  dokumentiert (§5.3: einzige nicht abgefangene Browserfunktion), Umsetzungshinweis in §10.
- **22.08.2026** — Mehrmandantenfähigkeit entfernt: je Installation genau eine Firma
  (`_data.md` §5). Firmenauswahl bei der Anmeldung und Firmenwechsel gestrichen (§3a); die
  Topbar zeigt die Firma der Installation weiterhin dauerhaft. Anmeldekarte auf zwei
  gleichwertige Anmeldewege ausgerichtet (`_security.md` §1): zentral der
  Standard-Anmeldeweg der Installation, Einmalcode des zweiten Faktors als eigener Schritt.
- **18.08.2026** — Anmeldung, Übergang, Firmenwechsel und Sitzungsmeldungen als eigener
  Abschnitt (§3a) samt nicht-modalem Feedback und Fortschrittsanzeige. Merkmalsfilter,
  Bezugskontext und Vorschauspalte festgeschrieben (§6a). Vier übergreifende Maskenmuster
  ergänzt: Strukturliste mit Gruppen und historisierten Werten (§7d), vererbte Zeilen aus einer
  Vorlage mit Zeilenstatus (§7e), Geltungsbereichskette als einheitliche Begründungsform (§7f),
  Merkmalsmatrix mit befristeter Ausnahme (§7g), erzeugte Texte (§7h). Datums- und
  Zeiteingabe-Konvention und Wiederverbindungsdarstellung in die Umsetzungssektion aufgenommen.
- **29.07.2026 (5)** — Akzent in zwei Rollen getrennt (Linie/Fläche gegen Text), Platzhalterton
  abgedunkelt, Kontrast- und Mindestgrößenregel ausdrücklich auf bedeutungstragende Zeichen
  ausgeweitet.
- **29.07.2026 (4)** — Doppelklick auf eine Tabellenzeile öffnet ausnahmslos den Satz (§5.2, §6);
  Gruppen innerhalb einer Listenart als Auswahl-, Spalten- und Gruppierkriterium festgeschrieben,
  ihre Verwaltung ausdrücklich der Verwaltungsmodulgruppe zugewiesen.
- **29.07.2026 (3)** — Panel für den Bezugssatz der markierten Position ergänzt (Bild,
  Bezeichnung, Kennzahlen).
- **29.07.2026 (2)** — Belegkopf als eigener abgegrenzter Block festgeschrieben,
  Rasteraktionen auf `F3`/`F4` korrigiert und als beschriftete Buttons verortet, ausdrückliches
  Verbot einer Tastenübersicht in der Maske, abhängige Mengengröße als Rasterspalte,
  Ausgabeblock und Belegvorschau (`Strg+P` öffnet die Vorschau, nicht den Druckdialog)
  ergänzt, Drucken und Versenden auch im gesperrten Endzustand erlaubt.
- **29.07.2026** — Belegmuster ergänzt (§7a): Statusfluss als Kette, editierbare
  Rasterzelle ohne Dauerkontur mit den Zuständen der Feldbox, Tastaturfluss und
  Tastenbezug im Positionsraster, Summenblock, gesperrter Endzustand. Wertehilfe als
  eigenes Muster festgeschrieben (§7b). Löschdialog um die Variante mit
  Verwendungsnachweis erweitert (§7c). Inline-Hinweis als drittes Rückmeldeformat.
- **28.07.2026** — Erste verbindliche Fassung. Eingabefelder als Boxen mit sichtbarer
  Kontur (leer erkennbar), Kontrastschranken verschärft, Tastenlegenden aus Listenfuß und
  Statuszeile entfernt und stattdessen am Auslöser platziert, Auslassungszeichen-Button
  durch Burger ersetzt, Funktionstastenbelegung §5 festgeschrieben, sämtliche Maß- und
  Farbwerte in Tokens überführt.

---

## 12. Klassenverzeichnis

Vollständige Liste der Klassen des Stylesheets mit ihrer Rolle. Erweiterungen werden hier
mit eingetragen; ungenannte Klassen sind unzulässig.

**Hilfsklassen** · `au-num` Zahl in Monospace mit `tabular-nums` · `au-code` Modulnummer ·
`au-key` Tastenkürzel · `au-icon` Symbol (§2.4).

**Shell** · `au-app` Rahmen · `au-topbar` Kopfzone · `au-brand`, `au-brand__mark` Markenblock ·
`au-topbar__meta` Firma/Rolle/Benutzer · `au-tabbar` Tabzone · `au-tab` Tab mit
`au-tab__code`, `au-tab__close`, `au-tab--active`, `au-tab--dirty` (ungespeichert) ·
`au-work` Arbeitsbereich · `au-panel` Panel mit `au-panel--list`, `au-panel--detail`,
`au-panel__head`, `au-panel__title`, `au-panel__foot`.

**Navigation** · `au-sidebar`, `au-sidebar__hint` · `au-jump` Sprungfeld mit
`au-jump--field` (in Panelfläche), `au-jump--lg` (Startseite), `au-jump__code`,
`au-jump__sep` · `au-jumphits`, `au-jumphit`, `au-jumphit--sel` Trefferliste ·
`au-navsection` Abschnittsüberschrift · `au-navlist` · `au-navitem` mit
`au-navitem__code`, `au-navitem__label`, `au-navitem__key`, `au-navitem--group`,
`au-navitem--active` ·
`au-rail` Nummern-Rail mit `au-rail__item`, `au-rail__code`, `au-rail__label`,
`au-rail__item--active` · `au-scrim` Abdunkelung · `au-palette` Palette mit
`au-palette__input`, `au-palette__group`, `au-palette__row`, `au-palette__row--sel`,
`au-palette__foot` · `au-start`, `au-start__greeting`, `au-tilegrid`, `au-tile`,
`au-tile__label`, `au-modulerow`, `au-modulerow__subs` Startseite.

**Steuerelemente** · `au-btn` mit `au-btn--primary`, `au-btn__key`, `au-btn--burger`,
`au-btn--icon` (Zeichen-Taste, §2.4) ·
`au-actionbar` Aktionsleiste im Panelkopf · `au-menu` mit `au-menu__title`,
`au-menu__item`, `au-menu__item--sep`, `au-menu__item--danger`, `au-menu__item--ico`,
`au-menu__key` ·
`au-search`, `au-search__key` Filterfeld · `au-chiprow`, `au-chip`, `au-chip__x` Filterchips.

**Datenliste** · `au-table` mit `au-table__head`, `au-table__body`, `au-table__row`,
`au-table__row--sel`, `au-table__row--muted` (inaktiver Satz) · Zellrollen `au-cell-num`,
`au-cell-money`, `au-cell-name`, `au-cell-name__tag` · `au-avatar` Kürzelmarke ·
`au-status` mit `au-status--ok`, `au-status--warn`, `au-status--error`.

**Datensatzmaske** · `au-record__head`, `au-record__ident`, `au-record__mark`,
`au-record__name`, `au-record__id`, `au-record__meta`, `au-record__actions`
(Aktionszeile, §7), `au-record__tabs`,
`au-record__body` · `au-kpigrid`, `au-kpi`, `au-kpi__label`, `au-kpi__value`,
`au-kpi__value--warn` · `au-group`, `au-group__title`, `au-fields` · `au-field` mit
Spannweiten `au-field--2` bis `au-field--12` (vollständige Skala, §7), `au-field__label`, `au-field__box`,
`au-field__value`, `au-field__value--empty`, `au-field__adorn`, `au-field__msg` und
Zuständen `au-field--num`, `au-field--multiline`, `au-field--readonly`,
`au-field--dirty`, `au-field--invalid` · `au-statusbar`.

**Vollbreite-Ansicht** · `au-navigator`, `au-navigator__item`, `au-navigator__item--sel`,
`au-navigator__meta`.

**Belegmaske** · `au-statusflow` mit `au-statusflow__step`, `au-statusflow__step--done`,
`au-statusflow__step--current`, `au-statusflow__step--final`, `au-statusflow__arrow` ·
`au-grid` Positionsraster mit `au-grid__toolbar`, `au-grid__head`, `au-grid__body`,
`au-grid__row`, `au-grid__row--odd`, `au-grid__row--active`, `au-grid__pos`, `au-grid__label`
(Bezeichnungszelle), `au-grid__new`
(Zeile für die nächste Position) · `au-cellbox` editierbare Rasterzelle mit
`au-cellbox__value`, `au-cellbox__value--num`, `au-cellbox__adorn` und Zuständen
`au-cellbox--focus`, `au-cellbox--dirty`, `au-cellbox--invalid`, `au-cellbox--readonly` ·
`au-sums` Summenblock mit `au-sums__row`, `au-sums__label`, `au-sums__value`,
`au-sums__row--total`, `au-sums__row--grand` · `au-dochead` abgegrenzter Kopfblock mit
`au-dochead__title` · `au-doc--locked` abgeschlossener Beleg (Konturen entfallen, Werte
werden Text).

**Bezugssatz** · `au-refcard` mit `au-refcard__media`, `au-refcard__facts`.

**Ausgabe und Vorschau** · `au-output` Aktionsblock mit `au-output__row`, `au-output__log` ·
`au-preview` Belegvorschau mit `au-preview__head`, `au-preview__controls`,
`au-preview__stage`, `au-preview__sheet`, `au-preview__draft` (Entwurfsvermerk),
`au-preview__foot`.

**Inline-Hinweis** · `au-hint` mit `au-hint--warn`, `au-hint--error`, `au-hint--locked` —
drittes Rückmeldeformat neben Feldmeldung und Dialog.

**Wertehilfe** · `au-lookup` mit `au-lookup__head`, `au-lookup__title`, `au-lookup__ref`,
`au-lookup__close`, `au-lookup__meta`, `au-lookup__foot`.

**Rückfrage** · `au-dialog`, `au-dialog__title`, `au-dialog__text`, `au-dialog__actions` ·
`au-btn--danger` löschende Primäraktion · `au-usage` Verwendungsnachweis mit
`au-usage__head`, `au-usage__row`, `au-usage__code`, `au-usage__label`, `au-usage__count`,
`au-usage__link`.

**Anmeldung und Übergang** · `au-login` Anmeldefläche mit `au-login__card`,
`au-login__title`, `au-login__more` (Untermenü weiterer Anmeldewege), `au-login__foot`,
`au-login__state` (Zustand der Umgebung) · `au-splash` Übergang mit `au-splash__stage`,
`au-splash__title`, `au-splash__meta`, `au-splash__progress`, `au-splash__steps`,
`au-splash__step`, `au-splash__step--done`.

**Nicht-modales Feedback und Fortschritt** · `au-toast` mit `au-toast__text`,
`au-toast__action`, `au-toast--ok`, `au-toast--warn`, `au-toast--error` · `au-progress` mit
`au-progress__bar`, `au-progress__bar--done`.

**Fußzeile der Sidebar** · `au-navfoot` mit `au-navfoot__time`,
`au-navfoot__meta`, `au-navfoot__toggle` · `au-mini-cal` mit `au-mini-cal__head`,
`au-mini-cal__title`, `au-mini-cal__nav`, `au-mini-cal__dow`, `au-mini-cal__week`,
`au-mini-cal__kw`,
`au-mini-cal__day`, `au-mini-cal__day--out`, `au-mini-cal__day--we`,
`au-mini-cal__day--today`, `au-mini-cal__foot`, `au-mini-cal__reset`.

**Merkmalsfilter, Kontextspalte** · `au-facets` mit `au-facets__head`, `au-facets__group`,
`au-facets__title`, `au-facets__opt`, `au-facets__opt--on`, `au-facets__count`,
`au-facets__foot` · `au-check`, `au-check--on` Auswahlkästchen · `au-context`
Vorschau-/Kontextspalte.

**Strukturliste und historisierte Werte** · `au-grid__group` Gruppenzeile im Raster mit
`au-grid__group-sum` · `au-grid__row--empty` Vermerk „keine Position" · `au-delta`
Abweichung mit `au-delta--none` (unverändert), `au-delta--new` (keine Fixierung vorhanden).

**Vorlagenvererbung** · `au-rowstate` Zeilenstatus mit `au-rowstate--inherited`,
`au-rowstate--changed`, `au-rowstate--new`, `au-rowstate--removed` · `au-grid__row--removed`
ausgeblendete Vorlagezeile (durchgestrichen).

**Geltungsbereichskette** · `au-chain` mit `au-chain__result`, `au-chain__step`,
`au-chain__mark`, `au-chain__meta`, `au-chain__step--hit`, `au-chain__step--miss`,
`au-chain__step--skip`.

**Merkmalsmatrix** · `au-matrix` mit `au-matrix__head`, `au-matrix__row`,
`au-matrix__label`, `au-matrix__cell`, `au-matrix__legend` · `au-perm` Zustandszeichen mit
`au-perm--yes`, `au-perm--inh` (geerbt), `au-perm--no`, `au-perm--na`.

**Erzeugte Texte** · `au-derived` schreibgeschützter erzeugter Text mit `au-derived__row`,
`au-derived__nr`, `au-derived__row--empty`.

**Kennfarbe der Installation (§3)** · `au-kennmark` Kurzname-Marke in der Topbar ·
`au-swatchgrid`, `au-swatch`, `au-swatch--sel`, `au-swatch__chip`, `au-swatch__name`,
`au-swatch__check` Farbauswahl · `au-kennpreview` Vorschau mit `au-kennpreview__col`,
`au-kennpreview__label`, `au-kennpreview__bar`, `au-kennpreview__sig`,
`au-kennpreview__mark`, `au-kennpreview__txt`, `au-kennpreview__card`,
`au-kennpreview__cardbar`, `au-kennpreview__cardbody`, `au-kennpreview__cardname`,
`au-kennpreview__cardmeta`.

**Dichte** · `au-dense` schaltet Zeilenhöhe und Abstände auf die dichte Stufe.
