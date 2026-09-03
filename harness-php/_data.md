# _data.md — Datenmodell- und Datenbankvertrag

Verbindliche Regeln für alles Persistente in allen Produkten und der Plattform. Dieses
Dokument ist dem Architekturvertrag `_architecture.md` untergeordnet und regelt, **wie Daten
aussehen, wie sie geschützt sind und wie sie sich über die Zeit verändern dürfen**.

Datenbankfehler sind die teuersten Fehler in einem ERP: Code lässt sich ersetzen,
Kundendaten nicht. Die Regeln hier sind entsprechend streng.

---

## 1. Geltungsbereich

Gilt für jede Entität, jede Migration und jede Abfrage. Bei Widersprüchen gilt die Rangfolge
aus `_architecture.md` §1 — die Regeldateien gewinnen, danach `_architecture.md`, danach
dieses Dokument.

---

## 2. Datenbank und Namensgebung

- **MySQL 8 oder MariaDB 10.6+** — je Installation genau eines davon; welches, steht im
  Projektprofil unter Betrieb. Eine Datenbank je Kundeninstallation. **Kompatibilitätsregel:**
  Es werden nur Datenbankfunktionen verwendet, die das gewählte System in dieser Version
  kann; Funktionen, die nur eines der beiden kann, brauchen eine ADR und eine Doku-Notiz für
  den Hosterwechsel.
- **Ein Tabellenpräfix je Modul** (`{modul}_`, Modulname in Kleinbuchstaben) — er ersetzt
  das Modul-Schema: Kein Modul liest oder schreibt eine Tabelle mit fremdem Präfix
  (`_architecture.md` §5).
- **Schreibweise: Kleinbuchstaben mit Unterstrichen.** Tabellen im Plural, Spalten im
  Singular:

  ```
  umsatz_auftraege            Spalten: id, auftragsnummer, belegdatum, angelegt_am
  umsatz_auftragspositionen   Spalten: id, auftrag_id, positionsnummer, menge
  ```

- **Tabellen und Spalten heißen deutsch — ohne Umlaute und ohne ß** (ae, oe, ue, ss:
  `geaendert_am`, nie `geändert_am`). Quelle des Namens ist der Oberflächenbegriff aus dem
  verbindlichen Glossar (`_requirements.md`); ein Fachbegriff hat genau eine Entsprechung,
  Begriffe außerhalb des Glossars sind ein Regelverstoß.
- **Die PHP-Bezeichner bleiben englisch** (`_code.md`, Glossar): `SalesOrder::orderNumber`
  wird auf `umsatz_auftraege.auftragsnummer` abgebildet. Weil die Datenbanknamen damit nicht
  aus den PHP-Namen ableitbar sind, steht der Tabellenname **ausdrücklich am Modell**
  (`protected $table = '…'`, `_design.md` §5.2) — eine automatische Namensableitung aus den
  Klassennamen gibt es nicht.
- **Zeichensatz `utf8mb4`.** Kollation (Sortier- und Vergleichsregel) wird bei der
  Einrichtung festgelegt und dokumentiert, damit die Sortierung von Umlauten überall gleich
  ausfällt. Suchen ohne Beachtung der Groß-/Kleinschreibung laufen über eine
  `_ci`-Kollation an der Spalte oder dem Index — eine Umwandlung zur Abfragezeit
  (`LOWER(...)` ohne passenden Index) ist verboten.
- **Alle Zeitpunkte in UTC**, Spaltentyp `timestamp`; die Sitzungs-Zeitzone der Datenbank
  steht auf UTC, die Umrechnung in die Zeitzone der anwendenden Person geschieht
  ausschließlich bei der Anzeige. Ein reines Datum ohne Uhrzeit (Belegdatum, Fälligkeit)
  ist `date`.

---

## 3. Schlüssel

### 3.1 Geschäftsentitäten

- Primärschlüssel ist **`id binary(16)`**, Inhalt eine **UUID Version 7** (zeitlich
  sortiert), im geordneten Binärformat abgelegt, damit die zeitliche Sortierung der
  Schlüssel der Indexsortierung entspricht. In der Anwendung und der Integrations-API wird
  die UUID in Textform geführt und am Modellrand umgewandelt (`_design.md` §5.2).
- Die Kennung wird **in der Anwendung** erzeugt (UUID-v7-Erzeugung des Projekts, PHP-seitig)
  — **nie** von der Datenbank. Grund: der Feld-Client erzeugt Datensätze offline, und die
  Datenübernahme braucht Schlüssel vor dem Einfügen.
- **Keine** datenbankvergebenen Zählnummern für Geschäftsentitäten.
- **Keine zusammengesetzten Primärschlüssel**, ausgenommen reine Zuordnungstabellen ohne
  eigene Identität.
- Fremdschlüsselspalten heißen `{entitaet_singular}_id` — deutscher Entitätsname im
  Singular, z. B. `auftrag_id`.

### 3.2 Ausgelieferte Wertetabellen

Einheiten, Währungen, Steuerschlüssel, Länder, Sprachen und vergleichbare kleine, mit dem
Produkt ausgelieferte Tabellen bekommen einen **Textcode als Primärschlüssel**:

```
einheiten:  code = 'PCS', 'GRM', 'KGM'
waehrungen: code = 'EUR', 'USD', 'CHF'
```

