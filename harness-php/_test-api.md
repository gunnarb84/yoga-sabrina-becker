# _test-api.md — API-Vertragstests (PHP/Pest)

Regeln für Tests gegen die **Integrations-API** — die einzige Tür von außen
(`_integration.md` §1).

Übergeordnet: **`_test-strategy.md`** — dort stehen die tragenden Regeln, die Ebenenzuordnung,
die Verbotsliste und die Laufzeitbudgets. Dieses Dokument ergänzt sie nur um das, was für die
API eigen ist. Bei Abweichung gilt `_test-strategy.md`.

Der geprüfte Vertrag selbst steht in `_integration.md`.

---

## 1. Was hier geprüft wird — und was nicht

**Gegenstand ist der Vertrag nach außen**, nicht die Fachlichkeit dahinter.

Die eigene Oberfläche ruft die Application-Schicht direkt auf und geht **nicht** über die
Integrations-API (`_integration.md` §1). Ein Kriterium, das nur die eigene Anwendung betrifft,
gehört deshalb **nicht** hierher, sondern in den Modul-Integrationstest. Die frühere
Arbeitsteilung „Oberflächentests prüfen vorn, API-Tests prüfen hinten" gilt nicht mehr.

| Prüft | Prüft **nicht** |
|---|---|
| Form und Stabilität des Vertrags: Statuscodes, Fehlerformat, Listenformat (`_integration.md` §3) | Fachregel und Berechnung → Domänentest |
| Idempotenz wiederholter Aufrufe (§4) | Persistenz, Nummernkreis → Modul-Integrationstest |
| Auftragseingang samt Prüfungen, Klärfällen und doppelter Übertragung (§5) | Ablauf einer Story in der Oberfläche → Oberflächentest |
| Abbildung externer Kennungen (§6) | Interna: Application-Verträge, Modelle, Domänenmethoden |
| Ausgehende Ereignisse, Zustellung, Wiederholung, Webhooks (§7) | |
| Abgleichsprotokoll des Feld-Clients (§10) | |
| Abwärtskompatibilität innerhalb einer Hauptversion (§2) | |

**Getestet wird ausschließlich über HTTP.** Kein direkter Zugriff auf Modelle, Vorgänge oder
sonstige Interna — sonst prüft der Test nicht den Vertrag, sondern seine Umgehung.

---

## 2. Herkunft der Testfälle

- **Ausschließlich aus Specs und deren Akzeptanzkriterien** unter `[pfade] specs` — Story oder
  das `_Backend.md` des Epics. Ein Testwunsch beginnt mit einer Spec-Änderung
  (`_requirements.md`), nicht mit einem Test.
- **Ausnahme:** Die Zusagen aus `_integration.md`, zu denen es nie ein Akzeptanzkriterium geben
  wird — Fehlerformat, Statuscode-Zuordnung, Listenobergrenze, Idempotenz, Kompatibilität. Sie
  werden als **Vertragstests** geführt und als solche gekennzeichnet, analog zu den
  Architektur-Konformitätstests in `_test-strategy.md` §6.
- **Ein Spec-Dokument → genau eine Testdatei.** Vertragstests liegen getrennt davon (§3).
- **Jede Datei beginnt mit einem Kopfkommentar**, der die Quelle nennt: Spec-Pfad oder der
  Abschnitt aus `_integration.md`.
- Kriterien der Story, die **kein** API-Verhalten sind, stehen mit Begründung und Verweis auf
  die zuständige Ebene am Ende der Datei. Stillschweigen gilt als Lücke.

---

## 3. Ablage und Namensgebung

Wurzel ist `[pfade] apitests` aus dem Projektprofil (Vorgabe `tests/Api/` im Laravel-
Projekt); der Testrahmen ist Pest, registriert in der `phpunit.xml` des Projekts.

```
{apitests}/
├── Infrastructure/
│   ├── apiFixture.php          ← startet Anwendung und Container-Datenbank, hält den HTTP-Client
│   ├── apiTest.php             ← Basisklasse: eigener Zugang und testeindeutige Daten je Test (§5)
│   └── Erzeuger/               ← Testdatenerzeuger, ausschließlich über die API
├── Vertrag/                    ← Zusagen aus _integration.md, ohne Story
│   ├── FehlerformatTest.php
│   ├── ListenformatTest.php
│   ├── IdempotenzTest.php
│   └── KompatibilitaetTest.php
└── E{n}_{Epic}/
    └── F{n}_S{n}_{StoryPascalCase}Test.php
```

