# _documentation.md — Leitfaden Dokumentation

Regeln für die beiden Dokumentationen des Produkts:

- die **Endkunden-Dokumentation** für Anwender unter `[pfade] dokuUser` (unten `{dokuUser}`),
- die **Entwicklerdokumentation** für Entwickler:innen und Betreibende unter `[pfade] dokuDev`
  (unten `{dokuDev}`).

Zielgruppe, Sprache und Detailgrad unterscheiden sich grundlegend — verwechsle die beiden
nicht. Die Mechanik dagegen ist dieselbe und steht deshalb nur einmal (§1); danach folgt, was
je Dokumentation eigen ist (§2, §3).

Zugehörige Dateien: `_requirements.md` (Specs sind das fachliche **Was**; die Begriffszuordnung
steht im Glossar von `PROJEKT.md`), `_uiux.md` (Benennung und Bedienung der Oberfläche),
`_architecture.md` (der autoritative Architekturvertrag — die Doku konkretisiert ihn,
widerspricht ihm nie).

---

## 1. Gemeinsame Mechanik (beide Dokumentationen)

### 1.1 Diátaxis: vier Dokumenttypen, nicht gemischt

| Typ | Frage | Charakter |
|---|---|---|
| **Tutorial** | „Wie komme ich rein und erlebe einen ersten Erfolg?" | ein durchgehender, garantiert funktionierender Pfad |
| **Anleitung / How-To** | „Wie erledige ich Aufgabe X?" | zielgerichtete, nummerierte Schrittfolge, ein Ziel je Dokument |
| **Referenz** | „Wie ist X genau aufgebaut?" | vollständig, tabellarisch, nachschlagbar, wertungsfrei |
| **Erklärung** | „Warum ist das so?" | Hintergrund, Zusammenhänge, Konsequenzen |

**Ein Dokument mischt die Typen nicht.** Wer eine Anleitung schreibt, erklärt nicht nebenbei
Konzepte; wer ein Konzept erklärt, listet keine Klick-Schritte. Einordnung: *Handeln* →
Anleitung; *Lernen* → Tutorial; *Nachschlagen* → Referenz; *Verstehen* → Erklärung.

### 1.2 Ablage und Benennung

- **Dateiname in kebab-case**, deutschsprachig, aufgabensprechend (`adresse-anlegen.md`,
  nicht `S2.md` oder `create-address.md`).
- **`README.md` je Doku-Wurzel ist der Index** und die einzige Seite mit Anspruch auf
  vollständige Verlinkung: Von ihr ist jedes Dokument in höchstens einem Klick erreichbar.

### 1.3 Meta-Block

Jedes Dokument beginnt mit einem Meta-Block und einer Kurz-Einordnung — ein Satz, was das
Dokument beantwortet und wann man es braucht:

```markdown
> **Typ:** {Diátaxis-Typ} · **Für:** Anwender | Entwickler:innen · **Bezug:** {Quelle}
> **Stand:** {JJJJ-MM-TT}
```

- **`Bezug`** nennt die Quelle: bei Nutzer-Dokumenten die Story
  (`{specs}/E{n} …/F{n} …/S{n} ….md`), bei Entwickler-Dokumenten die betroffenen Code-Pfade
  (Pfadwurzeln aus dem Profil, **keine Zeilennummern** — die veralten sofort).
- **`Stand`** ist das absolute Datum des letzten Abgleichs.

### 1.4 Verlinkung

Die Dokumente bilden ein Netz, keine Einzelseiten:

- **Relative Markdown-Links** zwischen Dokumenten; keine absoluten Pfade, keine rohen URLs im
  Fließtext.
- **Jedes Dokument hat einen Abschnitt „Siehe auch"** mit 2–5 Links auf fachlich benachbarte
  Dokumente. Sackgassen ohne Weiterführung sind unzulässig.
