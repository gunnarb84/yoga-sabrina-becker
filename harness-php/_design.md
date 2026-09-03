# _design.md — Umsetzungsmuster

Konkrete Datei-, Klassen- und Ablaufmuster für jede Schicht. Dieses Dokument beantwortet
**wie** etwas geschrieben wird; **wo** es hingehört und **was miteinander reden darf**, regelt
`_architecture.md`.

Zweck dieser Muster: Ein Modul soll aussehen wie jedes andere. Gleichförmigkeit ist hier kein
Selbstzweck, sondern die Voraussetzung dafür, dass ein Agent Code erzeugt, der sich in den
Bestand einfügt, und dass ein Mensch sich in einem fremden Modul sofort zurechtfindet.

Codestil (Benennung, Formatierung, Sprachgebrauch): `_code.md`.

---

## 1. Geltungsbereich und Rangfolge

Untergeordnet: `_architecture.md`. Die Regelvorlage `harness/deptrac.yaml` ist für
Schichten- und Modulgrenzen gegenüber diesem Dokument **führend** — bei Abweichung gilt, was
tatsächlich geprüft wird, und die Abweichung wird gemeldet.

---

## 2. Aufbau eines Moduls

Ein Modul ist ein Verzeichnis unter der Modulwurzel (`[php] modulwurzel`, Vorgabe
`modules/`) mit **vier Namensräumen** — die vier Schichten aus `_architecture.md` §4:

```
modules/lager/
├── domain/          Modules\Lager\Domain        — Eloquent-Modelle mit Verhalten, Wertobjekte, Ereignisse
├── application/     Modules\Lager\Application   — Vorgänge (je Klasse), Abfragen, Result/Request/Response
├── persistence/     Modules\Lager\Persistence   — Migrationen, Casts, Erzeuger/Factories
├── ui/              Modules\Lager\Ui            — Livewire-Komponenten, Blade-Views, View-Mapper
├── api/             Modules\Lager\Api           — Controller, Form Requests, API-Ressourcen (nur bei Integrations-Endpunkten)
└── tests/                                         — Pest-Tests des Moduls (_test-strategy.md §3)
```

- Der PSR-4-Präfix ist `{Kurzname}\Modules\{Modul}\…`, eingetragen in `composer.json` des
  Projekts (`[projekt] kurzname` im Profil). Verzeichnisse sind klein (`domain/…`),
  Namensräume in PascalCase (`Modules\Lager\Domain\…`).
- Ein Modul ohne Integrations-Endpunkte hat **kein** `api/`-Verzeichnis.
- Module nennen ihre fachlichen Gegenstände, nicht ihre Technik — die Gliederung *innerhalb*
  der Schichten folgt dem fachlichen Gegenstand (z. B. `domain/beleg/…`), nicht dem
  Dateityp.
- Der Routen-, Menü- und Rechte-Eintrag des Moduls folgt den Plattform-Konventionen
  (§2.1); jedes Modul registriert sich an derselben Stelle auf dieselbe Weise.

### 2.1 Plattformbausteine

Gemeinsame Fachbausteine ohne eigenes Fachmodul (Nummernkreise, Rechteprüfung, Audit-
Protokoll, Beleglogik, Korrelations-ID, Einstellungen) liegen als **Plattformbausteine**
unter `platform/` (Namensraum `{Kurzname}\Platform\…`). Regeln:

- Ein Baustein hat die **gleichen vier Namensräume** wie ein Modul — er ist kein
  Sammelordner für „utils".
- Bausteine werden über ihre Application-Schnittstellen benutzt (Schnittstelle mit Suffix
  `Interface`, im Container gebunden) — ein Modul greift nie in die Persistenz eines
  Bausteins, und der Baustein kennt kein Modul.
- Je Baustein ein eigener Migrationsstrang mit eigenem Tabellenpräfix (`_data.md` §2).

---

## 3. Domain-Schicht