- Höchstens 10 Zeichen, Großbuchstaben, **unveränderlich**. Ein Code wird nie umbenannt; er
  wird stillgelegt und durch einen neuen ersetzt.
- Vorteil: im Beleg und in jeder Datenbankabfrage sofort lesbar, keine Nachschlage-Verknüpfung
  für die Anzeige.

### 3.3 Fachliche Schlüssel

Belegnummer, Artikelnummer, Kunden- und Lieferantennummer sind **eigene Felder**, niemals
Primärschlüssel. Sie sind installationsweit eindeutig (§5) und werden über den
Nummernkreisdienst der Plattform vergeben (§7.4).

---

## 4. Pflichtspalten

Jede Geschäftsentität führt folgende Spalten. Sie werden von der Plattform gesetzt, **nie**
von einem Vorgang oder einer Migration von Hand. Deshalb stehen sie — bis auf `id`, die
fachlich ist — nicht im fachlichen Konstruktionspfad des Modells; die Basiskonventionen des
Moduls setzen sie beim Speichern (`_design.md` §3.1). Braucht eine Fachregel eine dieser
Größen, ist sie nicht mehr technisch und wird zur gewöhnlichen Eigenschaft.

| Spalte | Typ | Bedeutung |
|---|---|---|
| `id` | `binary(16)` | Primärschlüssel, UUID v7 (§3.1) |
| `angelegt_am` | `timestamp` | Zeitpunkt der Anlage |
| `angelegt_von` | `binary(16)` | Anlegende Identität — Person, maschineller Zugang oder Systemidentität (`_architecture.md` §6.1) |
| `geaendert_am` | `timestamp` | Letzte Änderung, leer solange unverändert |
| `geaendert_von` | `binary(16)` | Letzte ändernde Identität |
| `version` | `int unsigned` | Zeilenversion der Nebenläufigkeitsprüfung — bei jedem Speichern um eins erhöht und bei Änderungen mitgeprüft (`_architecture.md` §8, `_design.md` §3.1) |

**Löschkennzeichen** (`geloescht_am`, `geloescht_von`) gibt es **ausschließlich** bei
Stammdatenentitäten, die in Altbelegen referenziert sein können (§9.3). Belege haben es nie.

---

## 5. Eine Firma je Installation

Eine **Firma** ist eine rechtliche Einheit. **Je Installation gibt es genau eine Firma** —
eigene Anwendung, eigene Datenbank, eigener Benutzerstamm. Hat ein Kunde mehrere Firmen,
laufen mehrere vollständig getrennte Installationen. Eine Mehrmandantenfähigkeit innerhalb
der Software gibt es nicht.

### 5.1 Folgen für das Datenmodell

- **Keine Firmen-Spalte, kein Firmenfilter.** Die Grenze der Firma ist die Installation
  selbst. Eine `company_id` an Entitäten, ein automatischer Firmen-Abfragefilter oder ein
  Firmenkontext existieren nicht — sie wären totes Gewicht mit echten Kosten (jeder Index,
  jede Eindeutigkeit, jeder Test müsste sie mitschleppen).
- **Eindeutigkeit gilt installationsweit.** Fachliche Schlüssel (§3.3) und Nummernkreise
  (§7.4) haben keinen Firmenbezug.
- **Die eigenen Firmenstammdaten** — Name, Anschrift, Steuernummern, Bankverbindungen für
  Belegdruck und Buchhaltungsübergabe — sind Konfigurationsdaten (§13): genau **ein**
  Firmenstammsatz je Installation, gepflegt bei der Einrichtung (`_operations.md`).
- **Debitoren- und Kreditorennummer, Zahlungsbedingungen, Kreditlimit und Preiszuordnung
  liegen direkt am Geschäftspartner.** Eine Zuordnungstabelle je Firma gibt es nicht mehr.

### 5.2 Geschäfte und Abgleiche zwischen Firmen desselben Kunden

Verkauf, Umlagerung oder Stammdaten-Abgleich zwischen zwei Firmen desselben Kunden sind
Geschäfte zwischen **zwei Installationen**. Dafür gilt:

- Sie laufen **ausschließlich über die Integrations-API** (`_integration.md`) und werden
  **je Fall als Spec definiert** (`_requirements.md`): welche Daten, welche Richtung,
  welcher Auslöser, welche Fehlerbehandlung.
- Jede Installation bucht **ihre eigenen Belege**; die Zuordnung über die Grenze läuft über
  externe Kennungen (`_integration.md`). Ein Vorgang, der in zwei Installationen
  gleichzeitig schreibt, ist unzulässig.
- Ein direkter Zugriff auf die Datenbank oder das Dateisystem der anderen Installation ist
  verboten — die andere Firma ist ein externes System wie jedes andere.

---

## 6. Fachliche Datentypen

Zwei Grundgrößen stehen im Profil, weil sie je Produkt verschieden sind und über das ganze
Datenmodell hinweg gleich gelten müssen: **Hauswährung** (`[daten] hauswaehrung`) und
**Gewichtsbasis** (`[daten] gewichtBasis`). Sie entscheiden, in welcher Einheit gespeichert und
gerechnet wird — nicht nur, wie angezeigt wird.

