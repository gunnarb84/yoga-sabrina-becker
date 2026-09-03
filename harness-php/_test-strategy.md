# _test-strategy.md — Teststrategie, Domänen-, Komponenten- und Modultests

Dieses Dokument enthält die **Teststrategie für alle Produkte** und die Regeln für
Domänentests, Komponententests und Modul-Integrationstests. Ihm untergeordnet sind
`_test-ui.md` (Oberfläche) und `_test-api.md` (Integrations-API).

Übergeordnet: `_architecture.md`. Anforderungen und Akzeptanzkriterien: `_requirements.md`.

---

## 1. Die zwei tragenden Regeln

**1. Jedes Akzeptanzkriterium hat mindestens einen Test, der es beweist** — auf der Ebene, auf
der das Kriterium tatsächlich entschieden wird. Jeder Test nennt sein Kriterium im
Kopfkommentar.

**2. Jede Story hat mindestens einen durchgängigen Oberflächentest**, der den beschriebenen
Ablauf im echten Browser gegen echte Datenbank nachweist. Die Oberfläche bleibt der Ort, an
dem eine Story als erfüllt gilt.

Dazu die Klarstellung, die den Umfang bestimmt:

> **Mehrere Testfälle je Kriterium sind erlaubt und erwünscht. Testfälle ohne Kriterium sind
> es nicht.**

Ein Kriterium wie „Der Positionspreis ergibt sich aus Preisliste, Rabattstaffel und
Währungskurs" ist ein Satz, aber ein Feld aus Fällen. Diese Fälle werden geprüft — und zwar
dort, wo es billig ist. Was es weiterhin **nicht** gibt, ist ein Test, zu dem kein Kriterium
existiert. Neue Testwünsche beginnen mit einer Spec-Änderung, nicht mit einem Test.

Zwei Ausnahmen: Architektur-Konformitätstests und Plattform-Vertragstests (§6). Sie prüfen
Regeln und Plattform-Zusagen, keine Fachlichkeit, und sind als solche gekennzeichnet.

---

## 2. Ebenen

| Ebene | Prüft | Datenbank | Laufzeit je Test |
|---|---|---|---|
| **Domänentest** | Fachregel, Berechnung, Statusübergang | keine | unter 1 ms |
| **Komponententest** | Verhalten einer Livewire-Komponente | keine | wenige ms |
| **Modul-Integrationstest** | Vorgang von Anfang bis Ende, Persistenz, Nebenläufigkeit | echt, im Container | 10–100 ms |
| **API-Vertragstest** | Integrations-API (`_test-api.md`) | echt | 10–100 ms |
| **Durchgängiger Oberflächentest** | Ablauf einer Story (`_test-ui.md`) | echt | Sekunden |

**Grundregel: Ein Kriterium wird auf der billigsten Ebene geprüft, die es tatsächlich
entscheidet.** Nicht so hoch wie möglich, nicht überall.

### Zuordnung

| Art des Kriteriums | Ebene |
|---|---|
| Berechnung, Fachregel, Statusübergang, Prüfung | Domänentest |
| Feld sichtbar oder deaktiviert, Fehlermeldung erscheint, Spalten, Sortierhinweis, Dialog öffnet sich | Komponententest |
| Persistenz, Nummernkreis, gleichzeitiger Zugriff, Berechtigung wirkt | Modul-Integrationstest |
| Verhalten der Integrations-API, Fehlercodes, Idempotenz, Auftragseingang | API-Vertragstest |
| Ablauf der Story: anlegen → speichern → in der Liste sichtbar; Navigation | Oberflächentest |

---

## 3. Ablage

Je Modul ein Testverzeichnis mit bis zu drei Unterbereichen — ein Bereich entsteht erst mit
seinem ersten Test, ein leeres Pflichtverzeichnis je Modul gibt es nicht:

```
modules/{modul}/tests/domain/         ← Domänentests, keine Infrastruktur
modules/{modul}/tests/feature/        ← Modul-Integrationstests, Container-Datenbank (§5)
modules/{modul}/tests/ui/             ← Komponententests (§4.1), erst ab dem ersten Komponentenkriterium
```

Der Testrahmen ist **Pest** (auf PHPUnit-Basis). Dateiname spiegelt den Spec-Pfad, wie in
`_test-api.md` und `_test-ui.md`: `F{n}_S{n}_{StoryPascalCase}Test.php`. Jede Datei beginnt
mit einem Kopfkommentar, der die Story nennt. Testbeschreibungen formulieren **ein**
prüfbares Kriterium, möglichst nah am Wortlaut — deutschsprachig erlaubt.

