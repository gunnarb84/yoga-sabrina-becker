# _test-ui.md — Oberflächentests (Playwright)

Regeln für durchgängige Oberflächentests gegen die laufende Anwendung im echten Browser.

Übergeordnet: **`_test-strategy.md`** — dort stehen die tragenden Regeln, die Ebenenzuordnung,
die Verbotsliste und die Laufzeitbudgets. Dieses Dokument ergänzt sie nur um das, was für die
Oberfläche eigen ist. Bei Abweichung gilt `_test-strategy.md`.

Gestaltung und Tastaturbelegung, gegen die hier geprüft wird: `_uiux.md`.

---

## 1. Was ein Oberflächentest prüft — und was nicht

Die Oberfläche ist der Ort, an dem eine Story als erfüllt gilt (`_test-strategy.md` §1). Ein
Oberflächentest weist deshalb den **Ablauf** nach, nicht die Fachlichkeit dahinter:

| Prüft | Prüft **nicht** |
|---|---|
| Der beschriebene Weg durch die Story von Anfang bis Ende | Berechnung, Fachregel, Statusübergang → Domänentest |
| Navigation, Sprung zwischen Liste und Maske, Tabverhalten | Persistenz, Nummernkreis → Modul-Integrationstest |
| Tastaturbedienung nach `_uiux.md` §5 | Fehlercodes, Idempotenz → API-Vertragstest |
| Dass ein vom Backend geliefertes Ergebnis angezeigt wird | Ob das Ergebnis fachlich richtig ist |

**Je Story mindestens ein durchgängiger Test.** Fehlt er, gilt die Story als nicht umgesetzt —
auch wenn alle anderen Ebenen grün sind (`_test-strategy.md` §11.3).

**Nicht jedes Kriterium wird hier geprüft.** Ein Kriterium wandert auf die billigste Ebene, die
es entscheidet. Wer eine Preisstaffel im Browser prüft, verstößt gegen `_test-strategy.md`
§11.4. Sichtbarkeit, Aktivierung eines Feldes oder das Erscheinen einer Meldung sind
**Komponententests** (`_test-strategy.md` §2) und gehören nur dann hierher, wenn sie Teil des
durchgängigen Ablaufs sind.

---

## 2. Herkunft der Testfälle

- **Ausschließlich aus Specs und deren Akzeptanzkriterien.** Quelle ist immer eine Story unter
  `[pfade] specs` (`E{n} …/F{n} …/S{n} ….md`). Ein Testwunsch beginnt mit einer Spec-Änderung
  (`_requirements.md`), nicht mit einem Test.
- **Ein Story-Dokument → genau eine Testdatei.**
- **Jede Datei beginnt mit einem Kopfkommentar**, der den Spec-Pfad nennt.
- **Jeder Test nennt sein Kriterium** — im Titel möglichst am Wortlaut, sonst als Kommentar.
- Kriterien, die auf **keiner** Ebene sinnvoll prüfbar sind, stehen mit Begründung am Ende der
  Datei. Stillschweigen gilt als Lücke (`_test-strategy.md` §10).

---

## 3. Ablage und Namensgebung

Wurzel ist `[pfade] uitests` aus dem Projektprofil.

```
{uitests}/
├── package.json
├── playwright.config.ts        ← Konfiguration; parallel, kein einzelner Worker
├── fixtures/
│   ├── kennung.ts              ← testeindeutige Kennung für alle Testdaten (§5)
│   ├── anmeldung.ts            ← Anmeldung und Rollenwahl (§6)
│   └── daten.ts                ← Erzeuger für Testdaten über die Integrations-API
├── seiten/                     ← optional: Seitenobjekte je Modul
└── e2e/
    └── E{n}-{epic}/
        └── F{n}-S{n}-{kebab-story-titel}.spec.ts
```

- Dateiname: `F{n}-S{n}-{kebab-story-titel}.spec.ts`, Epic-Ordner darüber.
- `test.describe`-Titel nennen Story und Kriteriengruppe; Gruppen folgen den
  `###`-Unterabschnitten der Story, sonst fachlich gebildet.
- Jeder `test(...)`-Titel formuliert **ein** prüfbares Kriterium.

---

## 4. Datenbank

- **MySQL oder MariaDB im Container, dasselbe System und dieselbe Hauptversion wie in
  Produktion** — kein Ersatz, keine speicherinterne Datenbank (`_test-strategy.md` §5.1). Was
  Oberflächentests durchlaufen — Sortierfolge, Nummernkreise — verhält sich sonst anders.