| Größe | Typ und Regel |
|---|---|
| **Geldbetrag** | `numeric(19,4)`, immer zusammen mit einem Währungscode. Bei Fremdwährung zusätzlich Umrechnungskurs und Betrag in Hauswährung (`[daten] hauswaehrung`), beide zum Belegzeitpunkt eingefroren. **Niemals Gleitkommazahlen.** |
| **Menge** | `numeric(19,6)`, immer zusammen mit einem Einheitencode |
| **Gewicht** | `numeric(19,4)`, Basiseinheit aus `[daten] gewichtBasis`. Umrechnung in andere Einheiten nur bei der Anzeige |
| **Prozentsatz** | `numeric(9,4)` als Prozentwert, nicht als Faktor (`19,0000` bedeutet 19 %) |
| **Zeitpunkt** | `timestamp` in UTC (§2) |
| **Datum** | `date` |
| **Text** | Immer mit Längenbegrenzung. Die Begrenzung in der Datenbank und die Prüfung im Vorgang sind **identisch** |
| **Wahrheitswert** | Nicht leer, mit Vorbelegung |
| **Aufzählung** | Als **Textcode** gespeichert, nicht als Zahl. In der Datenbank lesbar und über Versionen hinweg stabil |

### 6.1 Fachliche Maßgrößen

Darüber hinausgehende Maßgrößen einer Domäne — Reinheitsgrade, spezifische Gewichte,
Qualitätsstufen und Ähnliches — werden **nicht hier**, sondern in der Datenstruktur der
Specs des jeweiligen Produkts festgelegt. Sie unterliegen dabei den Typregeln dieses
Abschnitts:

- numerisch mit **fester** Genauigkeit, nie als Gleitkommazahl,
- **immer zusammen mit ihrer Einheit** gespeichert,
- Genauigkeit und Einheit einmal je Größe festgelegt und in allen Modulen gleich verwendet,
- ein zweideutiger Fachbegriff bekommt im Glossar **getrennte** Bezeichner je Bedeutung und
  läuft nie über dieselbe Spalte.

### 6.2 Rundung

- Gerundet wird **kaufmännisch** auf die Nachkommastellen der jeweiligen Währung.
- **Je Belegposition wird gerundet**; die Belegsumme ist die Summe der bereits gerundeten
  Positionswerte. So stimmt die ausgewiesene Summe immer mit den sichtbaren Zeilen überein.
- Zwischenergebnisse innerhalb einer Positionsberechnung bleiben ungerundet.
- Die Rundungsregel steht an genau einer Stelle in der Plattform und wird nirgends
  nachgebaut.

### 6.3 Erweiterungsfelder

Kundenspezifische Zusatzfelder (`_architecture.md` §2.2, §10.2) sind der Hauptweg der
Kundenanpassung — was sich dort nicht abbilden lässt, wird Produktmerkmal oder entfällt.
Ein so tragender Baustein braucht einen Vertrag, keinen Freibrief:

- **Typkatalog:** Ein Erweiterungsfeld hat einen der Typen aus §6 — Text mit
  Längenbegrenzung, Zahl mit fester Genauigkeit, Geldbetrag, Datum, Zeitpunkt,
  Wahrheitswert, Aufzählung mit Wertekatalog — oder ist ein **Verweis auf einen Stammsatz**
  (als Kennung; die Verweisregeln aus `_architecture.md` §5.3 gelten unverändert). Andere
  Typen gibt es nicht.
- **Validierung liegt in der Definition:** Pflicht, Länge, Wertebereich und Wertekatalog
  stehen an der Felddefinition und werden vom Plattformbaustein **im Vorgang generisch
  geprüft**. So gilt „Begrenzung und Prüfung identisch" (§6) auch für dynamische Felder.
- **Speicherung** in einer `jsonb`-Spalte an der Entität, nicht in einer
  Schlüssel-Wert-Tabelle je Feld.
- **Suche nur über suchbar gekennzeichnete Felder.** Für ein als **suchbar** definiertes
  Feld legt die Plattform einen Index an (Ausdrucks- oder GIN-Index); nur solche Felder
  erscheinen als Filter- oder Sortierkriterium. Damit gilt „jede Such- und Filterspalte ist
  indiziert" (§10, `_architecture.md` §9) auch hier — ein nicht suchbares Feld wird
  angezeigt, aber nie gefiltert.
- **Sichtbarkeit steht an der Definition:** ob das Feld in Listen, im Belegdruck und in der
  Integrations-API erscheint. Die API liefert Erweiterungsfelder als eigenes Objekt,
  getrennt von den Vertragsfeldern.
- **Schutzklasse ist Pflichtangabe** je Definition (`_ai.md`); ohne sie ist das Feld nicht
  anlegbar.
- **Gebuchte Belege:** Ein Erweiterungsfeld an einem Beleg ist nach dem Buchen nur änderbar,
  wenn die Definition es in die Positivliste buchungsneutraler Felder aufnimmt (§7.3) —
  einschließlich der Audit-Pflicht von dort.
- **Beförderungspfad:** Wird ein Erweiterungsfeld zum Produktmerkmal, wird es eine echte
  Spalte — Migration mit Datenumzug (§11), die Definition wird stillgelegt. Ein
  Nebeneinander von Erweiterungsfeld und gleichbedeutender Spalte ist ein Regelverstoß.

---

## 7. Belege

### 7.1 Aufbau

Jeder Beleg besteht aus **Kopf** und **Positionen**.

