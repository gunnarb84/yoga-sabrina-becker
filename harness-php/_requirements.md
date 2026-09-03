# _requirements.md — Anforderungs-Konventionen

Regeln, wie Anforderungen (Epics, Features, Stories, Akzeptanzkriterien) strukturiert und
formuliert werden.

Übergeordnet: `_architecture.md` — was ein Kriterium fordern darf, wird dort entschieden.
Wie geprüft wird, dass es erfüllt ist: `_test-strategy.md`. Wie aus einer Idee eine Spec wird:
`_ideas.md`.

Ablagewurzel ist `[pfade] specs` aus dem Projektprofil; sie wird unten `{specs}` genannt.

---

## 1. Ordner- und Dateistruktur

```
{specs}/
├── guidelines/               ← projektspezifische Ergänzungen, keine Kopie des Harness
│   └── _Menüstruktur.md      ← Navigation über alle Epics, gehört keinem (§2.3)
├── E{n} {Epic-Name}/
│   ├── _Datenstruktur.md     ← fachliche Gegenstände des Epics und ihre Felder
│   ├── _Backend.md           ← Vorgänge, Abfragen und Ereignisse des Epics
│   └── F{n} {Feature-Name}/
│       └── S{n} {Story-Name}.md
```

- **E** = Epic, **F** = Feature, **S** = Story, jeweils mit laufender Nummer.
- Nummerierung startet pro Ebene bei 1.
- **Nummern bleiben stabil.** Neue Stories werden hinten angehängt, nicht dazwischen
  eingeschoben — Tests, Doku und Verweise hängen daran.

---

## 2. Die begleitenden Dokumente

Sie sind **kein Beiwerk**: Ohne sie hängen die Regeln aus §3, §4 und §7 in der Luft. Zwei
gehören dem Epic, das dritte allen.

### 2.1 `_Datenstruktur.md`

Führt die fachlichen Gegenstände des Epics mit ihren Feldern auf. Sie ist die Quelle für jeden
Feldnamen, den ein Akzeptanzkriterium in Backticks nennt.

Je Feld: **Bezeichner** (englisch, nach Glossar), **Oberflächenbegriff** (deutsch),
**fachlicher Datentyp** (`_data.md` §6 — Geld, Menge, Gewicht, Prozent sind keine
Fließkommazahlen), **Pflicht oder optional**.

Sie beschreibt **Fachlichkeit, keine Tabellen**. Schlüssel, Pflichtspalten, Indizes und
Migrationen regelt `_data.md`; sie gehören nicht in die Spec.

### 2.2 `_Backend.md`

Führt die **Vorgänge** des Epics auf (`_architecture.md` §7 — Vorgänge statt CRUD), ihre
Vorbedingungen, ihre stabilen Fehlercodes und die ausgelösten Ereignisse. Nur was hier steht,
darf von außen erwartet werden.

### 2.3 `guidelines/_Menüstruktur.md` — gehört keinem Epic

Ordnet jede Ansicht einer **Modulgruppe** aus `[ui.modulgruppen]` zu und legt die Reihenfolge
innerhalb der Gruppe fest. Ohne diesen Eintrag ist eine Ansicht nicht erreichbar — eine Story,
die eine neue Ansicht einführt, ergänzt ihn mit und nennt neben dem Menüpunkt das Epic, aus dem
er kommt.

**Sie liegt bewusst außerhalb der Epics.** Die Navigation ist eine Festlegung über alle Epics
hinweg: Menüpunkt 1.1 kommt aus dem ersten Epic, 1.2 aus dem zweiten, beide stehen in derselben
Gruppe. Lag die Datei im ersten Epic, müsste jedes weitere in fremdes Gebiet schreiben — oder
das erste Epic ginge bei jedem neuen Menüpunkt auf `Modified`, obwohl sich an seiner Fachlichkeit
nichts geändert hat.

**Sie hat keinen `State`.** Sie ist keine Story und trägt keine Akzeptanzkriterien; ihre
Richtigkeit entscheidet sich an der Oberfläche, nicht an einem Meta-Block. §4 gilt für sie nicht.

---

## 3. Story-Format

```markdown
# {Story-Titel}

## Meta
- **State:** Modified | Implemented

## User Story
Als {Rolle} möchte ich {Ziel}, damit {Nutzen}.

## Description
Kurze fachliche Beschreibung: was die Ansicht oder Funktion leistet, wie sie erreichbar ist,
was die wichtigsten Interaktionen sind. Keine technische Umsetzung.

## Akzeptanzkriterien
- …
```