- **Eigene Test-Datenbank**, nie die Entwicklungs- oder Produktionsdatenbank.
- **Die Migrationen laufen im Testaufbau.** Auslieferungsdaten (Einheiten, Währungen,
  Steuerschlüssel) kommen aus der Migration, nicht aus Testcode (`_data.md` §13).

---

## 5. Isolation: eigene, testeindeutige Daten je Test

Die Transaktionsrückrollung der Modul-Integrationstests steht hier nicht zur Verfügung — der
Browser spricht mit einem eigenständigen Anwendungsprozess. **Ein globales Zurücksetzen vor
jedem Test ist trotzdem verboten** (`_test-strategy.md` §11.6): es erzwingt den
Ein-Worker-Betrieb und skaliert nicht.

Alle Tests teilen sich die eine Test-Datenbank der Installation — Firmen als Trennwände
gibt es nicht mehr (`_data.md` §5). **Isolation entsteht über die Daten selbst:**

- Eine automatische Fixture vergibt **je Test eine eindeutige Kennung**; alle fachlichen
  Bezeichner der Testdaten (Namen, Codes, Suchbegriffe) tragen sie. So kollidieren
  parallele Tests weder an Eindeutigkeitsregeln noch in Listen.
- Alle Testdaten entstehen über die Integrations-API oder über die Oberfläche — nie durch
  direkten Datenbankzugriff.
- **Ein Test behauptet nie etwas über den Gesamtbestand.** Listen werden über die
  testeigene Kennung gefiltert oder gesucht geprüft, nie über absolute Zeilenzahlen —
  nebenläufige Tests schreiben in denselben Bestand.
- Damit laufen Tests **parallel** und in beliebiger Reihenfolge. Parallelisierung ist Pflicht
  (`_test-strategy.md` §9).
- Aufräumen ist nicht nötig: testeindeutige Daten stören einander nicht. Die Test-Datenbank
  wird als Ganzes verworfen, nicht satzweise.
- **Kein gespiegelter Seed-Datensatz mit Erwartungskonstanten** (`_test-strategy.md` §7). Ein
  Test legt an, was sein Kriterium braucht, und prüft gegen das, was er selbst angelegt hat.

---

## 6. Anmeldung und Rechte

- Die Anwendung ist angemeldet zu bedienen (`_security.md`). Die Anmeldung gehört in eine
  Fixture, nicht in jeden Test.
- **Der Anmeldeweg selbst wird einmal geprüft**, in der Story, die ihn beschreibt. Alle
  übrigen Tests übernehmen einen vorbereiteten Sitzungszustand
  (`storageState`), statt die Maske erneut zu durchlaufen.
- **Rechte werden hier nur geprüft, wo die Story sie sichtbar macht** — etwa ein
  ausgeblendeter Menüeintrag. Dass ein Vorgang ohne Recht scheitert, ist ein
  Modul-Integrationstest (`_test-strategy.md` §5.3).

---

## 7. Livewire: was hier anders ist

Die Oberfläche wird serverseitig gerendert und über eine dauerhafte Verbindung aktualisiert.
Daraus folgen fünf Regeln, deren Missachtung die häufigste Ursache flatterhafter Tests ist:

1. **Auf die hergestellte Verbindung warten, nicht auf das geladene Dokument.** Nach jeder
   Navigation ist das Markup da, bevor die Komponente auf Eingaben reagiert. Erst wenn die
   Verbindung steht, darf getippt oder geklickt werden. Die Anwendung weist diesen Zustand
   maschinenlesbar aus; die Fixture wartet darauf.
2. **Die Wiederverbindungs-Einblendung ist ein Fehlschlag, kein Wartezustand.** Erscheint sie
   während eines Tests, bricht der Test ab und meldet sie. Sie stillschweigend wegzuwarten
   verdeckt echte Abbrüche.
3. **Kein festes Warten.** `waitForTimeout` ist verboten. Jede Erwartung läuft über `expect`
   mit automatischem Warten. Wer eine feste Pause braucht, hat einen fehlenden Zustand am
   Markup — der wird ergänzt, nicht überbrückt.
4. **Virtualisierte Raster zeigen nur das Sichtbare** (`_uiux.md`). Die Zahl der Zeilen im
   Markup ist **nicht** die Trefferzahl. Geprüft wird über die Trefferanzeige der Liste oder
   über Filtern auf den erwarteten Satz — nie durch Zählen gerenderter Zeilen.