- **Kopf:** Belegnummer, Belegart, Status, Belegdatum, Geschäftspartner, Währung und Kurs,
  Summen (Netto, Steuer, Brutto).
- **Position:** Zeilennummer, Artikel oder Leistung, Bezeichnung, Menge und Einheit,
  Einzelpreis, Rabatt, Positionswert, Kontierung (§8), fachspezifische Felder je Produkt.

### 7.2 Werte werden kopiert, nicht verwiesen

Bezeichnung, Preis, Steuersatz und Kontierung werden beim Erfassen **in die Position kopiert**.
Ein Beleg von vor zwei Jahren muss unverändert reproduzierbar sein, auch wenn Artikelpreis
oder Steuersatz sich seither geändert haben. Ein Verweis allein genügt nie.

Ebenso werden **Belegsummen gespeichert**, nicht bei jeder Anzeige neu berechnet.

### 7.3 Status und Unveränderbarkeit

```
Entwurf  →  Freigegeben  →  Gebucht  →  (Storniert)
```

- Im Status **Entwurf** ist ein Beleg frei änderbar.
- Ab **Gebucht** ist der Beleg **unveränderlich**. Keine Änderung an Kopf, Positionen,
  Summen oder Kontierung — auch nicht durch eine Migration.
- **Einzige Ausnahme ist die Positivliste buchungsneutraler Felder.** Das Produkt darf je
  Belegart in `projekt/_domaene.md` Felder benennen, die nach dem Buchen änderbar bleiben —
  etwa Belegtexte, Referenz- und Zuordnungsnummern, Zahlungs- und Liefersperren,
  Zahlungsbedingungen, Mahndaten. **Niemals auf der Liste:** Beträge, Mengen, Steuersätze,
  Kontierung, Geschäftspartner, Belegdatum, Belegnummer — nichts, was Buchung, Bestand oder
  Steuer berührt. Jede Änderung über die Positivliste wird im Audit-Protokoll erfasst
  (§9.2): wer, wann, alter und neuer Wert. Ohne Positivliste ist nichts änderbar; eine
  Rolle oder Sonderberechtigung, die an der Liste vorbei ändert, gibt es nicht.
- **Korrektur erfolgt ausschließlich über Storno und Neuerfassung.** Der Stornobeleg ist ein
  eigener Beleg mit Verweis auf den stornierten. Der anwendenden Person dürfen beide Schritte
  als **ein** Bedienschritt angeboten werden — Storno plus vorbefüllte Kopie als neuer
  Entwurf; das Datenmodell bleibt die Storno-Kette (dasselbe Prinzip wie beim Löschen, §9.3).
- **Ein gebuchter Beleg wird niemals gelöscht.** Weder hart noch weich.

**Nicht jede Belegart nutzt jeden Status, und wann sie bucht, ist fachlich.** Das Statusmodell
kennt zwei Belegrollen; welche Belegart welche Rolle hat, legt das Produkt in
`projekt/_domaene.md` fest:

- **Langlebige Steuerbelege** (etwa ein Auftrag) buchen nicht oder erst am Ende ihres Lebens.
  Sie bleiben in **Freigegeben** änderbar — mit fachlichen Grenzen: Werte, auf die bereits ein
  Buchungsbeleg Bezug genommen hat, sind die Untergrenze. Ihr Abschluss macht sie
  unveränderlich wie Gebucht.
- **Kurze Buchungsbelege** (etwa eine Lieferung oder Rechnung) erzeugen die Wirkung —
  Bestandsbewegung, Forderung, Verbindlichkeit — und buchen sofort oder nach einem fachlichen
  Prüfschritt. Ab dem Buchen gelten die Regeln oben uneingeschränkt.

**Wirkung erst beim Buchen — Verfügbarkeit schon vorher.** Ein Entwurf bewegt keinen
Bestand und erzeugt keine Forderung. Damit er trotzdem sofort bindet, gilt für
bestandswirksame Belegarten das **Reservierungsmuster**: Beim Erfassen der Position wird die
Menge reserviert; der **verfügbare Bestand** ist physischer Bestand minus Reservierungen,
und Verfügbarkeitsprüfungen rechnen gegen ihn. Die Reservierung ist abgeleiteter Zustand,
kein Beleg: Ändert sich der Entwurf, zieht sie mit; wird er verworfen, verfällt sie — ohne
Storno und ohne Belegnummer. Beim Buchen löst die echte Bestandsbewegung sie ab. Der
Buchungszeitpunkt bestandswirksamer Belegarten richtet sich am **physischen Warenfluss**
aus — eine Lieferung bucht beim Warenausgang, nicht beim Erfassen. So bleibt der häufigste
Korrekturfall (Menge falsch erfasst, vor dem Warenausgang bemerkt) eine schlichte Änderung
am Entwurf statt eines Stornos.

Was in **Freigegeben** änderbar ist, legt das Produkt je Belegart fest. **Ohne Festlegung gilt
Freigegeben als unveränderlich wie Gebucht** — die Änderbarkeit ist die begründete Ausnahme,
nicht die Voreinstellung. Die Nummernvergabe (§7.4) gilt für beide Rollen unverändert.

### 7.4 Nummernkreise