### 3.1 Entitäten

Entitäten sind **Eloquent-Modelle mit fachlichem Verhalten**. Die bewusste Festlegung der
Bauart: Das Modell ist zugleich Abbildung auf die Tabelle und Träger der Fachlogik — dafür
gelten diese Leitplanken:

- **Jede fachliche Zustandsänderung läuft über eine Verhaltensmethode** mit fachlichem
  Namen (`confirm()`, `storniere()`, `erhoeheBestand(…)`). Direkte Attribut-Zuweisung von
  außen (`$beleg->status = …`) ist außerhalb der Persistenz-Mechanik verboten — Attribute
  entstehen beim Bauen (Erzeugermethode oder Factory) oder durch Verhaltensmethoden.
- Verhaltensmethoden **führen die fachlichen Prüfungen selbst durch** und melden erwartbare
  Fehler als `Result` mit stabilem Fehlercode (§4.3); der Aufrufer prüft nicht vorweg „ob
  das wohl geht".
- Massenzuweisung ist kontrolliert: `$fillable` wird nicht mit wilden Listen gefüllt; der
  Standard ist `$guarded = []` **nur** zusammen mit dem Verbot direkter Zuweisung (oben) —
  nie `Model::create($request->all())`.
- Das Modell enthält **kein HTTP, keine Validierung von Rohdaten, keine View-Belange** und
  ruft keine anderen Module auf — Verknüpfungen über Modulgrenzen laufen über die
  Application-Schicht (§5.2 des Architekturvertrags bzw. §5.4 hier).
- **Zeit und Zufall** nur über die testbaren Framework-Mechanismen (`now()`/Carbon mit
  `setTestNow`), nie über `time()`, `microtime()`, `mt_rand()` direkt.

Pflichtspalten und ihre Abbildung (`id` als UUID, `angelegt_am`, `version` für
optimistische Sperrung) stehen in `_data.md` §4. Im Modell:

- `$incrementing = false`, Schlüssel als UUID v7, anwendungsseitig erzeugt (`_data.md` §3.1);
- `CREATED_AT`/`UPDATED_AT`-Konstanten auf die deutsch benannten Spalten gemappt;
- `version` wird bei jedem Speichern erhöht (eigenes `saving`-Verhalten im Modell) und bei
  jeder Änderungsabfrage mitgeprüft — „gleichzeitig bearbeitet" ist ein erwartbarer
  Fehler, kein stiller Überschreiber.

#### Pflichtspalten bleiben Datenbanksache

Am Modell steht nur `id`; Zeitstempel und `version` werden von der Persistenz-Mechanik
gesetzt und gehören nicht in den fachlichen Konstruktionspfad des Modells.

### 3.2 Wertobjekte

Wertobjekte sind unveränderlich (`readonly`), vergleichen über Wert und tragen fachliche
Regeln ihrer Größe (Geld, Menge, Prozent — `_data.md` §6): Runden, Addieren, Vorzeichen
liegen **am Wertobjekt**, nicht am Aufrufer. Feste Wertelisten sind `enum`s (backed),
keine Konstantensammlungen. Wertobjekte werden über Casts auf ihre Spalten abgebildet
(§5.3).

### 3.3 Fehler in der Domain

Die Domain wirft keine Ausnahmen für erwartbare Fachfälle — sie meldet `Result`
(§4.3, Fehlercode stabil und sprachneutral). Ausnahmen sind Programmierfehler
(Vorbedingung verletzt, Typbruch) und dürfen laut sein.

### 3.4 Domain-Ereignisse