- **Nicht duplizieren, sondern verlinken.** Eine Information hat genau einen Ort; FAQ und
  Problembehebung verweisen auf Anleitung und Referenz, statt Inhalte zu kopieren.
- **Sprechende Linktexte** — der Titel des Ziels, nie „hier klicken".

### 1.5 Sprache, Begriffe, Sicherheit

- Deutsch, sachlich, präzise. **Bezeichner nach dem Glossar** des Projektprofils
  (`_requirements.md` §5) — die Doku erfindet keinen dritten Begriff.
- **Keine Geheimnisse und keine echten Daten:** keine Passwörter, Tokens, Schlüssel oder
  internen Hostnamen; keine echten Personen-, Kunden- oder Firmendaten in Beispielen und
  Screenshots. Platzhalter wie `<API_TOKEN>` verwenden, Beispielwerte als solche kennzeichnen
  und auf die tatsächliche Quelle verweisen.
- **Barrierearm:** genau ein H1 je Dokument, Überschriftenebenen lückenlos, Alt-Text für jedes
  Bild, keine reinen Farb- oder Positionsverweise, Tabellen mit Kopfzeile, kurze Absätze.

### 1.6 Pflege (verbindlich)

Die Doku ist Teil des Dev-Workflows (`_index.md`) und wird **mit** der Änderung gepflegt,
nicht nachgelagert:

- **Nutzersichtbares Verhalten ändert sich** → betroffene Nutzer-Dokumente in derselben
  Sitzung anpassen, bevor die Story auf `Implemented` geht.
- **Technik oder Architektur ändert sich** (Endpunkt, Migration, Ports/Umgebung, Build und
  Auslieferung, Architekturentscheidung) → betroffene Entwickler-Dokumente mitziehen
  beziehungsweise eine ADR anlegen (§3.4).
- **Eine Benennung ändert sich** → zuerst das Glossar in `PROJEKT.md`, dann alle Nennungen
  und Screenshots — nie umgekehrt.
- **Neue Fähigkeit mit Doku-Relevanz** → neues Dokument nach §1.1/§1.2, im `README.md`
  verlinkt. „Stillschweigend nicht dokumentiert" gilt als Lücke.
- **Rein interne Änderungen** ohne sichtbares Verhalten und ohne strukturelle Wirkung ändern
  die Doku nicht.
- Vor Abschluss: keine toten Links, jedes Dokument im Index, „Siehe auch" gesetzt,
  `Stand`-Zeile aktuell.

---

## 2. Endkunden-Dokumentation (`{dokuUser}`)

### 2.1 Zielgruppe und Ziel

Der fachliche **Anwender** des Produkts (die im Profil benannte Anwender-Rolle) — kein
Entwickler, kein Administrator. Er kennt seine fachliche Domäne, aber nicht die
Funktionsweise der Anwendung; er liest **aufgabengetrieben, selektiv und unter Zeitdruck**;
er arbeitet in der Installation **einer Firma** und sieht nur deren Belege — hat sein Haus
mehrere Firmen, ist jede eine eigene Installation (`_data.md` §5).

**Ziel:** den Anwender so schnell wie möglich handlungsfähig machen und ihn bei Problemen
ohne Support zur Lösung führen. **Nicht-Ziele:** Marketing, Feature-Aufzählung, Erklärung
interner Technik.

### 2.2 Grundprinzipien