Alle fachlichen Nummern kommen aus **einem** Nummernkreisdienst der Plattform. Selbst
hochzählen ist verboten, und die Vergabe ist nebenläufigkeitssicher über eine kurz gehaltene
Sperre.

**Der Geltungsbereich gehört zum Nummernkreis**, nicht zum Dienst:

| Geltungsbereich | Wofür | Eindeutigkeit | Fortlaufend |
|---|---|---|---|
| **je Belegart** | Belege aller Art | innerhalb der Belegart | **ja — Lücken selten und erklärbar** |
| **je Gattung** | Stammsätze (Artikel, Partner, …) | über die ganze Installation | nein |

- **Belegnummern sind fortlaufend; Lücken bleiben selten und erklärbar.** Steuerlich
  gefordert ist eine **fortlaufende** Nummer, keine lückenlose (§14 UStG; UStAE 14.5 Abs. 10
  stellt ausdrücklich klar, dass Lückenlosigkeit nicht zwingend ist). Eine harte
  Lückenlosigkeitszusage stünde zudem im Widerspruch zur kurz gehaltenen Sperre: wer sie
  garantiert, muss die Vergabe in die Vorgangstransaktion ziehen und serialisiert damit alle
  Freigaben je Belegart über die Sperre auf der Zählerzeile. Es gilt:
  - Die Nummer wird beim **ersten Statusübergang aus dem Entwurf** vergeben — bei der
    Freigabe beziehungsweise, wo eine Belegart keine kennt, beim Buchen (§7.3) —, nie beim
    Anlegen des Entwurfs. Ein verworfener Entwurf hinterlässt so keine Lücke.
  - Scheitert das Speichern **nach** der Vergabe (etwa am Zeilenversionskonflikt), entsteht
    eine Lücke. Das ist zulässig: der Nummernkreisdienst **protokolliert jede Vergabe**,
    damit jede Lücke über das Protokoll erklärbar ist.
  - Ab der Vergabe zählt der Beleg und wird storniert statt entfernt.
  - Verlangt ein Profil ausdrücklich harte Lückenlosigkeit
    (`[daten] belegnummernLueckenlos`), wird die Nummer **innerhalb der Vorgangstransaktion**
    vergeben. Die Serialisierung der Freigaben je Belegart und ihre Folge für die
    Budgets (`_architecture.md` §9) sind dann im Profil benannt.
- **Bei Stammsatznummern wird auch Fortlaufen nicht verlangt.** Eine Lücke ist dort ohne
  Bedeutung, und die Zusage würde jede Anlage über dieselbe Sperre serialisieren. Gefordert
  ist allein Eindeutigkeit.
- **Debitoren- und Kreditorennummern** liegen direkt am Geschäftspartner (§5.1) und kommen
  aus eigenen Nummernkreisen ihrer Gattung.

---

## 8. Kontierung

Das Produkt führt **keine eigene Finanzbuchhaltung**. Es erzeugt buchungsfähige Daten und
übergibt sie an das Buchhaltungssystem des Kunden (`_integration.md` §9); dessen Format steht
im Projektprofil.

### 8.1 Ort der Kontierung

Die Kontierung liegt auf der **Belegposition**, nicht auf dem Belegkopf. Verschiedene Artikel
und Leistungen desselben Belegs können auf unterschiedliche Konten laufen.

Je Position: **Sachkonto**, **Steuerschlüssel**, optional **Kostenstelle** und
**Kostenträger**.

### 8.2 Vorbelegung und Überschreiben

Die Vorbelegung wird beim Erfassen der Position abgeleitet, in dieser Reihenfolge:

1. **Artikel beziehungsweise Leistung** liefert die Erlös- oder Aufwandskontengruppe.
2. **Geschäftspartner** liefert Debitoren- oder Kreditorenkonto sowie
   steuerliche Besonderheiten (Inland, EU mit Umsatzsteuer-Identifikationsnummer, Drittland).
3. **Der Kontenrahmen der Installation** löst die Kontengruppe in ein konkretes Konto auf.

Die abgeleiteten Werte sind in der Position **überschreibbar**. Eine manuell gesetzte
Kontierung wird als solche gekennzeichnet und bei einer erneuten Ableitung **nicht**
überschrieben.

### 8.3 Einfrieren

Mit dem Buchen wird die Kontierung eingefroren und ist danach unveränderlich. Eine spätere
Änderung an Artikelstamm oder Kontenrahmen wirkt ausschließlich auf neue Belege.

### 8.4 Kontenrahmen

Je Installation gibt es **einen** Kontenrahmen — die Firma der Installation ist in der
Buchhaltung ein eigener Mandant. Mit dem Produkt wird ein Rahmen als **Vorlage**
ausgeliefert; bei der Einrichtung der Installation wird daraus der Kontenrahmen erzeugt
und kann anschließend angepasst werden.

---

## 9. Historisierung, Audit und Löschung

### 9.1 Was historisiert wird

- **Belege** sind ab dem Buchen unveränderlich und brauchen deshalb keine zusätzliche
  Versionierung. Änderungen über die Positivliste buchungsneutraler Felder (§7.3) werden
  im Audit-Protokoll (§9.2) erfasst, nicht über Versionen.
- **Stammdaten mit fachlicher Wirkung** werden versioniert oder mit Gültigkeitszeitraum
  geführt: Preise, Kontierungsvorgaben, Steuersätze sowie die fachlichen Kurse und Sätze des
  Produkts (`projekt/_domaene.md`).