- Dateiname: `F{n}_S{n}_{StoryPascalCase}Test.php`.
- Testbeschreibungen formulieren **ein** prüfbares Kriterium, nah am Wortlaut —
  deutschsprachig erlaubt.
- Zusammengehörige Kriterien einer Story werden gruppiert (`describe()`-Blöcke oder klares
  Namenspräfix), orientiert an den `###`-Unterabschnitten der Story.

---

## 4. Datenbank und Anwendungsprozess

- **Gegen eine tatsächlich laufende Anwendung** (echter Serverprozess, etwa
  `php artisan serve` mit eigener Umgebung oder ein PHP-FPM-Verbund im Container), nicht
  gegen den eingebauten Testwirt (`$this->get(...)` im selben Prozess). Was hier geprüft
  wird — Statuscodes, Zwischenspeicherung, Fehlerbehandlung, Verhandlung des Inhaltstyps —
  verhält sich sonst anders.
- **MySQL oder MariaDB im Container, dasselbe System und dieselbe Hauptversion wie in
  Produktion**, eigene Test-Datenbank (`_test-strategy.md` §5.1). Kein Ersatz, keine
  speicherinterne Datenbank.
- **Die Migrationen laufen im Testaufbau.** Auslieferungsdaten kommen aus den Migrationen
  bzw. den Seeds der Auslieferungsdaten, nicht aus Testcode (`_data.md` §13).
- **Ports, Umgebungsvariablen und Verbindungszeichenfolgen stehen nicht in diesem Dokument** —
  sie sind Betriebswerte (`_operations.md`, Projektprofil). Der Testaufbau liest sie von dort.
- `apiFixture.php` startet Anwendung und Datenbank, wartet auf den Health-Endpunkt
  (`_operations.md`) und beendet beides am Ende. Ein bereits laufender Testverbund kann über
  eine Umgebungsvariable übernommen werden.

---

## 5. Isolation: eigene Daten und eigener Zugang je Test

**Kein globales Zurücksetzen vor jedem Test und kein Ein-Worker-Betrieb**
(`_test-strategy.md` §11.6). Beides skaliert nicht und verbietet die geforderte
Parallelisierung.

Alle Tests teilen sich die eine Test-Datenbank der Installation — Firmen als Trennwände
gibt es nicht mehr (`_data.md` §5). Isolation entsteht über die Daten selbst:

- Jeder Test bekommt einen **eigenen maschinellen Zugang** (`_security.md` §4) und legt
  **seine eigenen Daten** an — über die API, nie durch direkten Datenbankzugriff.
  Fachliche Bezeichner (Namen, Codes, externe Kennungen) tragen eine **testeindeutige
  Kennung**, damit parallele Tests nicht kollidieren und Eindeutigkeitsregeln nicht
  zufällig zuschlagen.