5. **Besuchsverlauf über `page.goBack()`/`page.goForward()` prüfen, nie über die URL.** Die
   Adresszeile ändert sich nicht (`_uiux.md` §4a); eine Erwartung an die URL prüft nichts.
   Geprüft wird die danach aktive Ansicht. Löst `goBack()` ein Neuladen des Dokuments aus,
   ist das ein Fehlschlag, kein Wartezustand — der Verlauf arbeitet ohne Dokumentwechsel,
   sonst bricht die Verbindung (Regel 1 und 2 greifen dann zu Recht).

---

## 8. Locator und Assertions

- **Rollenbasierte Locator** (`getByRole`, `getByLabel`, `getByText`). Sie koppeln den Test an
  das vom Kriterium beschriebene Verhalten statt an das Markup — und sie prüfen nebenbei die
  Barrierefreiheit mit, die `_uiux.md` fordert.
- **Keine Selektoren auf Gestaltungsklassen.** Die Basisklassen aus `reference.css` sind
  Gestaltung, keine Testschnittstelle. Ändert sich die Vorlage, dürfen keine Tests brechen.
- Ein eigenes Testattribut nur dort, wo es keine zugängliche Bezeichnung geben kann — und dann
  mit einem Kommentar, warum.
- **Zahlen und Datumsangaben** werden in der Anzeigeform der Oberfläche geprüft (`_uiux.md`),
  nicht in der Rohform des Modells.
- Ein Test prüft genau **ein** Kriterium.
- Fehlermeldungen kommen laut `_architecture.md` §4 aus der Application-Schicht; der Test
  prüft, dass die Oberfläche sie **anzeigt**, nicht ihren Wortlaut als Fachregel.

---

## 9. Tastaturbedienung

Die durchgängige Tastaturbedienung ist eine zugesagte Eigenschaft der Oberfläche
(`_uiux.md` §5) und damit prüfpflichtig, nicht optional.

- **Der durchgängige Test einer Story läuft den Hauptweg über die Tastatur**, nicht über die
  Maus. Die Maus wird geprüft, wo die Story sie ausdrücklich beschreibt.
- Die belegten Funktionstasten werden in dem Modul geprüft, in dem sie eine Wirkung haben —
  jeweils genau einmal, nicht in jeder Story erneut.
- Dass eine im Browser vorbelegte Taste von der Anwendung übernommen wird, ist ein eigenes
  Kriterium und braucht einen eigenen Test.

---

## 10. Ausführung

Beide Betriebsarten müssen ohne Handarbeit funktionieren:

- **Kopfüberlos (Übersetzungsstrecke und Kommandozeile):** der Standardlauf.
- **Sichtbar im Testläufer:** derselbe Lauf mit Oberfläche, zum Nachvollziehen eines Fehlers.

- Playwright startet den Anwendungsprozess und die Container-Datenbank selbst und wartet, bis
  beide erreichbar sind. Manuell gestartete Dienste sind nicht nötig.
- **Ports, Umgebungsvariablen und Verbindungszeichenfolgen stehen nicht in diesem Dokument** —
  sie sind Betriebswerte (`_operations.md`, Projektprofil). Der Testaufbau liest sie von dort.
- Test-Anwendung und Test-Datenbank sind von einem laufenden Entwicklungsstand getrennt.
- **Keine automatische Wiederholung fehlgeschlagener Tests** (`_test-strategy.md` §11.11). Ein
  flatterhafter Test wird repariert oder entfernt.
- Die vollständige Suite läuft nächtlich und vor jeder Freigabe (`_test-strategy.md` §9).

---

## 11. Pflege

- Ändert sich eine Story, werden ihre Tests **in derselben Sitzung** angepasst.
- Neue Story → neue Datei nach §3.
- **Entfällt ein Kriterium, entfällt sein Test** (`_test-strategy.md` §11.14).
- Wird die Suite langsamer als ihr Budget, wandern Kriterien auf eine billigere Ebene. Die
  Ausführung wird nicht ausgedünnt.

---

## Verweise

- Übergeordnet, Ebenen und Verbotsliste: `_test-strategy.md`
- Gestaltung, Tastaturbelegung, virtualisierte Raster: `_uiux.md`
- Akzeptanzkriterien und Story-Struktur: `_requirements.md`
- Auslieferungsdaten: `_data.md`
- Anmeldung und Rechte: `_security.md`
- API-Vertragstests: `_test-api.md`
- Ports, Umgebungen, Betriebswerte: `_operations.md`