- Die Nummerierung ergibt sich aus dem Dateipfad und wird **nicht** im Titel wiederholt.
- Bei großen Stories dürfen Kriterien in thematische Unterabschnitte (`### …`) gegliedert
  werden; die Testdateien folgen dieser Gliederung (`_test-ui.md`, `_test-api.md`).

---

## 4. Meta-Block und State (verbindlich)

**Zweck:** Auf einen Blick sichtbar machen, welche Stories zum Code passen.

| State | Bedeutung |
|---|---|
| **`Modified`** | Neu angelegt oder seit der letzten Umsetzung geändert; der Code spiegelt die Spec **nicht** (mehr) wider. |
| **`Implemented`** | Spec und Code — Oberfläche, Backend **und Tests** — sind synchron; alle Kriterien sind umgesetzt und geprüft. |

- **Jede inhaltliche Spec-Änderung** an einer `Implemented`-Story setzt den State auf
  `Modified` — auch eine Verschärfung eines Kriteriums oder eine Änderung der Description.
- **`Implemented` erst, wenn auch die Tests stehen.** Ein Kriterium ohne Test ist ein
  Regelverstoß (`_test-strategy.md` §11.2); eine Story ohne durchgängigen Oberflächentest
  ebenso (§11.3). Ohne beides bleibt der State `Modified`.
- **Reine Code-Änderungen ohne Spec-Änderung** (Fehlerbehebung, Umbau, Beschleunigung) ändern
  den State nicht.
- Werden Spec und Code in derselben Sitzung geändert, wird direkt auf `Implemented` gesprungen.
- **Im Zweifel `Modified`.** Drift ist der Hauptfeind.

---

## 5. Glossarpflicht (verbindlich)

Jeder Fachbegriff hat **genau einen** deutschen Oberflächenbegriff und **genau einen**
englischen Code-Bezeichner. Die Zuordnung steht im **Glossar des Projektprofils**
(`PROJEKT.md`) und nirgends sonst.

- **Ohne Glossareintrag kein neuer Begriff.** Der Eintrag entsteht **mit** der Spec, nicht
  danach.
- Ein Begriff wird nicht in zwei Epics unterschiedlich benannt. Fällt beim Schreiben auf, dass
  ein vorhandener Begriff dasselbe meint, wird er verwendet — nicht ein zweiter erfunden.
- Der Oberflächenbegriff ist verbindlich für die Anwendung (`_uiux.md` §2), die
  Endkunden-Dokumentation (`_documentation.md`) und die Testtitel. Der Code-Bezeichner ist
  verbindlich für Entitäten, Felder, Vorgänge und die Integrations-API (`_code.md`,
  `_data.md`, `_integration.md` §3.2).
- Deckt eine Story einen Begriff neu ab, wird das Glossar in derselben Sitzung ergänzt.

---

## 6. Akzeptanzkriterien: INVEST

- **I**ndependent — so unabhängig wie möglich; Verweise auf andere Stories nur, wenn zwingend
  (dann als „siehe S{n}").
- **N**egotiable — beschreibt Verhalten, nicht Umsetzung.
- **V**aluable — liefert einen erkennbaren fachlichen Nutzen.
- **E**stimable — konkret genug, dass der Aufwand abschätzbar ist.
- **S**mall — **atomar**: genau eine prüfbare Aussage. Zusammengesetztes wird gesplittet.
- **T**estable — eindeutiges Erfüllt/Nicht-erfüllt.

### 6.1 Formulierungs-Muster

- ✅ „Das Feld `Name` ist ein Pflichtfeld."
- ✅ „Das Feld `Name` akzeptiert höchstens 200 Zeichen."
- ✅ „Das Feld `Code` ist eindeutig."
- ❌ „`Name`: Pflichtfeld, höchstens 200 Zeichen, eindeutig." *(nicht atomar)*
- ❌ „Alle Eingaben werden geprüft." *(nicht prüfbar — welche?)*
- ❌ „Nur gültige Eingaben werden zugelassen." *(nicht prüfbar)*

### 6.2 Verb-Konventionen

| Formulierung | Wofür |
|---|---|
| „ist sichtbar / ist aktiv / ist deaktiviert" | Zustände der Oberfläche |
| „`{Taste}` löst {Vorgang} aus" | Tastaturbedienung (`_uiux.md` §5) |
| „wird persistiert / wird entfernt" | Datenzustände |
| „das Ereignis `X` wird veröffentlicht" | Ereignisse (`_integration.md` §7) |
| „der Vorgang scheitert mit dem Fehlercode `X`" | Fachliche Fehlschläge (`_design.md` §4.3) |
| „entspricht dem Format `{…}`" | Formate |

### 6.3 Felder, Ereignisse, Aufzählungen

- Feldnamen in Backticks und in der Schreibweise aus `_Datenstruktur.md`.
- Ereignis- und Fehlercodenamen in Backticks.
- Aufzählungswerte in Backticks.

---

## 7. Abgrenzung Oberfläche und Backend

Die Oberfläche ist **Livewire** (serverseitig gerendert, mit Blade und Alpine.js) und ruft
die Application-Schicht **direkt**
auf (`_architecture.md` §4, `_integration.md` §1). Es gibt keinen HTTP-Aufruf zwischen eigener
Oberfläche und eigenem Backend — Kriterien dürfen deshalb keinen voraussetzen.

**Die Regel bleibt trotzdem:** Fachlogik, Prüfung und Eindeutigkeit werden als Verhalten des
**Vorgangs** formuliert; die Oberfläche zeigt dessen Ergebnis an.

- ✅ „Der Vorgang prüft die Eindeutigkeit von `Code`. Bei Konflikt zeigt die Maske die
  Meldung des Vorgangs an."
- ❌ „Die Maske prüft, ob `Code` bereits vergeben ist." *(Fachlogik in der Oberfläche)*
- ❌ „`POST /api/…` liefert `400`." *(die eigene Oberfläche spricht nicht über HTTP; ein
  Statuscode ist nur dann ein Kriterium, wenn die **Integrations-API** Gegenstand der Story
  ist — dann nach `_integration.md` §3.4)*