---

## 4. Domänen- und Komponententests

- **Ohne jede Infrastruktur.** Keine Datenbank, kein Webserver, keine Dateien, kein Netz.
- **Zeit und Zufall werden hineingereicht oder eingefroren**, nie direkt gelesen: Zeit über
  die testbare Uhr des Frameworks (`Carbon::setTestNow`), Zufall als hineingereichter Wert.
  Ein Test, der von der Systemuhr abhängt, wird irgendwann grundlos rot — meist am
  Monatsersten oder zur Zeitumstellung.
- Ein Test prüft **eine** Aussage.
- **Tabellengetriebene Tests** für Wertebereiche und Fallunterscheidungen. Das ist der Weg,
  viele Fälle zu einem Kriterium zu prüfen, ohne den Testcode zu vervielfachen.
- Attrappen sind hier kaum nötig: Die Domain-Schicht hat definitionsgemäß keine
  Abhängigkeiten (`_architecture.md` §4). Braucht ein Domänentest eine Attrappe, ist meist
  die Schichtung verletzt.

### 4.1 Komponententests

Für Kriterien, die das Verhalten einer Livewire-Komponente beschreiben — Feld sichtbar oder
deaktiviert, Meldung erscheint, Dialog öffnet sich (§2). Alle Regeln aus §4 gelten auch hier;
dazu kommt:

- **Werkzeug sind die Livewire-Komponententests** (`Livewire::test(…)` in Pest). Die
  Komponente wird ausgeführt und ihr Ergebnis geprüft, ohne Browser und ohne laufende
  Anwendung — deshalb Millisekunden statt der Sekunden eines Oberflächentests.
- **Ablage in `modules/{modul}/tests/ui/`** (§3). Es entsteht erst mit dem ersten
  Komponentenkriterium; ein leeres Pflichtverzeichnis je Modul gibt es nicht.
- **Vorgänge und Abfragen werden durch Attrappen ersetzt.** Geprüft wird die Komponente,
  nicht die Fachlichkeit dahinter — die hat ihren Test auf der zuständigen Ebene (§2).
- Namensgebung und Kriterienbindung wie überall (§3): eine Testdatei je Story, jeder Test
  nennt sein Kriterium.

---

## 5. Modul-Integrationstests

### 5.1 Echte Datenbank

- Gegen eine **echte Datenbank-Instanz im Container** — dasselbe System (MySQL oder
  MariaDB) in derselben Hauptversion wie in Produktion.
- **Kein Ersatz durch eine andere oder speicherinterne Datenbank.** Genau die Dinge, die hier
  geprüft werden — Nebenläufigkeit über die Zeilenversion, Sortierfolge,
  eindeutige Indizes, Migrationen — verhalten sich dort anders oder gar nicht. Ein grüner
  Test gegen einen Ersatz beweist nichts.
- **Die Migrationen laufen im Testaufbau.** Damit sind sie mitgeprüft und nicht erst beim
  Kunden.

### 5.2 Isolation und Parallelität

- **Standard ist die Transaktionsrückrollung:** Jeder Test läuft in einer Transaktion, die
  am Ende verworfen wird (Datenbank-Transaktions-Trait des Testrahmens). Wo DDL beteiligt
  ist — MySQL/MariaDB führen Schemaänderungen implizit fest aus und brechen damit die
  Hüll-Transaktion —, bekommt der Testlauf eine **eigene Test-Datenbank** und setzt sie über
  die Migrationen neu auf.
- **Wo ein Vorgang selbst festschreiben muss**, bekommt der Testlauf ebenfalls eine eigene
  Datenbank.
- Damit ist die Suite **parallelisierbar** (Parallele Ausführung je Prozess mit eigener
  Test-Datenbank). Der Ein-Worker-Betrieb mit globalem Zurücksetzen vor jedem Test
  entfällt — er skaliert nicht auf tausende Tests.
- Jeder Test bleibt einzeln, wiederholt und in beliebiger Reihenfolge grün.

### 5.3 Pflichttests

- **Je Vorgang mit Konfliktpotenzial ein Nebenläufigkeitstest.**
- **Je Vorgang ein Test, dass er ohne das erforderliche Recht scheitert** (`_security.md` §2.2).