- **Alle übrigen Änderungen** werden über das Audit-Protokoll erfasst, nicht durch eigene
  Historientabellen je Entität.

### 9.2 Audit-Protokoll

- Wird von der Plattform geschrieben, nicht vom Modul.
- Inhalt: wer, wann, welcher Vorgang, welche Entität, alter und neuer Wert,
  Korrelations-ID.
- **Nur Einfügen.** Änderungen und Löschungen sind auf Datenbankebene entzogen.
- Umfang der protokollpflichtigen Vorgänge: `_security.md`.

### 9.3 Löschung

- **Belege werden nie gelöscht** (§7.3).
- **Stammdaten** werden weich gelöscht, wenn sie in Altbelegen referenziert sein können. Weich
  gelöschte Stammdaten sind nicht mehr auswählbar, bleiben in Altbelegen aber sichtbar.
- Hartes Löschen ist nur bei Datensätzen zulässig, die nie referenziert wurden und keine
  buchungsrelevante Wirkung hatten.
- **Wie „nie referenziert" festgestellt wird.** Innerhalb eines Moduls entscheidet die
  Datenbank: der Fremdschlüssel verweigert das Löschen. Über Modulgrenzen gibt es ihn nicht
  (`_architecture.md` §5.3), und das besitzende Modul kennt die verweisenden nicht — dort
  antwortet der **Verwendungsnachweis** der Plattform (`_architecture.md` §6). Er antwortet
  **über alle Module der Installation**
  (`_architecture.md` §6.1). Er wird vor dem Löschen gefragt; erst seine leere Antwort macht
  den Satz hart löschbar. Ohne diese Auskunft ist über Modulgrenzen nicht entscheidbar, ob
  ein Satz je referenziert war — dann bleibt nur weiches Löschen.
- **Hartes Löschen ist zweistufig**, weil Auskunft und Löschen nicht transaktional verbunden
  sind: zwischen leerer Auskunft und Commit kann nebenläufig ein neuer Verweis entstehen —
  die Existenzprüfung des schreibenden Vorgangs sieht den Satz ja noch. Deshalb: erst
  **sperren** (nicht mehr auswählbar, nicht mehr referenzierbar), nach einer **Karenzfrist**
  erneut fragen, erst bei erneut leerer Auskunft endgültig löschen. Für die anwendende
  Person bleibt es **ein** Bedienschritt; die zweite Stufe erledigt die Plattform im
  Systemkontext (`_architecture.md` §6.1).
- **Der Nachweis entscheidet, nicht die anwendende Person.** War der Satz in Verwendung, wird
  das harte Löschen verweigert und die Verwendung benannt; die anwendende Person deaktiviert
  stattdessen. Zwei Löschfälle also, aber nur ein Bedienschritt.
- **Aufbewahrungspflicht:** Buchungsrelevante Daten unterliegen den handels- und
  steuerrechtlichen Fristen. Ein Löschverlangen nach Datenschutzrecht kann diese Daten nicht
  entfernen; es führt zu Sperrung und Einschränkung der Verarbeitung. Das Zusammenspiel
  regelt `_security.md`.

### 9.4 Dokumente und Ablage

Die Ablage von Dokumenten — erzeugte Ausgangsdokumente wie Beleg-PDFs ebenso wie extern
eingehende — ist ein **Plattformbaustein** (`_architecture.md` §2.2). Ein DMS-Fachmodul
(Verschlagwortung, Suche, Mappen, Berechtigungen) baut darauf; seine Fachlichkeit wird je
Produkt spezifiziert und ist hier bewusst nicht geregelt.

- **Ablageort:** Dokumente liegen in einem **eigenen Ablagebereich des Container-Verbunds**,
  nicht in Datenbanktabellen; die Datenbank führt je Dokument Metadaten, Prüfsumme und
  Verweis. Grund: Datenbankgröße, Migrations- und Sicherungslaufzeiten. Eine andere Ablage
  erfordert eine ADR.
- **Einmal archiviert ist ein Dokument unveränderlich.** Korrektur erfolgt durch eine neue
  Version mit Verweis auf die vorige — nie durch Überschreiben. Das Original bleibt erhalten;
  externe Originale bleiben unverändert, wie es die Rohdatensatz-Regel für Eingänge vormacht
  (`_integration.md` §5).
- **Prüfsumme bei der Aufnahme**, gespeichert in den Metadaten. Die wiederkehrende
  Konsistenzprüfung meldet veränderte Dateien, Metadaten ohne Datei und Dateien ohne
  Metadaten.
- **Das tatsächlich versendete Ausgangsdokument wird beim Versand archiviert** und über
  seine Kennung mit dem Beleg verknüpft. Die Reproduzierbarkeit aus den Belegdaten (§7.2)
  ersetzt das nicht: Maßgeblich ist das Dokument, das der Empfänger wirklich erhalten hat,
  Layout eingeschlossen.
- **Aufbewahrung je Dokumentart** legt das Produkt in `projekt/_domaene.md` fest. Für das
  Spannungsfeld Löschverlangen gegen Aufbewahrungspflicht gilt §9.3 beziehungsweise
  `_security.md` §6.3; Löschung nach Fristablauf ist möglich und protokollpflichtig.