- **Aufgabenorientiert, nicht feature-orientiert.** Kapitel heißen nach dem, was der Nutzer
  *tun* will („{Gegenstand} verwalten"), nicht nach Bausteinen der Oberfläche.
- **Quelle ist die Spec, nicht der Code.** Beschrieben wird ausschließlich spezifiziertes
  Verhalten, in Nutzersprache übersetzt — nie aus dem Code „herausgelesene" Interna.
- **Eine Wahrheit für Benennungen.** Jede Schaltfläche, jedes Feld, jeder Menüpunkt heißt in
  der Doku **exakt** wie in der Oberfläche (`_uiux.md`). Weicht die Doku ab, ist die Doku
  falsch.
- **Ehrlich und präzise.** Grenzen, Pflichtfelder und Fehlerfälle werden benannt, nicht
  verschwiegen.
- **Ton wie das Produkt:** nüchtern, „Sie"-Anrede, kein Marketing-Sprech.

### 2.3 Ablage

```
{dokuUser}/
├── README.md                 ← Index, verlinkt ALLES
├── erste-schritte.md         ← Tutorial: erster erfolgreicher Durchlauf
├── anleitungen/              ← je fachliche Aufgabe eine Datei
├── referenz/
│   ├── {gegenstand}-felder.md   ← jedes Feld: Bedeutung, Pflicht?, Format, Grenzen
│   ├── menue-und-navigation.md
│   ├── tastatur.md              ← die feste Funktionstastenbelegung (_uiux.md §5)
│   └── meldungen.md             ← Fehler- und Hinweistexte der Anwendung
├── konzepte/                 ← je Zusammenhang eine Datei
├── faq.md                    ← kurze Antworten, jede verlinkt in Anleitung oder Referenz
├── problembehebung.md        ← Symptom → Ursache → Lösung
├── glossar.md                ← Fachbegriffe für den Anwender erklärt (§2.5)
└── assets/                   ← Screenshots und Medien
```

Anleitungen spiegeln Stories in Nutzersprache; die Zuordnung zur Spec steht im Meta-Block
(§1.3), nicht im Dateinamen. Das Glossar hier erklärt, was ein Begriff **fachlich bedeutet** —
es ist nicht das Glossar aus `PROJEKT.md` (dort: Oberflächenbegriff ↔ Code-Bezeichner); die
Schreibweise muss übereinstimmen.

### 2.4 Schreibstil

- **Aktiv und Imperativ in Schritten:** „Wählen Sie **{Label}**." — ein Schritt, eine
  Handlung; nummerierte Listen für Abläufe.
- **UI-Elemente fett und wortgleich** zur Oberfläche: **Speichern**, Feld **{Feldname}**.
- **Den Tastaturweg zuerst nennen.** Die Anwendung wird über die Tastatur bedient
  (`_uiux.md` §5): „Drücken Sie **F3**, um einen neuen Satz anzulegen." — der Mausweg folgt.
- **Ergebnisorientiert:** nach kritischen Schritten beschreiben, was der Nutzer sieht.
- **Keine internen Begriffe:** der deutsche Oberflächenbegriff aus dem Glossar, nie der
  englische Code-Bezeichner; eine technische Fehlerkennung wird zur Wirkung („Speichern
  schlägt fehl").
- **Fehlermeldungen wörtlich** und als Zitat gekennzeichnet — die Meldung stammt aus dem
  Vorgang, die Doku formuliert sie nicht um, sonst sucht der Anwender nach einem Satz, den er
  nie zu sehen bekommt.
- Fachbegriffe verlinken beim ersten Vorkommen auf den Glossar-Eintrag; Feld- und
  Meldungsnennungen auf die zuständige Referenzseite.

| Situation | Formulierung |
|---|---|
| Aktion auslösen | „Klicken Sie auf **{Label}**." |
| Eingabe | „Geben Sie im Feld **{Feld}** … ein." |
| Pflichtfeld | „**{Feld}** ist ein Pflichtfeld und muss ausgefüllt sein." |
| Erfolg | „Nach dem Speichern kehren Sie zur Übersicht zurück." |
| Fehler | „Bleibt das Formular offen und zeigt eine rote Meldung, …" |

### 2.5 Screenshots

- Ablage in `{dokuUser}/assets/`, Dateiname nennt Dokument und Motiv.
- **Zweck statt Zierde:** genau der Zustand, auf den sich der umgebende Text bezieht — nicht
  der ganze Bildschirm. Standard-Darstellung, ohne Browser-Rahmen.
- **Aktualität:** Ändert sich die Oberfläche, werden betroffene Screenshots in derselben
  Sitzung ersetzt. Veraltete Bilder sind schlimmer als keine.
- Alt-Text ist Pflicht; nur eigens angelegte Testdaten, auch beim sichtbaren Firmennamen.

### 2.6 Beispiel (Anleitung, gekürzt)

```markdown
# {Gegenstand} anlegen

> **Typ:** Anleitung · **Für:** Anwender · **Bezug:** {specs}/E{n} …/F{n} …/S{n} ….md
> **Stand:** {JJJJ-MM-TT}

So erfassen Sie einen neuen {Gegenstand}.

## Voraussetzungen
- Sie arbeiten in der Anwendung der richtigen **Firma** — sie steht oben im Kopf der
  Anwendung.
- Sie befinden sich im Menü **{Modulgruppe}** › **{Ansicht}**.

## Schritte
1. Drücken Sie **F3**. Die leere Maske **{Titel}** öffnet sich.
2. Füllen Sie die Pflichtfelder **{Feld}** und **{Feld}** aus. Zu einem Bezugsfeld öffnet
   **F5** die Auswahlliste; **Enter** übernimmt den Treffer.
3. Drücken Sie **Strg+S**.

## Ergebnis
Der Satz ist gespeichert und in der Liste ausgewählt; die Änderungsmarkierung am Tab
verschwindet.

## Siehe auch
- [{Gegenstand}-Felder](../referenz/…md)
- [Tastaturbedienung](../referenz/tastatur.md)
```

---

## 3. Entwicklerdokumentation (`{dokuDev}`)

### 3.1 Zielgruppe und Ziel

Menschen, die am oder mit dem Code arbeiten: neue Teammitglieder (Onboarding), Beitragende,
Betreibende. Sie sind technisch versiert (PHP/Laravel, Livewire, relationale Datenbanken,
Git, Container), kennen aber **dieses** System und seine Entscheidungen noch nicht.

**Ziel:** Neue schnell produktiv machen und die **nicht offensichtlichen** Entscheidungen
festhalten. **Nicht-Ziele:** den Code nacherzählen, Specs duplizieren, eine API-Referenz
pflegen, die bei jeder Umbenennung veraltet.

### 3.2 Grundprinzipien

- **Der Code ist die Wahrheit — die Doku erklärt das Nicht-Offensichtliche:** das *Warum* und
  das große Ganze. Steht es lesbar im Code, wird der Code verlinkt, nicht abgeschrieben.
- **Specs sind das Was, die Doku ist das Wie.** Für fachliches Verhalten wird auf die Story
  beziehungsweise `_Backend.md` verwiesen (`_requirements.md`).
- **Architektur-Regeln sind gesetzt** (`_architecture.md` und die nachgelagerten Verträge).
  Weicht die Realität ab, wird zuerst die Regel geklärt — eine bewusste Abweichung braucht
  eine ADR (§3.4), keine „vorbei dokumentierte" Doku.
- **Code-Referenzen als Pfade und Symbole**, in Backticks: der Vorgang mit Klassennamen, der
  Endpunkt mit Verfahren und versioniertem Pfad. Codeausschnitte kurz und als Ausschnitt
  gekennzeichnet.
- **Reproduzierbar:** Befehle in kopierbaren Codeblöcken mit Ausführungsort, Shell (falls
  relevant) und Erfolgskriterium („der Health-Endpunkt antwortet mit `200`"). Keine Schritte
  dokumentieren, die nicht real ausgeführt wurden.
- **Diagramme als Mermaid-Codeblöcke** direkt im Markdown — versionierbar, diffbar, kein
  Binär-Asset. Klein und auf eine Aussage fokussiert; ein Diagramm ergänzt Text, ersetzt ihn
  nicht.

### 3.3 Ablage und Mindestumfang

```
{dokuDev}/
├── README.md                    ← Index; kürzester Weg „von 0 auf lauffähig"
├── setup.md                     ← lokale Entwicklungsumgebung Schritt für Schritt
├── architektur.md               ← Überblick: Topologie, Schichten, Modulschnitt, Diagramm
├── how-to/                      ← je Entwicklungsaufgabe ein Rezept
├── referenz/
│   ├── projektstruktur.md       ← der kommentierte Verzeichnisbaum (einzige Stelle dafür)
│   ├── module.md                ← Module, Grenzen, öffentliche Flächen, Abhängigkeiten
│   ├── api.md                   ← Integrations-API: Versionsstand, Zugänge, wo die erzeugte
│   │                              OpenAPI-Beschreibung liegt — keine handgepflegte Endpunktliste
│   ├── datenmodell.md           ← Schemata, Schlüssel; verweist je Epic auf
│   │                              dessen _Datenstruktur.md, statt Felder zu duplizieren
│   ├── konfiguration.md         ← Umgebungsvariablen, Einstellungen, Schalter
│   └── build-und-ausrollen.md   ← Pakete, Container, Versionierung, Rückkehrweg
└── entscheidungen/              ← ADRs, fortlaufend nummeriert (0001, 0002, …)
```

Verbindlich ist der Aufbau, nicht die Auswahl der Einzeldateien. Eine vollständige
Entwicklerdoku deckt mindestens ab: Architektur-Überblick (wie dieses Produkt den Vertrag
ausfüllt — nicht den Vertrag nacherzählen) · Projektstruktur · Modulübersicht (je Modul ein
Abschnitt, angelegt **mit** dem Modul) · lokales Setup · Datenmodell und Migrationen ·
Integrations-API · **wo Fachlogik liegt** (die häufigste Frage neuer Beitragender und die
häufigste Schichtverletzung) · Sicherheit · Betrieb und Konfiguration · Bauen und Ausrollen ·
Testen (Verweis auf die Testleitfäden, wie die Suiten lokal laufen) · Entscheidungen (ADRs) ·
Störungssuche (häufige Stolpersteine).

### 3.4 Architecture Decision Records (ADR)

```markdown
# {Nr}. {Titel der Entscheidung}

> **Status:** Accepted | Superseded by [{Nr}](000X-….md) · **Datum:** JJJJ-MM-TT

## Kontext
Welches Problem/Spannungsfeld führte zur Entscheidung?

## Entscheidung
Was wurde festgelegt (aktiv: „Wir …").

## Konsequenzen
Positive wie negative Folgen; was dadurch einfacher/schwerer wird.

## Bezug
Verweise auf Code, Specs und die betroffene Harness-Regel.
```

- **Unveränderlich:** Eine ADR wird nicht umgeschrieben. Ändert sich die Entscheidung,
  entsteht eine neue; die alte erhält `Superseded by …`. Nummern bleiben stabil.
- **Wann eine ADR Pflicht ist, steht in `_architecture.md` §11** — jeder Fall von dort hat
  eine. Fehlt sie, ist die Entscheidung nicht getroffen, sondern nur passiert.
- **Geschrieben wird sie mit der Entscheidung, nicht danach.** Nachträglich verfasst hält sie
  fest, was ohnehin passiert ist, statt eine Wahl zu begründen.
- ADRs verlinken Regel, Story und Code, die sie betreffen; betroffene Referenz- und
  How-To-Doku verlinkt zurück auf die begründende ADR.

---

## Verweise

- Specs, Story-Struktur, Glossarpflicht: `_requirements.md`
- Benennung und Bedienung der Oberfläche: `_uiux.md`
- Architekturvertrag und ADR-Pflichtfälle: `_architecture.md` §11
- Betriebsdokumentation je Installation: `_operations.md` §8
- Teststrategie und Testleitfäden: `_test-strategy.md`, `_test-api.md`, `_test-ui.md`
- Pfade, Glossar, Anwender-Rolle: Projektprofil `PROJEKT.md`