Fachliche Ereignisse („Beleg freigegeben") entstehen in der Domain und werden vom Vorgang
nach erfolgreichem Speichern ausgelöst — modulintern als Laravel-Event, über Modul- und
Installationsgrenzen ausschließlich über die Outbox (`_integration.md`). Ein Ereignis
trägt seine Daten selbst mit; Hörer lesen nicht am Modell nach, was „wohl gemeint war".

---

## 4. Application-Schicht

### 4.1 Ein Vorgang, eine Klasse

Jeder fachliche Vorgang ist **eine eigene Klasse** mit genau einer öffentlichen Methode
`execute()`:

```php
final class ConfirmSalesOrder
{
    public function __construct(private NumberSequenceInterface $numbers) {}

    public function execute(Request $request): Result { … }
}
```

- `Request` und das Datenpaket von `Response`/`Result` sind **readonly-DTOs** im selben
  Verzeichnis, benannt nach dem Vorgang (`ConfirmSalesOrder/Request.php`).
- Abhängigkeiten injiziert (Konstruktor, Promotion), nie `app()` im Rumpf (`_code.md` §5.3).
- Die Klasse ist der **einzige** Aufrufpunkt des Vorgangs: UI-Komponente, API-Controller,
  Job und Artisan-Befehl rufen denselben Vorgang auf — niemals zweite Implementierungen
  derselben Wirkung.

### 4.2 Verbindliche Reihenfolge

Jeder schreibende Vorgang besteht aus denselben Schritten in derselben Reihenfolge:

1. **Rechte** prüfen (Durchsetzung hier, nicht in der UI — `_security.md` §2.2),
2. **Request validieren** (Form/Syntax; fachliche Prüfung folgt in der Domain),
3. **Laden** der betroffenen Entitäten (mit Sperrung, wo §8 des Architekturvertrags sie
   verlangt),
4. **Fachlich handeln** über Verhaltensmethoden (§3.1),
5. **Speichern** in genau einer Transaktion (`DB::transaction()`),
6. **Ereignisse** auslösen / Outbox-Eintrag schreiben (innerhalb derselben Transaktion,
   `_integration.md`).

Kein Vorgang darf Schritt 4 oder 5 auslassen, indem er „direkt in die Tabelle schreibt".

### 4.3 Ergebnis und Fehlercodes

- Rückgabe ist ein `Result`: Erfolg mit Datenpaket oder Fehler mit **stabilem,
  sprachneutralem Fehlercode** (`sales.order_not_found`) und Fehlerliste. Fehlercodes
  sind Vertrag der Integrations-API (`_integration.md` §2) und werden nie umbenannt.
- Mehrere fachliche Fehler werden gesammelt gemeldet, nicht beim ersten abgebrochen —
  die Maske zeigt alle Konflikte auf einmal (`_uiux.md` §7).

### 4.4 Abfragen

Lesefälle sind **eigene Abfrageklassen** (nicht Vorgänge): `…Query` mit einer öffentlichen
Methode, die

- **projiziert** (nur die Spalten des Lesefalls — kein `Model::all()`/volle Modelle für
  Listen; Rückgabe als readonly-Zeilendaten, je Liste getypt),
- eine **Obergrenze** erzwingt (explizites `limit()` oder Cursor-Paginierung — eine Liste
  ohne Obergrenze ist ein Fehler, `_architecture.md` §9),
- Filter/Sortierung als getyptes Kriterien-Objekt entgegennimmt, nicht als Array.

Listen der Oberfläche laufen **nur** über diese Abfragen; die Komponente baut keine
Queries selbst (§6).

---

## 5. Persistence-Schicht

### 5.1 Migrationen je Modul

- Jedes Modul hat **eigenen Migrationsstrang** mit eigenem Tabellenpräfix
  (`{modul}_…`, `_data.md` §2/§11); Migrationen liegen unter
  `modules/{modul}/persistence/migrations/` und werden über die Modul-Registrierung
  eingebunden.
- Migrationen beschreiben das Schema vollständig und widerspruchsfrei zu §3–§10 von
  `_data.md` (Spaltentypen der fachlichen Datentypen, Pflichtspalten, Indizes).
- Ausgelieferte Wertetabellen werden über **Seed-Daten des Moduls** befüllt
  (`_data.md` §13), nie über `insert` in einer Schema-Migration.

### 5.2 Eloquent-Konventionen je Modul

- Modelle liegen in `domain/` und erklären ihre Tabelle ausdrücklich (`protected $table =
  'lager_beleg';`) — keine implizite Namensableitung.
- Modul-interne Relationen sind erlaubt; **Relationen über Modulgrenzen sind verboten**
  (siehe §5.4).
- Zeitstempel-Mapping und `version`-Verhalten wie in §3.1; dafür darf ein Modul **eine**
  eigene Basisklasse haben (`abstract class LagerModel extends Model`) — mehr Basisklassen
  sind verboten (Hierarchie statt Verhalten).

### 5.3 Wertobjekte abbilden

Wertobjekte werden über **Casts** auf ihre Spalten abgebildet (eine Spalte je Wertobjekt-
Anteil; Geld: `DECIMAL(19,4)` nach `_data.md` §6). Eigene Serialisierungsformen (JSON-
Spalten mit fachlichem Inhalt) sind nur für Erweiterungsfelder erlaubt (`_data.md` §6.3).

### 5.4 Modulgrenzen

- Ein Modul greift **nie** auf Modelle, Tabellen oder Migrationen eines anderen Moduls zu —
  fachliche Daten anderer Module kommen über deren Application-Schnitt (Abfrage/Vorgang
  oder Ereignis), geprüft durch Deptrac (`deptrac.yaml`, §1).
- Fremdschlüssel über Modulgrenzen verweisen **fachlich** (Kennung/UUID des anderen
  Moduls), nicht als erzwungener Datenbank-FK (`_architecture.md` §5.3).

---

## 6. UI-Schicht

- Eine **Livewire-Komponente** je Maske/Listenausschnitt; sie bindet Eingaben, ruft
  Vorgänge (§4.1) und Abfragen (§4.4) auf und übergibt vorbereitete View-Daten. Logik-
  Umfang und Verbote: `_code.md` §9.
- Blade-Views rendern nur, was die Komponente liefert — kein Datenbankzugriff, kein
  Vorgangsaufruf im Template.
- Gemeinsame Masken-/Listen-Bausteine (Wertehilfe, Löschdialog, Belegpositionsraster) sind
  Plattform-Komponenten (`_uiux.md` §7a–§7h), nicht je Modul neu erfunden.
- Anwendertexte kommen aus den Sprachdateien; die UI führt keine Fachprüfungen durch und
  speichert nichts selbst.

---

## 7. Integrations-API

- **Dünne Controller**: Authentifizierung und Rechte am Eingang, Form Request für Syntax,
  dann **genau ein** Vorgang (§4.1); die Antwort entsteht aus dem `Result` über eine
  API-Ressource. Controller enthalten keine Fachlogik und greifen nie auf Modelle zu.
- Routing versioniert unter `/api/v{n}` (`_integration.md` §1); Fehlerformat und
  Statuscodes folgen `_integration.md` §2 — der Fehlercode des Vorgangs wird durchgereicht,
  nicht neu erfunden.

---

## 8. Checklisten

### 8.1 Neue Entität

- [ ] Migration mit Pflichtspalten (`id` UUID, `angelegt_am`, `version`) und fachlichen
      Spalten nach `_data.md` §2/§6, Tabellenpräfix des Moduls
- [ ] Modell in `domain/`: `$table` gesetzt, Verhaltensmethoden für alle Zustandsänderungen,
      Casts der Wertobjekte, `version`-Erhöhung
- [ ] Erzeuger/Factory für Tests (`_test-strategy.md` §7)
- [ ] Ausgelieferte Werte (falls Wertetabelle): Seed des Moduls (`_data.md` §13)
- [ ] Domänen-/Integrationstests für die Verhaltensmethoden (`_test-strategy.md`)
- [ ] Glossar-Einträge für neue Begriffe (`PROJEKT.md`)

### 8.2 Neuer Vorgang

- [ ] Klasse in `application/…` mit genau einer öffentlichen `execute()`; `Request` als
      readonly-DTO
- [ ] Reihenfolge §4.2 eingehalten (Rechte → Validierung → Laden → Domain → eine
      Transaktion → Ereignisse/Outbox)
- [ ] `Result` mit stabilen Fehlercodes; Codes in der Doku der Schnittstelle
      (`_integration.md`, falls API-beteiligt)
- [ ] Aufruf aus UI/Controller/Job über diesen Vorgang — keine Parallel-Implementierung
- [ ] Modul-Integrationstest gegen echte Datenbank (`_test-strategy.md` §5)

---

## 9. Namenskonventionen

| Was | Muster | Beispiel |
|---|---|---|
| Vorgang (schreibend) | `{Verb}{Gegenstand}` | `ConfirmSalesOrder` |
| Vorgang-Methoden | `execute()` | — |
| Vorgangs-Daten | `Request` / `Result` im Vorgangsverzeichnis | `ConfirmSalesOrder/Request` |
| Abfrage (lesend) | `{Gegenstand}…Query` | `OffeneBestellungenQuery` |
| Modell | Glossarbegriff, Singular | `SalesOrder` |
| Tabelle | `{modul}_{begriff}` klein | `lager_beleg` (`_data.md` §2) |
| Migration | `JJJJ_MM_TT_hisss_beschreibung` | Framework-Konvention |
| Modul | klein, fachlich | `modules/lager/` |
| Namensraum | `{Kurzname}\Modules\{Modul}\{Schicht}\…` | `Bsp\Modules\Lager\Domain` |
| Ereignis | Vergangenheitsform | `SalesOrderConfirmed` |
| Job | `{Wirkung}Job` | `RecalculateInventoryJob` |
| Fehlercode | `{modul}.{fall}` klein, Punktebenen | `lager.beleg_gesperrt` |

---

## 10. Verbotsliste

- **Kein Datenbankzugriff in UI/Templates/Controller** (`DB::`, Eloquent, Query-Builder) —
  UI und API rufen Vorgänge/Abfragen.
- **Kein Direktzugriff auf fremde Module** — weder deren Modelle/Tabellen/Relationen noch
  deren `domain/`/`persistence/`-Namensräume (Deptrac-geprüft, Ausnahmen nur über die
  Application-Schnitt).
- **Kein `Model::create($request->all())`** und keine direkte Attribut-Zuweisung von außen
  (§3.1).
- **Keine Fachlogik in Livewire-Komponenten**, Jobs, Artisan-Befehlen oder Middleware.
- **Keine `env()`-Aufrufe außerhalb von `config/`** — Konfiguration wird gecacht; ein
  `env()` im Code bricht im Produktivbetrieb still.
- **Keine Abfrage ohne Obergrenze**, kein `Model::all()` außerhalb von Seeds
  (`_architecture.md` §9).
- Keine Zeit/Zufall über `time()`/`microtime()`/`mt_rand()` (§3.1).
- Keine fachlichen Fassaden/Service-Location (`app(…)`) in Domain und Application
  (`_code.md` §5.3).
- Keine zweite Implementierung derselben Wirkung (Vorgang muss der einzige Weg sein, §4.1).
- Kein `dd()`, `dump()`, `var_dump()`, `print_r()` im ausgelieferten Code.

---

## Verweise

- `_architecture.md` §4–§8 — Schichten, Modulgrenzen, Vorgänge, Transaktionen.
- `_code.md` — Benennung, Promotion, Blade/Livewire-Konventionen.
- `_data.md` — Spalten, Typen, Migrationen, Auslieferungsdaten.
- `_integration.md` — Fehlerformat, Ereignisse, Outbox, Versionierung.
- `_security.md` §2.2 — Durchsetzung der Rechte in dieser Schicht.
- `_test-strategy.md` — Testebenen und Ablage der Tests.