- **Schutzklassen gelten für Dokumente wie für Daten** — relevant, sobald KI-Anwendungsfälle
  Dokumente lesen (`_ai.md` §6).
- **Verknüpfungen** von Dokumenten zu Belegen, Partnern oder Vorgängen sind gewöhnliche
  modulübergreifende Verweise: Kennung, Existenzprüfung, Auskunftsstelle
  (`_architecture.md` §5.3).
- **Rechtssicher ist die Installation, nicht die Software allein:** Revisionssichere
  Archivierung setzt eine **Verfahrensdokumentation** der Installation voraus; sie gehört
  zur Betriebsdokumentation (`_operations.md` §8).

---

## 10. Indizes und Abfragen

- **Pflichtindizes:** Primärschlüssel, jeder Fremdschlüssel, **jede modulübergreifende
  Verweisspalte** (Kennungen ohne Fremdschlüsselzwang, §5 in `_architecture.md`) sowie jede
  Spalte, nach der in einer Liste gefiltert oder sortiert wird. Die Verweisspalten stehen
  hier ausdrücklich, weil Auskunftsstelle und Konsistenzprüfung genau über sie suchen —
  ohne Index wird jede Löschprüfung zum Tabellenscan.
- **Eindeutigkeit** wird über Datenbankindizes erzwungen, nicht nur im Vorgang geprüft.
- **Keine Abfrage ohne Obergrenze.** Standardseitengröße 50, Höchstwert 200
  (`_architecture.md` §9).
- **Lesende Abfragen projizieren direkt auf den Ausgabevertrag** — nur die benötigten
  Spalten (`select(...)`/Projektion, `_design.md` §4.4). Kein Laden vollständiger Modelle
  zum Anzeigen.
- **Kein N+1.** Benötigte Verknüpfungen werden in derselben Abfrage aufgelöst.
- **Suchmuster mit führendem Platzhalter** sind ohne Volltextindex verboten.
- **Partitionierung** großer Tabellen erst nach Messung und mit ADR — nicht vorsorglich.

---

## 11. Migrationen

- **Laravel-Migrationen**, ein eigener Migrationsstrang je Modul (eigener Ordner, eigener
  Tabellenpräfix — `_design.md` §5.1).
- **Erzeugte Migrationen werden nicht von Hand geändert**, außer für einen bewusst
  eingefügten Datenumzug. Dieser wird kommentiert und getestet.
- **Jede Migration muss auf Bestandsdaten laufen.** Eine Änderung, die vorhandene Daten
  verlieren oder ungültig machen würde, ist ohne begleitenden Datenumzug unzulässig.
- **Spalten und Tabellen werden umbenannt, nicht gelöscht und neu angelegt** — sonst gehen
  Daten verloren.
- **In Produktion läuft die Migration als gesteuerter Schritt**, nicht automatisch beim Start
  der Anwendung. Ablauf: Sicherung → Migration → Prüfung → Freigabe. Automatische Migration
  beim Start ist ausschließlich in der Entwicklung zulässig.
- **Eine Rückwärtsmigration wird nicht zugesichert.** Die Rückfallebene ist die Sicherung vor
  der Migration; sie ist Pflicht (`_operations.md`).

### 11.1 Sperren und Laufzeit

Eine Migration hält Sperren auf den betroffenen Tabellen. Bei einer Tabelle mit Millionen
Zeilen bedeutet das Minuten, in denen niemand arbeiten kann.

- **Das Sperrverhalten jeder Schemaänderung auf großen Bestandstabellen wird geprüft.**
  MySQL/MariaDB führen viele Fälle mit Online-DDL aus (ohne Vollsperre), kopieren aber bei
  anderen die gesamte Tabelle — welcher Fall vorliegt, steht in der Freigabenotiz der
  Migration. Wo eine Änderung sperrt oder kopiert, wird sie zerlegt oder ins
  Wartungsfenster gelegt.
- **Jede Migration wird vor der Freigabe auf einer Kopie der Kundendaten gemessen.** Die
  erwartete Laufzeit gehört in die Freigabenotiz.
- **Migrationen mit einer erwarteten Laufzeit über 60 Sekunden laufen ausschließlich im
  Wartungsfenster.** Kürzere dürfen im regulären Aktualisierungsschritt laufen.
- **Schemaänderungen werden nach Möglichkeit erweiternd statt verändernd umgesetzt**
  (erst hinzufügen, später entfernen). Das hält die Sperrzeiten kurz und erlaubt eine
  Rückkehr zur Vorversion ohne Rückwärtsmigration (`_operations.md`).

---

## 12. Datenübernahme aus dem Altsystem

Die Ablösung eines Bestandssystems ist Teil des Lieferumfangs und wird wie ein Fachmodul
behandelt, nicht wie ein einmaliges Skript.

### 12.1 Ablauf

```
Auslesen  →  Rohdaten laden  →  Abbilden  →  Prüfen  →  Übernehmen  →  Abgleichen
```

- Rohdaten und Abbildungen liegen in **eigenen Tabellen mit dem Präfix `uebernahme_`**,
  getrennt von den Produktivtabellen aller Module.
- Der Lauf ist **wiederholbar**. Eine **Zuordnungstabelle** hält je Altsystem-Kennung die
  vergebene UUID fest, damit eine Wiederholung dieselben Schlüssel erzeugt und keine
  Dubletten entstehen.