- **Ein Test behauptet nie etwas über den Gesamtbestand.** Listen werden über die
  testeigenen Kennungen gefiltert geprüft („enthält die drei angelegten Belege"), nie über
  absolute Zahlen („enthält genau drei Belege") — nebenläufige Tests schreiben in
  denselben Bestand.
- **Kein gespiegelter Seed-Datensatz mit Erwartungskonstanten** (`_test-strategy.md` §7). Ein
  Test prüft gegen das, was er selbst angelegt hat.
- Rechte werden je Zugang gesetzt. Ein Kriterium „ohne Recht abgewiesen" bekommt einen Zugang
  ohne dieses Recht, statt einen globalen umzuschalten.
- Aufgeräumt wird nicht satzweise; die Test-Datenbank wird als Ganzes verworfen.

---

## 6. Technik

- **Testrahmen:** Pest. **Prüfungen:** Pest-Erwartungen (`expect(…)->…`).
- **HTTP:** Ein HTTP-Client der PHP-Welt (Guzzle oder der Symfony-HTTP-Client) — feste Wahl
  je Projekt, im Testaufbau zentral.
- **Antworten werden roh geprüft, wo die Form Gegenstand ist.** Wer die Antwort in dasselbe
  DTO zurückliest, das der Server serialisiert hat, prüft die Übersetzung mit sich selbst und
  bemerkt weder ein umbenanntes Feld noch eine geänderte Schreibweise. Für Feldnamen,
  Schreibweise und Zeitformat wird deshalb über das JSON-Dokument geprüft.
- **PHP-Version wie die Anwendung.**
- Codestil wie überall: `_code.md`.

---

## 7. Formulierung der Prüfungen

- **Statuscodes werden exakt geprüft.** Die Zuordnung steht in `_integration.md` §3.4 und ist
  Teil des Vertrags — nicht dem Ermessen des Testautors überlassen.
- **Das Fehlerformat wird als Ganzes geprüft**, nicht nur der Statuscode: Aufbau, stabiler
  Fehlercode, Feldzuordnung (`_integration.md` §3.3). Ein Fehlercode ist eine Zusage nach
  außen; ändert er sich unbemerkt, brechen Kundensysteme.
- **Persistenz wird über die API rückgeprüft**, nie über die Datenbank.
- **Idempotenz braucht den zweiten Aufruf.** Ein Test, der nur einmal sendet, prüft sie nicht.
  Geprüft wird: gleicher Schlüssel → gleiche Antwort, kein zweiter Vorgang.
- **Abwärtskompatibilität wird gegen die veröffentlichte Beschreibung geprüft**, nicht gegen
  die Erinnerung des Testautors. Die OpenAPI-Beschreibung wird aus dem Code erzeugt
  (`_integration.md` §3.1); der Vertragstest vergleicht sie mit dem festgehaltenen Stand der
  Hauptversion und meldet jede Änderung aus der Verbotsspalte in §2.
- **Listen werden auf ihre Obergrenze geprüft**, nicht nur auf ihren Inhalt
  (`_integration.md` §3.5) — eine Liste ohne Obergrenze ist ein Architekturverstoß.
- Ein Test prüft genau **ein** Kriterium.

---

## 8. Ausgehende Wirkungen

Ereignisse, Webhooks und Übergaben nach außen werden über ihre **beobachtbare Wirkung**
geprüft, nicht über Interna der Outbox:

- Ein Testempfänger nimmt Webhooks entgegen; geprüft werden Zustellung, Inhalt und die
  Wiederholung nach Fehlschlag (`_integration.md` §7.2, §7.3).
- **Nie über eine feste Wartezeit.** Auf die eingegangene Zustellung wird gewartet, mit
  Zeitgrenze und aussagekräftigem Fehlschlag.
- Dass eine Wirkung protokolliert wurde, ist über die lesende Schnittstelle nachzuweisen —
  `_requirements.md` fordert diese Nachvollziehbarkeit ohnehin für jedes Epic mit Wirkung nach
  außen.

---

## 9. Ausführung

- Standardlauf über die Pest-Kommandozeile; `apiFixture.php` startet und beendet den
  Testverbund selbst. Manuell gestartete Dienste sind nicht nötig.
- **Parallelisierung ist Pflicht** (`_test-strategy.md` §9). Die Abschaltung der
  Testparallelität ist verboten — die Isolation liefert §5.
- **Keine automatische Wiederholung fehlgeschlagener Tests.** Ein flatterhafter Test wird
  repariert oder entfernt.
- Laufen vor jedem Zusammenführen, gemeinsam mit den Modul-Integrationstests, im Budget aus
  `_test-strategy.md` §9.

---

## 10. Pflege

- Ändert sich eine Story oder `_integration.md`, werden die zugehörigen Tests **in derselben
  Sitzung** angepasst.
- Neuer Endpunkt oder neuer Fehlercode → Vertragstest dazu, im selben Zug.
- **Entfällt ein Kriterium, entfällt sein Test** (`_test-strategy.md` §11.14).
- Der festgehaltene Stand der OpenAPI-Beschreibung wird nur bewusst fortgeschrieben — eine
  Änderung daran ist die Entscheidung, den Vertrag zu ändern, und innerhalb einer Hauptversion
  nur nach §2 erlaubt.

---

## Verweise

- Übergeordnet, Ebenen und Verbotsliste: `_test-strategy.md`
- Geprüfter Vertrag: `_integration.md`
- Akzeptanzkriterien und Story-Struktur: `_requirements.md`
- Maschinelle Zugänge, Rechte: `_security.md`
- Auslieferungsdaten: `_data.md`
- Health-Endpunkt, Ports, Betriebswerte: `_operations.md`
- Oberflächentests: `_test-ui.md`
