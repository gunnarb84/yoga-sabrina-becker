# Harness — Einstieg und Regelindex

Hier findest du alle Richtlinien, die du einhalten MUSST. Lies die jeweils zuständige Datei,
**bevor** du in dem betroffenen Bereich arbeitest.

## Geltungsbereich

Dieser Harness gilt für Anwendungen dieser Bauart:

- **PHP mit Laravel** als Backend, modularer Monolith, geschichtet — Module unter
  `modules/{Modul}/{Domain,Application,Persistence,UI}` im Repo-Wurzel (`_design.md` §2),
- **Livewire** (mit Blade und Alpine.js) als serverseitig gerenderte, interaktive Oberfläche,
  **MySQL 8 oder MariaDB 10.6+** als Datenbank (Kompatibilität zu beiden, `_data.md` §2),
- vorgangsgetriebene Fachlichkeit (ERP, Warenwirtschaft, Logistik, Disposition),
- **je Firma eine eigene Installation** — komplett getrennt: eigene Anwendung, eigene
  Datenbank, eigener Benutzerstamm. Hat ein Kunde mehrere Firmen, laufen mehrere
  Installationen; Abgleiche zwischen ihnen laufen über die Integrations-API und werden je
  Fall als Spec definiert (`_data.md` §5). Betrieben beim Kunden im Haus (Container-/VM),
  von uns als Dienst oder auf klassischem Webhosting (`_operations.md` §4); nie mehrere
  Firmen oder Kunden in einer Installation.

Passt ein Projekt nicht dazu (andere Sprache, anderes Framework, anderes Oberflächenmodell,
anderer Datenbanktyp), dann gilt dieser Harness **nicht** — er wird dann nicht verwässert,
sondern es entsteht ein eigener. Die Bauart des Projekts steht im Profil unter
`[projekt] bauart` (`php-laravel-livewire-mysql`).

## Verteilung und Unveränderlichkeit

Der Harness ist ein **eigenes Repository** und wird in jedes Projekt unter `/harness`
eingebunden (Subtree oder Submodul).

- **Im Projekt ist `/harness` unveränderlich.** Regeländerungen entstehen im Harness-
  Repository und werden von dort übernommen — nie umgekehrt.
- **Der Harness enthält keine Projektwerte.** Keine Projektnamen, keine Pfade, keine
  Herstellernamen, keine Fachbegriffe eines Produkts. Geprüft über
  `python harness/analyse_harness.py`; ein Fund ist ein Fehler.
- Wer eine Harness-Datei ändern müsste, um ein Projekt abzubilden, hat einen Wert am
  falschen Ort — er gehört ins Profil.

## Projektprofil (Pflicht)

Alle projektspezifischen Werte stehen in **`PROJEKT.md`** im Wurzelverzeichnis des Projekts:
Namen, Pfade, Composer-Wurzel, Sprachen, Modulgruppen, Stylesheet-Ziel, Glossar, Stakeholder,
Zahlenwerte. Vorlage: `harness/PROJEKT.template.md`.

Fachliches Wissen des Produkts (Domäne, fachliche Datentypen, Schutzklassen, externe
Systeme, Vorgänge je Modul) liegt in **`projekt/_domaene.md`**.

Fehlt ein benötigter Wert, brechen die Analyse-Skripte mit einer Meldung ab, statt eine
Prüfung durchzuführen, die nichts prüft.

## Geschützte Dateien — Änderungen NUR nach Rückfrage