Sofortprüfungen im Formular sind als Bedienhilfe erlaubt und werden als solche formuliert
(„… wird bereits bei der Eingabe angezeigt"). Autoritativ ist immer der Vorgang.

**Wo ein Kriterium geprüft wird, entscheidet es nicht selbst** — das regelt
`_test-strategy.md` §2. Ein Kriterium wird so formuliert, dass es auf der billigsten Ebene
prüfbar ist.

---

## 8. Eine Firma je Installation in Kriterien (verbindlich)

Je Installation gibt es genau **eine Firma** (`_data.md` §5). Für Kriterien heißt das:

- **Eindeutigkeit gilt installationsweit** und wird nicht weiter qualifiziert — „`Code` ist
  eindeutig" ist ein vollständiges Kriterium.
- **Ein Firmenbezug hat in Kriterien nichts zu suchen.** Formulierungen wie „je Firma",
  „aktive Firma" oder „firmenübergreifend" beschreiben ein Altmuster; taucht eine auf,
  wird die Story geklärt, bevor Code entsteht.
- **Abgleiche und Geschäfte mit einer anderen Firma des Kunden sind eine Integration
  zwischen zwei Installationen** und bekommen eine **eigene Spec** über die
  Integrations-API (`_integration.md` §1): welche Daten, welche Richtung, welcher
  Auslöser, welche Fehlerbehandlung. Sie entstehen nie als Nebenwirkung einer
  Stammdaten- oder Beleg-Story.

---

## 9. Nicht-funktionale Kriterien

Die Zusagen aus `_architecture.md` §9 (Leistung), `_security.md` (Rechte, Nachweisbarkeit) und
`_uiux.md` (Bedienung) gelten **für jede Story**, ohne dass sie dort wiederholt werden. Sie
werden **nicht** in jede Spec kopiert — das erzeugt nur Pflegeaufwand und Widerspruch.

**Wiederholt wird nichts. Aufgenommen wird nur die Abweichung oder die Konkretisierung.**

Ein eigenes Kriterium entsteht genau dann, wenn eine Story:

| Fall | Beispiel eines zulässigen Kriteriums |
|---|---|
| von einem Budget abweicht | „Der Lauf darf bis zu 30 Sekunden dauern und zeigt währenddessen einen Fortschritt an." |
| eine Mengengrenze festlegt | „Die Ansicht bleibt bis 50 000 Sätze bedienbar." |
| ein Recht einführt | „Ohne das Recht `{Recht}` ist der Menüeintrag nicht sichtbar." |
| Nachweispflicht auslöst | „Die Freigabe wird im Audit-Protokoll mit Person und Zeitpunkt festgehalten." |
| das Vier-Augen-Prinzip fordert | „Freigebende und anlegende Person dürfen nicht dieselbe sein." — es ist bewusst nicht Teil des Grundvertrags; seine Einführung ist ADR-pflichtig (`_security.md` §2.2) |
| eine Aufbewahrungs- oder Löschfrist setzt | „Der Satz wird nach {Frist} anonymisiert." |
| eine Sprache oder ein Format erzwingt | „Der Beleg wird in der Sprache des Empfängers erzeugt." |
| eine Wirkung nach außen erzeugt | siehe §10 |
| ein KI-Ergebnis verwendet | „Das Ergebnis wird als Vorschlag gekennzeichnet und vor dem Übernehmen bestätigt." (`_ai.md`) |

Auch nicht-funktionale Kriterien erfüllen INVEST: **prüfbar, mit Zahl und Bezug.** „Die
Ansicht ist schnell" ist kein Kriterium.

**Ist eine Zusage nicht einzuhalten**, wird nicht das Kriterium weichgespült, sondern die
Abweichung entschieden und begründet — bei Architekturzusagen mit einer ADR
(`_architecture.md` §11).

---

## 10. Querschnitts-Features (verbindlich)

Manche Features wiederholen sich über alle Epics, die denselben Mechanismus nutzen. Sie sind
**kein optionales Extra**, sondern Mindest-Lieferumfang. Beim Anlegen eines Epics wird diese
Liste durchgegangen.

### 10.1 Protokoll ausgehender Wirkungen

Sobald ein Epic eine Wirkung **außerhalb** des Systems erzeugt — Nachricht, Datei an einen
Dritten, Aufruf eines Fremdsystems, Übergabe an die Buchhaltung — **muss** es ein eigenes
Feature für die **lesende Nachvollziehbarkeit** dieser Wirkung enthalten.

- Mindestens zwei Stories: eine sortier- und filterbare Übersicht der ausgegangenen Wirkungen
  und eine rein lesende Detailansicht mit dem übertragenen Inhalt und Sprungmarken zu den
  verknüpften Gegenständen.
- Ein Menüeintrag in der zugehörigen Modulgruppe.
- **Protokollierung im Backend allein erfüllt diese Konvention nicht.** Die Aufzeichnung
  existiert technisch meist längst — die lesende Oberfläche ist trotzdem eigenständig zu
  spezifizieren. Fehlt sie, kann im Störungsfall niemand beantworten, ob etwas herausgegangen
  ist.

### 10.2 Vorgangseingang von außen

Nimmt ein Epic Vorgänge von einem Fremdsystem entgegen, braucht es eine Story für die
**Klärfälle** (`_integration.md` §5.4): eine Ansicht der nicht automatisch verarbeitbaren
Eingänge, mit dem Grund und der Möglichkeit, sie nach Korrektur erneut zu verarbeiten. Ein
Eingang, der still liegen bleibt, ist ein verlorener Auftrag.

---

## 11. Verbotsliste

1. Kriterium ohne eindeutiges Erfüllt/Nicht-erfüllt.
2. Zusammengesetztes Kriterium, das mehrere Aussagen bündelt.
3. Kriterium, das die Umsetzung vorschreibt statt das Verhalten.
4. Begriff ohne Glossareintrag im Projektprofil.
5. Feldname, der nicht in `_Datenstruktur.md` steht.
6. Firmenbezug in einem Kriterium („je Firma", „aktive Firma", „firmenübergreifend") — je
   Installation gibt es genau eine Firma (§8).
7. Fachlogik oder Prüfung, die der Oberfläche zugeschrieben wird.
8. HTTP-Statuscode als Kriterium, wo nicht die Integrations-API Gegenstand ist.
9. Wiederholung einer allgemein geltenden Zusage in einer einzelnen Story.
10. Nicht-funktionales Kriterium ohne Zahl oder ohne Bezug.
11. Epic mit Wirkung nach außen ohne lesendes Protokoll-Feature.
12. Story auf `Implemented`, solange Tests fehlen.
13. Story, die eine Ansicht einführt, ohne Eintrag in `guidelines/_Menüstruktur.md` (§2.3).
14. Neue Story zwischen bestehende Nummern eingeschoben.

---

## Verweise

- Übergeordnet, Vorgänge, Leistungsbudgets, ADR-Pflicht: `_architecture.md`
- Von der Idee zur Spec: `_ideas.md`
- Fachliche Datentypen, eine Firma je Installation: `_data.md`
- Prüfebenen und Testpflichten: `_test-strategy.md`
- Oberflächenbegriffe, Tastaturbelegung: `_uiux.md`
- Fehlercodes und Vorgangsmuster: `_design.md`
- Vertrag nach außen: `_integration.md`
- Rechte, Vier-Augen-Prinzip, Audit: `_security.md`
- Glossar, Modulgruppen, Pfade: Projektprofil `PROJEKT.md`