---

## 6. Architektur-Konformitätstests

Für Zusagen, zu denen es nie ein Akzeptanzkriterium geben wird:

- Schichtgrenzen und Modulgrenzen werden eingehalten,
- keine Liste ohne Obergrenze,
- Pflichtspalten sind vorhanden,
- zu jedem Vorgang existiert ein Recht,
- kein fester Anwendertext in der Oberfläche,
- **zu jedem modulübergreifenden Verweis existiert eine angemeldete Auskunftsstelle** des
  Verwendungsnachweises (`_architecture.md` §5.2 Nr. 4, §6). Der Test sammelt die Kennungsfelder,
  die auf ein fremdes Modul zeigen, und vergleicht sie mit dem, was beim Start angemeldet wurde.
  Erst dieser Test macht den letzten Satz von `_architecture.md` §5.3 durchsetzbar („ohne diese
  Prüfung ist der Verweis nicht zulässig") — dass eine Prüfung **fehlt**, fällt sonst niemandem
  auf,
- **die Belegmechanik folgt dem verbindlichen Muster** (`_data.md` §7.3,
  `_architecture.md` §2.3): Statusübergänge nur entlang des Statusmodells, keine Änderung an
  gebuchten Belegen außerhalb der Positivliste, jeder Storno verkettet, Nummernvergabe erst
  beim ersten Statusübergang. Diese Tests existieren **je Produkt**, weil die Belegmechanik
  je Produkt entsteht — sie sind der Mechanismus, der Divergenz zwischen den Produkten
  aufdeckt.

Diese Tests prüfen **Regeln, nicht Fachlichkeit**. Sie liegen in einem eigenen Testprojekt und
sind dadurch klar von den kriteriengebundenen Tests getrennt.

**Was die statische Analyse bereits abdeckt** (`harness/deptrac.yaml`, siehe `_design.md`),
wird hier **nicht** zusätzlich geprüft. Doppelte Prüfung derselben Regel bedeutet doppelte
Pflege.

### 6.1 Plattform-Vertragstests

Plattformbausteine (`_architecture.md` §2.2) haben keine Stories und keine
Akzeptanzkriterien — ihr Vertrag steht im Harness selbst. Ihre Tests sind deshalb
**Vertragstests**, analog zu denen der Integrations-API (`_test-api.md` §2):

- **Quelle ist der Harness-Abschnitt, der die Zusage macht** — etwa Nummernkreise
  `_data.md` §7.4, Outbox `_architecture.md` §8. Jede
  Testdatei nennt ihn im Kopfkommentar.
- **Ablage je Baustein** im eigenen Testverzeichnis `platform/{baustein}/tests/`.
- **Die Ebenenregeln gelten unverändert:** ohne Infrastruktur, was ohne sie entscheidbar
  ist (§4); gegen die Container-Datenbank, wo der Baustein persistiert oder Nebenläufigkeit
  zusichert (§5).
- **Pflicht mindestens dort, wo der Vertrag Nebenläufigkeit oder Zuverlässigkeit zusichert:**
  Nummernvergabe unter gleichzeitigen Zugriffen, Outbox-Zustellung mit Wiederholung.
- **Ein Baustein ist erst mit seinen Vertragstests fertig.** Er entsteht ab seinem Auslöser
  (`_architecture.md` §2.5) und gilt ab dann vollständig — Tests eingeschlossen.

---

## 7. Testdaten

- **Erzeuger mit sinnvollen Vorbelegungen.** Ein Test setzt nur, was für sein Kriterium
  wichtig ist — dadurch ist am Testcode ablesbar, worauf es ankommt.
- **Kein gespiegelter Seed-Datensatz mit Konstanten.** Das bisherige Muster, bei dem
  Erwartungswerte des Backend-Seeds in einer Testdatei nachgeführt werden, entfällt: es
  koppelt alle Tests an einen gemeinsamen Datenbestand und bricht bei jeder Seed-Änderung
  flächig.
- **Auslieferungsdaten** (Einheiten, Währungen, Steuerschlüssel) kommen aus der Migration,
  nicht aus Testcode (`_data.md` §13).
- Kein Test setzt einen Zustand voraus, den ein anderer hinterlassen hat.

---

## 8. Prüfung nicht bestimmbarer Ergebnisse

Für KI-Anwendungsfälle (`_ai.md` §9):

- Je Anwendungsfall eine **Bewertungsmenge** aus Testfällen mit erwarteten Ergebnissen.
- Geprüft werden **Eigenschaften** des Ergebnisses: Format, Wertebereich, Pflichtangaben,
  Abwesenheit erfundener Kennungen, Einhaltung der Schutzklassen. **Niemals
  Zeichengleichheit** — dieselbe Frage darf morgen anders formuliert beantwortet werden.
- Diese Läufe gehören **nicht in die normale Suite**: sie kosten Geld, dauern und hängen von
  einem externen Dienst ab. Sie laufen vor jeder Freigabe sowie bei jeder Änderung an Modell,
  Anbieter oder Anweisung.
- **In der normalen Suite wird der Modellzugriff durch eine feste Antwort ersetzt.** Getestet
  wird dann der umgebende Vorgang, nicht das Modell.

---

## 9. Ausführung und Prüfschritte

| Zeitpunkt | Was läuft | Budget |
|---|---|---|
| Bei jeder Änderung | Domänen- und Komponententests | unter 2 Minuten |
| Vor dem Zusammenführen | zusätzlich Modul-Integrations- und API-Vertragstests | unter 10 Minuten |
| Nächtlich und vor jeder Freigabe | vollständige Oberflächensuite | — |
| Vor jeder Freigabe | Bewertungsmengen der KI-Anwendungsfälle | — |

- **Parallelisierung ist Pflicht.**
- **Flatterhafte Tests werden repariert oder entfernt — nie wiederholt, bis sie grün sind.**
  Eine automatische Wiederholung fehlgeschlagener Tests ist verboten: Sie verwandelt einen
  echten, seltenen Fehler in ein unsichtbares Problem.
- Wird ein Budget überschritten, wird die Suite umgeschichtet — Kriterien wandern auf eine
  billigere Ebene — nicht die Ausführung ausgedünnt.

---

## 10. Pflege

- Ändert sich eine Story, werden ihre Tests **in derselben Sitzung** angepasst.
- Der Testname bleibt am Wortlaut des Kriteriums.
- **Entfällt ein Kriterium, entfällt sein Test.** Ein Test ohne Kriterium ist ein Regelverstoß,
  auch wenn er grün ist.
- Kriterien, die auf keiner Ebene sinnvoll prüfbar sind, werden am Ende der zugehörigen
  Testdatei mit Begründung aufgeführt. Stillschweigen gilt als Lücke.

---

## 11. Verbotsliste

1. Test ohne zugehöriges Akzeptanzkriterium (außer Architektur-Konformitäts- und
   Plattform-Vertragstests nach §6).
2. Akzeptanzkriterium ohne Test.
3. Story ohne durchgängigen Oberflächentest.
4. Kriterium auf einer teureren Ebene geprüft als nötig — etwa eine Preisberechnung im
   Browser statt im Domänentest.
5. Modul-Integrationstest gegen eine andere oder speicherinterne Datenbank statt gegen die
   Produktionsdatenbank (MySQL/MariaDB, gleiche Hauptversion).
6. Testsuite im Ein-Worker-Betrieb mit globalem Zurücksetzen vor jedem Test.
7. Test, der von Systemuhr, Zufall oder Ausführungsreihenfolge abhängt.
8. Gespiegelte Seed-Konstanten als Grundlage von Erwartungswerten.
9. Test, der absolute Aussagen über den Gesamtbestand einer geteilten Test-Datenbank macht
   (`_test-api.md` §5, `_test-ui.md` §5).
10. Vorgang ohne Test des fehlenden Rechts.
11. Automatische Wiederholung fehlgeschlagener Tests.
12. Zeichengleicher Vergleich bei einem Modellergebnis.
13. Echter Modellaufruf in der normalen Testsuite.
14. Test, der nach Entfall seines Kriteriums stehen bleibt.

---

## Verweise

- Übergeordnet, Schichten und Modulgrenzen: `_architecture.md`
- Akzeptanzkriterien und INVEST: `_requirements.md`
- Oberflächentests: `_test-ui.md` · API-Vertragstests: `_test-api.md`
- Auslieferungsdaten: `_data.md`
- Rechteprüfung je Vorgang: `_security.md`
- Bewertungsmengen und Schutzklassen: `_ai.md`
- Statische Prüfung, Abgrenzung zu §6: `_design.md`