Die folgenden Dateien/Bereiche dürfen **nicht ohne ausdrückliche Freigabe** des Nutzers
geändert werden. Bevor du sie anlegst, bearbeitest, umbenennst oder löschst, musst du
**zuerst nachfragen** und die konkrete Änderung beschreiben; erst nach expliziter Zustimmung
(„ja"/Freigabe) führst du sie aus. Eine allgemeine Aufgabe wie „bau den Code um" ist
**keine** Freigabe für diese Dateien.

- **Alles unter `/harness/`** (Leitfäden, `_index.md`, `reference.css`, Regelvorlagen
  `phpstan.neon`/`pint.json`/`deptrac.yaml`, `analyse_*.py`, `PROJEKT.template.md`).
- **Die Einstiegsdateien des Projekts** (`AGENTS.md`, `CLAUDE.md`).
- **Das Projektprofil** (`PROJEKT.md`) — es steuert, was geprüft wird.

Reine **Lesezugriffe** auf diese Dateien sind jederzeit erlaubt und erwünscht. Die
Rückfragepflicht gilt ausschließlich für **schreibende** Änderungen.

## Bereiche und ihre Zuständigkeit

Welcher Bereich wo liegt, ergibt sich aus dem Profil; welche Datei ihn regelt, steht hier.
Der vollständige, kommentierte Verzeichnisbaum wird in der Entwicklerdoku gepflegt
(`{dokuDev}/referenz/projektstruktur.md`), **nicht** hier duplizieren.

| Bereich | Ort (Profilschlüssel) | Zuständige Harness-Datei(en) |
|---|---|---|
| Ideen | `[pfade] ideen` | `_ideas.md` |
| Anforderungen / Specs | `[pfade] specs` (`E{n}/F{n}/S{n}`, `guidelines/`) | `_requirements.md` |
| Oberfläche | `[pfade] frontend` | `_uiux.md`, `_uiux-abnahme.md`, `reference.css` |
| Backend (PHP) | `[pfade] backend` | `_architecture.md`, `_design.md`, `_code.md` + Regelvorlagen |
| API-Tests | `[pfade] apitests` | `_test-api.md` |
| Oberflächentests | `[pfade] uitests` | `_test-ui.md` |
| Statische Prüfung | `harness/analyse_*.py` → Ergebnisse in `[pfade] analyse` | `analyse_all.py`, Regelvorlagen |
| Endkunden-Doku | `[pfade] dokuUser` | `_documentation.md` |
| Entwickler-Doku | `[pfade] dokuDev` | `_documentation.md` |
| Domänenwissen | `projekt/_domaene.md` | — (projektspezifisch) |
| Harness (Prozess/Regeln) | `/harness` | — (geschützt, zentral gepflegt) |

## Anforderungen & Ideen

- **`_ideas.md`** — Ideen-Entwicklungsprozess: Wie aus einer groben Feature-Idee im
  kritischen Dialog eine dokumentierte, „ready" Idee wird. IMMER lesen, wenn eine **neue
  Idee/ein neues Feature** besprochen wird — **bevor** Specs oder Code entstehen.
  Neue Ideen-Dateien entstehen **nur** über `python harness/idee_neu.py "Name"` — das
  Skript reserviert die laufende Nummer zentral (sauberer, aktueller Stand vorausgesetzt;
  Commit und sofortiger Push), damit sich Nummern bei parallelem Arbeiten nicht doppeln.
- **`_requirements.md`** — Anforderungs-Konventionen: Ordner-/Story-Struktur
  (`E{n}/F{n}/S{n}`), Meta-/State-Block (`Modified`/`Implemented`), INVEST-Regeln für
  Akzeptanzkriterien, Querschnitts-Features, Abgrenzung Oberfläche/Backend. IMMER lesen,
  wenn du an **Anforderungen/Specs** arbeitest.

## UI / Design

- **`_uiux.md`** — Design-Leitfaden: Dichte statt Weißraum, flaches, warm-neutrales
  Admin-Design mit genau einem Akzentton, Split-View aus Liste und Maske, durchgängige
  Tastaturbedienung mit fester Funktionstastenbelegung (§5), Besuchsverlauf für
  Browser-Zurück ohne URLs (§4a), zwei Fenster derselben Anmeldung für zwei Arbeitsgänge
  nebeneinander (§3a), virtualisierte Raster,
  Typografie, Spacing, Komponenten, Ton/Sprache (Anrede „Sie"). IMMER lesen, wenn du an der
  **Oberfläche** arbeitest.
- **`_uiux-abnahme.md`** — Modulabnahme-Prüfliste: verdichtete Checkliste zu `_uiux.md`
  (Shell, Liste, Maske, Belege, Tastatur, Qualitätsschranken, Code), jeder Punkt mit
  Verweis auf den verbindlichen Abschnitt. Vor der **Abnahme eines Moduls** Punkt für
  Punkt durchgehen; ein „nein" ist ein Fehler im Modul.
- **`reference.css`** — Zentrale Design-Vorlage: Design-Tokens (`--au-*`) und Basisklassen
  mit Prefix `au-` (`.au-btn`, `.au-panel`, `.au-table`, `.au-field`, `.au-status`,
  `.au-key`, …). Unverändert unter den Zielnamen aus `[ui] stylesheet` kopieren; Farben und
  Schriften des Projekts ausschließlich in `[ui] theme` überschreiben, nie hart kodieren.

## Code & Backend-Design

- **`_architecture.md`** — Architekturvertrag (führend, steht über `_design.md`):
  Produktlandschaft (Plattform + Produkte, Ausbaustufen), Rollenbegriffe, Laufzeit-Topologie
  (stateless, beide Betriebsformen), Schichten je Modul, Modulschnitt und -grenzen,
  Querschnittsbelange, Vorgänge statt CRUD, Transaktions- und Konsistenzgrenzen,
  Leistungsbudgets, ADR-Pflicht, Verbotsliste. IMMER lesen, **bevor** du an Backend, Modulen
  oder Oberfläche arbeitest.
- **`_data.md`** — Datenmodell- und Datenbankvertrag (MySQL 8 / MariaDB 10.6+):
  Modul-Tabellenpräfixe und Namensgebung, UUID-Schlüsselstrategie, Pflichtspalten, eine
  Firma je Installation, fachliche Datentypen
  (Geld, Menge, Gewicht, Prozent), Belegmodell und Unveränderbarkeit, Kontierung,
  Historisierung und Löschung, Indizes, Migrationen, Datenübernahme. IMMER lesen, wenn du
  **Entitäten, Migrationen oder Abfragen** anfasst.
- **`_ai.md`** — KI-Funktionen und Assistenten: Anbieterabstraktion je Anwendungsfall (Dienst
  oder lokal), Ort der Aufrufe im Code, Werkzeuge über die Integrations-API, menschliche
  Freigabe schreibender Aktionen, Schutzklassen und Datenfreigabe, Verwaltung der Anweisungen,
  Trennung von Anweisung und Daten, Protokollierung und Kosten, Bewertungsmengen, Abgrenzung
  zum Automatisierungswerkzeug. IMMER lesen, wenn du an **KI-Funktionen oder Assistenten**
  arbeitest.
- **`_integration.md`** — Externe Schnittstellen: Versionierung und Abwärtskompatibilität der
  Integrations-API, Fehler- und Listenformat, Idempotenz, Vorgangseingang mit Klärfällen,
  Abbildung externer Kennungen, Ereignisse und Outbox, Webhooks, Automatisierungswerkzeug,
  Buchhaltungsübergabe, Offline-Abgleich des Feld-Clients. IMMER lesen, wenn du an
  **API-Endpunkten, Ereignissen, externen Abläufen oder dem Feld-Client** arbeitest.
- **`_security.md`** — Anmeldung, Rechte, Nachweisbarkeit: zwei gleichwertige Anmeldewege —
  OIDC gegen den Identity-Provider des Kunden und eine vollständig eigene lokale
  Benutzerverwaltung mit zweitem Faktor (TOTP) —, Notfallzugang, Sitzungsverhalten
  einschließlich mehrerer Sitzungen je Person ohne Browserbindung (§1.4),
  Lizenzgrenze über aktive Konten (§1.5), Rollen-
  und Rechtemodell (Rechte ausschließlich über Rollen, nie je Person) mit Durchsetzung in
  der Application-Schicht, Vier-Augen-Prinzip,
  maschinelle Zugänge, Umfang des Audit-Protokolls, Datenschutz gegen
  Aufbewahrungspflicht, Feld-Client, Geheimnisse. IMMER lesen, wenn du an **Anmeldung,
  Rechten, Audit oder maschinellen Zugängen** arbeitest.
- **`_operations.md`** — Betrieb und Beobachtbarkeit: Protokollierung mit Korrelations-ID,
  Health und Kennzahlen, zentrale Fehlerbehandlung, Erstinstallation beim Kunden oder auf
  klassischem Webhosting (zwei gleichwertige Betriebsformen, §4), Aktualisierung im
  laufenden Betrieb (Ablauf, Rückkehr zur Vorversion, Schalter), Sicherung
  und Wiederanlauf, Lasttest. IMMER lesen, wenn du an **Protokollierung, Konfiguration,
  Auslieferung oder Betriebsverhalten** arbeitest.
- **`_design.md`** — Umsetzungsmuster je Schicht (untergeordnet `_architecture.md`): Aufbau
  eines Moduls aus vier Namensräumen unter `modules/{Modul}/`, Plattformbausteine,
  Gliederung nach fachlichem Gegenstand,
  Eloquent-Modelle mit fachlichem Verhalten, Wertobjekte, ein Vorgang je Klasse mit
  `execute()`/`Request`/`Response` und fester Ablaufreihenfolge, Ergebnis mit stabilem
  Fehlercode, Abfragen mit Projektion und Obergrenze, Migrationen je Modul, dünne
  Livewire-Komponenten und Controller, Checklisten für neue Entität und neuen Vorgang,
  Namenskonventionen. IMMER lesen, wenn du am **Backend** (Modul, Entität, Vorgang, Abfrage,
  Migration, Endpunkt) arbeitest.
- **`_code.md`** — PHP-Codestil (PHPStan-/Pint-Voreinstellungen): englische Bezeichner nach
  Glossar, Benennung, Datei- und Typorganisation, `declare(strict_types=1)`, Formatierung,
  Sprachgebrauch mit **Konstruktor-Property-Promotion** und `readonly`, Nullbarkeit,
  Warteschlangen statt Asynchronität, Fehlerbehandlung, Blade-/Livewire-Konventionen,
  begründete Ausnahmen. Ziel sind null PHPStan-Hinweise und ein sauberer Pint-Lauf.
  Verifikation via `python harness/analyse_code.py`.
  IMMER lesen, wenn du **PHP- oder Blade-Code** schreibst oder änderst.

### Regeldateien sind führend

Für Code/Design sind die maschinenlesbaren **Regelvorlagen die einzige verbindliche Quelle**
(führend), nicht die `.md`-Leitfäden:

- **`harness/phpstan.neon`** und **`harness/pint.json`** — Regeln für Code und Stil
  (PHPStan/Pint), geprüft über `python harness/analyse_code.py`.
- **`harness/deptrac.yaml`** — Schichten- und Modulgrenzen (Deptrac), geprüft über
  `python harness/analyse_design.py`; im Projekt mit den Platzhaltern für
  Namensraum-Präfix und Modulnamen ausgefüllt.

Bei einem Verstoß gilt das, was die Regelvorlage tatsächlich prüft — nicht die Beschreibung im
zugehörigen `.md`. Weichen `.md` und Regelvorlage voneinander ab, hat die **Regelvorlage
Vorrang**. Ändert sich eine Regelvorlage, sind die zugehörigen `.md`-Dateien (`_code.md` bzw.
`_design.md`) entsprechend nachzuziehen — aber **nur nach Rückfrage** (siehe „Geschützte
Dateien").

## Dokumentation

- **`_documentation.md`** — Leitfaden für beide Dokumentationen. Gemeinsame Mechanik:
  Diátaxis-Dokumenttypen, Ablage und Benennung, Meta-Block, verbindliche Verlinkung, Pflege
  synchron zu Specs und Code. Dazu die Eigenheiten der **Endkunden-Doku** (Zielgruppe
  Anwender, UI-wortgleiche Benennung, Schreibstil, Screenshots, `[pfade] dokuUser`) und der
  **Entwicklerdoku** (Setup/How-To/Referenz/ADR, Mermaid-Diagramme, Mindestumfang,
  `[pfade] dokuDev`). IMMER lesen, wenn du an **Dokumentation** arbeitest — gleich welcher.

## Tests

- **`_test-strategy.md`** — Teststrategie (führend für alle Testarten) sowie Domänen-,
  Komponenten- (Livewire) und Modul-Integrationstests: die zwei tragenden Regeln (jedes
  Kriterium hat einen Test auf der Ebene, auf der es entschieden wird; jede Story hat einen
  durchgängigen Oberflächentest), Zuordnung der Kriterienarten zu Testebenen,
  Container-Datenbank statt Ersatzdatenbank, Isolation über Transaktionsrückrollung statt
  Ein-Worker-Betrieb, Architektur-Konformitäts- und Plattform-Vertragstests, Testdaten über
  Erzeuger, Prüfung nicht bestimmbarer KI-Ergebnisse, Laufzeitbudgets. IMMER **zuerst**
  lesen, wenn Tests jeder Art erstellt/geändert werden.
- **`_test-ui.md`** — Oberflächentests (Playwright): Testfälle ausschließlich aus
  Specs/Akzeptanzkriterien, einheitliche Struktur, eigene Test-Datenbank mit Seed, jeder Test
  unabhängig, headless. IMMER lesen, wenn **Oberflächentests** erstellt/geändert werden.
- **`_test-api.md`** — API-Tests (PHP/Pest): Integrationstests gegen ein laufendes Backend
  mit Test-Datenbank, Testfälle ausschließlich aus Specs/Akzeptanzkriterien
  (Backend-autoritative Kriterien). IMMER lesen, wenn **API-Tests** erstellt/geändert werden.
- **`analyse_all.py`** — Statische Richtlinien-Prüfung (Code + Design): ruft
  `analyse_code.py` (PHPStan/Pint / `_code.md`) und `analyse_design.py` (Deptrac /
  `_design.md`) auf
  und schreibt die Verstöße nach `[pfade] analyse` (`code.json`, `design.json`, `all.json`).
  IMMER ausführen, wenn **PHP-/Backend-Code** geändert wurde, und gemeldete Verstöße beheben;
  die Regelvorlagen sind führend.
- **`analyse_harness.py`** — Prüft, ob der Harness projektneutral geblieben ist. Ausführen,
  wenn eine Harness-Datei geändert wurde.

---

# Dev Workflow

Der Standard-Ablauf für jede Änderung. Die zuständige Harness-Datei (oben) wird immer
**vor** der Arbeit im jeweiligen Bereich gelesen.

1. **Verstehen & vorbereiten** — Aufgabe klären. Ist es eine **neue Idee/ein neues Feature**,
   zuerst den Ideen-Prozess aus `_ideas.md` durchlaufen (kein Code, keine Specs vorher) und
   die Idee dokumentieren, bis sie `Ready` ist.
2. **Spezifizieren** — Anforderungen nach `_requirements.md` als Epics/Features/Stories
   anlegen oder anpassen. Meta-`State` pflegen (`Modified`, sobald eine Spec vom Code
   abweicht).
3. **Umsetzen** — Code schreiben. Bei Arbeit an der Oberfläche `_uiux.md` befolgen und die
   Tokens/Basisklassen aus `reference.css` verwenden. Bei Backend-Arbeit den Architekturvertrag
   `_architecture.md`, die Umsetzungsmuster `_design.md` und die Coding-Guideline `_code.md`
   befolgen; Fachlogik und Prüfung liegen in Application/Domain, nie in der UI-Schicht.
4. **Testen** — Tests ausschließlich aus den Akzeptanzkriterien ableiten, auf der Ebene, auf
   der das Kriterium entschieden wird (`_test-strategy.md`): Oberflächentests nach
   `_test-ui.md`, API-Tests nach `_test-api.md`. Neue/berührte Kriterien abdecken und die
   Suite grün laufen lassen. Bei PHP-/Backend-Änderungen zusätzlich die **statische
   Richtlinien-Prüfung** ausführen (`python harness/analyse_all.py`) und die gemeldeten
   Verstöße (Code + Design) beheben; die Regelvorlagen sind führend.
5. **Dokumentieren** — nach `_documentation.md`: Ändert sich nutzersichtbares Verhalten, die
   Endkunden-Dokumentation mitziehen (betroffene Anleitungen/Referenzen anpassen, Benennungen
   UI-wortgleich halten, Links/Index pflegen). Ändert sich Technik/Architektur (Endpunkt,
   Migration, Ports/Umgebung, Build/Auslieferung, Architekturentscheidung), zusätzlich die
   Entwicklerdokumentation mitziehen bzw. eine ADR anlegen (ADR-Pflicht:
   `_architecture.md` §11).
6. **Abgleichen** — Wenn Spec und Code (Oberfläche + Backend + Tests) übereinstimmen, den
   Story-`State` auf `Implemented` setzen. Übernommene Ideen auf `Übernommen` setzen und die
   entstandenen Spec-Pfade verlinken.
7. **Git Commit** — abgeschlossene, zusammengehörige Änderung committen (aussagekräftige
   Nachricht; keine Build-/Test-Artefakte einchecken). Gearbeitet wird **Trunk-Based**:
   Commits gehen direkt auf `main`, langlebige Branches gibt es nicht. Voraussetzung für
   jeden Push: Analyse und Testsuite sind grün — `main` ist jederzeit auslieferbar. Eine
   Änderung, die noch nicht wirken darf, kommt **dunkel hinter einem Release-Schalter** in
   den Code (`_operations.md` §5.6); kundenspezifische Zweige sind verboten
   (`_architecture.md` §12).
8. **Bereitstellen** — Anwendung bauen und ausrollen (`_operations.md`); die Änderung in der
   laufenden Anwendung verifizieren.

---

# Sub Agents

JEDER Sub Agent erhält die Einstiegsdatei des Projekts, dieses `_index.md`, das Projektprofil
und alle für seine Aufgabe relevanten Richtlinien des Harness.