- Jede übernommene Entität trägt dauerhaft **Altsystem-Kennung und Altsystem-Name**. Diese
  Angaben werden nicht nach dem Umstieg entfernt — sie sind die Grundlage jeder späteren
  Klärung.

### 12.2 Belegnummern und Nummernkreise

Belegnummern aus dem Altsystem werden **übernommen**, nicht neu vergeben. Nach der Übernahme
wird jeder Nummernkreis auf den höchsten übernommenen Wert gesetzt, damit die Fortschreibung
ohne Kollision anschließt.

### 12.3 Prüfung und Abnahme

- **Abgleichsprotokoll** mit Zeilenanzahlen je Entität, Bestandssummen je Artikel und Lager,
  Summen der offenen Posten je Konto und einer Liste aller Abweichungen.
- **Abnahmekriterium:** Bestandssummen und offene Posten stimmen mit dem Altsystem überein.
  Ohne dieses Ergebnis erfolgt kein Umstieg.
- Da die Übernahme direkt in die Tabellen schreibt und nicht durch die Vorgänge läuft, ist
  eine **abschließende Invariantenprüfung** verpflichtend: Pflichtfelder,
  Eindeutigkeiten, Verweise über Modulgrenzen (`_architecture.md` §5.3).
- Welche Altdaten übernommen und welche nur archiviert werden, ist eine fachliche
  Entscheidung und wird dokumentiert.

---

## 13. Auslieferungsdaten, Konfiguration und Testdaten

Drei Klassen, die klar getrennt bleiben:

| Klasse | Beispiel | Herkunft |
|---|---|---|
| **Auslieferungsdaten** | Einheiten, Währungen, Steuerschlüssel, Kontenrahmenvorlage, Sprachen | Teil der Migration, versioniert mit dem Produkt |
| **Konfigurationsdaten** | Firmenstammdaten (§5.1), Nummernkreise, Belegvorlagen, Textbausteine, Preislisten | Vom Kunden gepflegt, nie vom Produkt überschrieben |
| **Testdaten** | Alles, was Tests brauchen | Ausschließlich in den Tests, nie in der Produktivdatenbank |

Das bisherige Muster, bei dem **jede Entität ein paar Beispielzeilen mitliefert**, entfällt
ersatzlos. Testdaten entstehen über Erzeuger in den Tests (`_test-strategy.md`), nicht über
mitgelieferte Datensätze im Produktivcode.

---

## 14. Verbotsliste

1. Datenbankvergebene Zählnummer als Primärschlüssel einer Geschäftsentität.
2. Gleitkommazahl für Geldbeträge, Mengen oder Gewichte.
3. Geldbetrag ohne Währung, Menge ohne Einheit.
4. Zeitpunkt ohne Zeitzone oder in lokaler Zeit gespeichert.
5. Mehr als eine Firma in einer Installation oder Datenbank — Firmentrennung ist
   Installationstrennung (§5).
6. Firmen-Spalte (`company_id`), Firmenfilter oder Firmenkontext im Datenmodell — Altmuster
   der aufgegebenen Mehrmandantenfähigkeit.
7. Abgleich oder Geschäft zwischen zwei Firmen an der Integrations-API vorbei — etwa direkt
   über deren Datenbank (§5.2).
8. Änderung an einem gebuchten Beleg außerhalb der Positivliste buchungsneutraler Felder
   (§7.3) — auch durch Migration oder Sonderberechtigung.
9. Löschen eines Belegs, hart oder weich.
10. Selbst hochgezählte Belegnummer.
11. Kontierung am Belegkopf statt an der Position.
12. Verweis auf Artikelpreis, Bezeichnung, Steuersatz oder Kurs statt Kopie in die Position.
13. Text- oder Zahlenspalte ohne Längen- beziehungsweise Wertebegrenzung.
14. Aufzählung als Zahl gespeichert.
15. Abfrage ohne Obergrenze der Ergebnismenge.
16. Vollständige Entität geladen, wo eine Projektion genügt.
17. Handänderung an einer erzeugten Migration ohne kommentierten Datenumzug.
18. Automatische Migration beim Anwendungsstart in Produktion.
19. Beispieldaten im Produktivcode.
20. Übernahmelauf ohne Zuordnungstabelle, ohne Abgleichsprotokoll oder ohne
    Invariantenprüfung.
21. Überschreiben oder Ändern eines archivierten Dokuments — Korrektur nur als neue Version
    (§9.4).
22. Dokumentenablage außerhalb der gemeinsamen Sicherung und Wiederherstellungsprobe
    (`_operations.md` §6).
23. Englischer Tabellen- oder Spaltenname, Umlaut oder ß in einem Datenbanknamen —
    Tabellen und Spalten heißen deutsch ohne Umlaute (§2).

---

## Verweise

- Übergeordnet: `_architecture.md`
- Rechte, Audit-Umfang, Aufbewahrung und Datenschutz: `_security.md`
- Buchhaltungsübergabe, Offline-Abgleich, externe Schnittstellen: `_integration.md`
- Schutzklassen für KI-Verarbeitung: `_ai.md`
- Umsetzungsmuster je Entität: `_design.md`
- Testdaten und Testisolation: `_test-strategy.md`
- Sicherung, Migrationsablauf beim Kunden: `_operations.md`
