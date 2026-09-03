# _code.md — PHP-Codestil

Verbindlicher Codestil für allen PHP-Code der Plattform und der Produkte. Ziel ist Code, der
mit den Regelvorlagen des Harness **keinen PHPStan-Hinweis und keinen Pint-Verstoß** erzeugt
(§12).

Dieses Dokument regelt **wie geschrieben wird** — Benennung, Formatierung, Sprachgebrauch.
**Was wohin gehört**, regelt `_architecture.md`; **welches Muster** eine Klasse hat, regelt
`_design.md`.

Agenten wenden diese Regeln **ab der ersten Zeile** an, nicht als Aufräumschritt danach.

Geprüft mit `python harness/analyse_code.py`; führend sind die Regelvorlagen
`harness/phpstan.neon` und `harness/pint.json` (§12).

---

## 1. Sprache der Bezeichner

- **Alle Bezeichner sind englisch**, nach dem verbindlichen Glossar im Projektprofil
  (`PROJEKT.md`; Pflicht dazu in `_requirements.md` §5). Ein Fachbegriff hat genau eine
  Entsprechung; ein Begriff außerhalb des Glossars ist ein Regelverstoß, kein Stilfehler.
- **Kommentare sind deutsch** und erklären das *Warum*, nicht das *Was*.
- Anwendertexte stehen nie im Code, sondern in Sprachdateien (`lang/`,
  `_architecture.md` §6).

---

## 2. Benennung

| Element | Stil | Beispiel |
|---|---|---|
| Klasse, Enum, Trait | `PascalCase` | `ConfirmSalesOrder`, `BelegStatus`, `HasBelegnummer` |
| Schnittstelle | `PascalCase` mit Suffix `Interface` | `NumberSequenceInterface` |
| Abstrakte Klasse | `PascalCase` mit Präfix `Abstract` | `AbstractBeleg` |
| Methode, Funktion | `camelCase`, Verb am Anfang | `confirmPosition()` |
| Variable, Property | `camelCase` | `$netAmount` |
| Konstante | `SCREAMING_SNAKE_CASE` | `MAX_POSITIONS` |
| Enum-Cases | `PascalCase` | `BelegStatus::Freigegeben` |
| Datenbank (Tabelle, Spalte) | deutsch, klein, ohne Umlaute | `_data.md` §2 |
| Konfigurationsschlüssel | klein, Punktebenen | `module.lager.max_positionen` |

- Datei- und Klassenname stimmen überein (PSR-4); eine Klasse je Datei.
- Testklassen heißen `F{n}_S{n}_{Story}Test` (Unterstriche erlaubt, siehe
  `_test-strategy.md` §3); Pest-Dateien enden auf `Test.php`.
- Abkürzungen aus dem Glossar werden wie Wörter geschrieben (`HttpClient` ist Framework-
  Sache; eigener Code schreibt `SchnittstellenClient` — nein: Glossarbegriff `…Client`).

### 2.1 Kein `Async`-Suffix

PHP ist pro Request synchron: Methodennamen bekommen **kein** `Async`-Suffix und geben
keine Promises zurück. Asynchrones läuft über Warteschlangen (§7) — ein Job heißt nach
seiner fachlichen Wirkung (`RecalculateInventoryJob`), nicht nach seiner Technik.

---

## 3. Dateien und Typen

- **Erste Anweisung jeder PHP-Datei ist `declare(strict_types=1);`** (mit Zeile Abstand
  nach dem öffnenden `<?php`); nur Dateien ohne Typdeklaration — reine Konfigurations-
  und Migrations-Arrays — sind davon ausgenommen.
- Eine Klasse je Datei; Namensraum folgt dem Verzeichnis (PSR-4), Wurzel je Modul
  (`_design.md` §2).
- `use`-Blöcke: vollqualifizierte Namen importieren statt im Code auszuschreiben;
  Gruppierung und alphabetische Sortierung überlässt Pint; Aliase nur bei
  Namenskollision, dann sprechend (`use …\Request as VorgangRequest` ist verboten —
  kollidierende Klassen unterschiedlich benennen statt aliassen).
- **Klassen sind `final`, Vererbung nur wo der Leitfaden sie vorsieht** (Basisklassen des
  Frameworks wie `Model`, `Component`, `FormRequest`; eigene Basisklassen nur in
  Plattformbausteinen, `_design.md` §2.1).
- `readonly` für Klassen, die nach dem Bauen nicht mehr verändert werden (DTOs,
  Wertobjekte, Ergebnisse).

---

## 4. Formatierung

Formatierung ist **maschinell**, nicht verhandelt: Pint mit `harness/pint.json`
(PSR-12-Basis) ist führend. Wer von Hand anders formatiert, erzeugt Diffs ohne Inhalt.

- Klammerstil: öffnende Klammer bei Klassen/Methoden in eigener Zeile, bei
  Kontrollstrukturen in derselben (PSR-12).
- Eine Anweisung je Zeile; keine leeren Codeblöcke; `elseif` statt `else if`.
- Kurze Array-Syntax `[]`, niemals `array()`.
- Trailing Comma in mehrzeiligen Arrays und Parameterlisten.

---

## 5. Sprachgebrauch

### 5.1 Grundlagen

- **Property-Promotion im Konstruktor** ist die Voreinstellung für Dienste und DTOs —
  doppelte Property-/Parameter-/Zuweisungsblöcke sind verboten (§5.3).
- `match` statt `switch`, wo ein Wert abgebildet wird; `switch` nur für Seiteneffekte.
- Null-Safe-Operator `?->` und Null-Coalescing `??`/`??=` statt `isset()`-Ketten.
- String-Interpolation `"Beleg {$beleg->nummer}"` bzw. `sprintf` für formatierte Werte;
  keine Punkt-Verkettung über mehr als zwei Teile.
- Kurze Closures `fn (` für Einzeiler; lange Closures mit `static fn`/`static function`,
  wenn kein `$this` gebraucht wird.
- Named Arguments, sobald ein Aufruf mehr als zwei optionale Parameter hat oder
  boolesche Literale enthält: `setzeStatus(archiviert: true)` statt `setzeStatus(true)`.
- Enums (backed) für feste Wertelisten — keine Klassenkonstanten-Sammlungen mehr.

### 5.2 DTOs und Wertobjekte statt assoziativer Arrays

Daten zwischen Schichten wandern in **readonly-Klassen** (Vorgangs-`Request`/`Response`,
Abfragezeilen, `_design.md` §4) oder Wertobjekten (`_design.md` §3.2) — nie als
Assoziative Arrays mit String-Schlüsseln. Assoziative Arrays sind nur für
Konfigurationsrückgaben des Frameworks und blade-nahe View-Daten erlaubt.

### 5.3 Konstruktor-Property-Promotion

```php
final class ConfirmSalesOrder
{
    public function __construct(
        private SalesRepository $sales,   // injizierte Dienste promoted und privat
        private NumberSequenceInterface $numbers,
    ) {}
}
```

Abhängigkeiten kommen ausschließlich über den Konstruktor (Container), nie über
`app()`, Fassaden-Auflösung oder Service-Location im Methodenrumpf. Fassaden sind in der
Application-/Domain-Schicht nur für die dort erlaubten Querschnittsdienste zulässig
(`_design.md` §10); im Domain-Code gar nicht.

---

## 6. Nullbarkeit

- Jede Property, jeder Parameter, jede Rückgabe ist **typisiert**. Nullable wird als
  `?string` geschrieben, nicht als PHPDoc allein; PHPDoc ergänzt nur, was das Typsystem
  nicht ausdrückt (z. B. `list<VorgangsZeile>`, Generics wie `Collection<int, Position>`).
- Ein nullable Rückgabewert braucht eine im Namen oder Kommentar erkennbare Bedeutung
  (`findOrNull`); „kann auch null sein, wenn etwas schiefging" ist verboten — das ist ein
  Fehlerfall (§8).
- Kein verstecktes Null: Eloquent-Navigationen sind lazy und können „leer" wirken —
  Verhalten dazu steht in `_design.md` §3.1, nicht hier.

---

## 7. Warteschlangen und lange Laufzeiten

PHP-Requests sind synchron und kurzlebig. Alles, was länger als das Request-Budget dauert
oder nicht synchron zum Aufrufer gehört (Mailversand, Exporte, externe Aufrufe,
Massenänderungen), läuft als **Job in der Warteschlange** oder als geplanter Artisan-
Befehl:

- Jobs sind dünn: sie rufen Vorgänge der Application-Schicht auf (`_design.md` §4);
  Fachlogik im Job ist verboten.
- Jobs sind idempotent (`_integration.md` — Wiederholung muss erträglich sein) und
  tragen ihre Korrelations-ID (`_operations.md` §1).
- Im Request darf nicht auf externe Dienste synchron gewartet werden, wenn das Ergebnis
  nicht sofort gebraucht wird — einreihen statt blockieren.
- `sleep()`, `usleep()` und Polling-Schleifen sind verboten; Warten erledigt der
  Scheduler/Queue-Treiber.

---

## 8. Fehlerbehandlung

- **Erwartbare fachliche Fehler** (Validierung, Statuskonflikt, Kontierungsfehler) sind
  Teil des Ergebnisses: Vorgänge geben ein `Result` mit stabilem Fehlercode zurück
  (`_design.md` §4.3), Ausnahmen dafür sind verboten.
- **Ausnahmen** nur für Programmierfehler und Ausreißer (Datenbank weg, Vertragsbruch
  intern). Sie steigen bis zur zentralen Fehlerbehandlung (`_operations.md` §3) — niemals
  schlucken, niemals `catch (\Throwable)` ohne Wiederwurf.
- Keine Ausnahmen als Flusssteuerung im positiven Pfad (`try { … } catch (NotFound) { …
  weiter }` als Normalweg ist verboten — vorher prüfen).
- Fehlermeldungen für Anwender kommen aus den Sprachdateien; Fehlercodes bleiben
  sprachneutral und stabil.

---

## 9. Blade und Livewire

- **Eine Livewire-Komponente ist dünn**: sie bindet Eingaben, ruft Vorgänge und Abfragen
  der Application-Schicht auf und gibt View-Daten aus. Fachlogik, Fachprüfung und
  Datenbankschreibzugriffe gehören in Application/Domain (`_design.md` §6), nie in die
  Komponente und nie in Blade.
- Überschreitet die Klasse einer Komponente ~30 Zeilen Handlogik, wandert sie in eigene
  Klassen (Vorgang, Abfrage, View-Mapper).
- Eingaben werden serverseitig validiert — in einer Form-Request-Klasse oder dem
  Validierungsteil des Vorgangs; Validierung nur im Browser ist keine Validierung.
- Blade-Views enthalten **keine Datenbankaufrufe**: kein Eloquent, kein Query-Builder,
  keine Vorgänge in Templates — nur Daten, die die Komponente geliefert hat.
- `wire:key` bei Schleifen dynamischer Listen ist Pflicht; `wire:navigate` für interne
  Navigation nach `_uiux.md` §4a; Tastaturbelegung und kurze Client-Interaktionen laufen
  über Alpine.js (`_uiux.md` §5, §10).
- Die Basisklassen aus `reference.css` werden benutzt (`_uiux.md` §12); eigene CSS-
  Klassen außerhalb der Tokens sind verboten, eigenes Inline-`style=""` erst recht.

---

## 10. Kommentare

- Kommentare erklären das *Warum* (Entscheidung, Zusammenhang, Nebenbedingung), nie das
  *Was* — der Code benennt das Was selbst. Ein Kommentar, der den Code wiederholt, wird
  entfernt.
- PHPDoc nur, wo das Typsystem etwas nicht ausdrückt (Generics, `list<…>`, Framework-
  Magie) oder der Aufrufer eine Bedingung kennen muss. Getter/Setter-Doku und
  offensichtliche `@param`-Wiederholungen sind verboten.
- TODO/FIXME nur mit Ticketnummer und in einer Form, die PHPStan als solche melden kann;
  ohne Ticket ist es keine Aufgabe, sondern Lärm.

---

## 11. Beispiel

```php
<?php

declare(strict_types=1);

namespace Modules\Beispiel\Application\Sales;

use Modules\Beispiel\Application\Shared\Result;
use Modules\Beispiel\Domain\Sales\SalesOrder;

final class ConfirmSalesOrder
{
    public function __construct(
        private NumberSequenceInterface $numbers,
    ) {}

    public function execute(Request $request): Result
    {
        $order = SalesOrder::query()->find($request->orderId);

        if ($order === null) {
            // Fehlercode ist Vertrag der Integrations-API — stabil, nie umbenennen.
            return Result::fehler('sales.order_not_found');
        }

        return $order->confirm($this->numbers);
    }
}
```

Regeln am Beispiel: Klasse `final`, Dienst promoted, frühe Rückkehr mit `Result`,
Fachprüfung und Zustandsänderung in der Domain (`SalesOrder::confirm()`), kein Framework-
Rauschen in der Klasse. Das vollständige Vorgangs-Muster steht in `_design.md` §4.

---

## 12. Prüfung und begründete Ausnahmen

Geprüft wird maschinell — die **Regelvorlagen sind führend**, diese Datei beschreibt sie:

| Werkzeug | Regelvorlage | Prüft |
|---|---|---|
| PHPStan | `harness/phpstan.neon` | Typen, Nullbarkeit, ungenutzter Code, Sprachgebrauch |
| Pint | `harness/pint.json` | Formatierung, Benennung soweit maschinell prüfbar |

Lauf: `python harness/analyse_code.py` (schreibt `analysis/code.json`). Ziel: **null
Hinweise**.

- Eine Ausnahme ist begründungspflichtig: PHPStan-Unterdrückung mit Kommentar am Ort
  (`// phpstan:ignore …` inkl. Regel-ID und Grund); Pint-Ausnahmen nur in der Vorlage,
  nie vor Ort.
- Baselines sind verboten. Ein vorhandener Bestand wird nicht „auf Raten" akzeptiert —
  neu geschriebener Code ist sauber, angefasster Code wird mit sauber gemacht.
- Weicht diese Beschreibung von der Regelvorlage ab, gilt die **Regelvorlage** — und die
  Abweichung ist hier nachzuziehen (nur nach Rückfrage, `_index.md` „Geschützte Dateien").

---

## 13. Erzeugter Code

- Vom Framework oder von Artisan generierte Dateien (Skeletons, `bootstrap/cache`,
  kompilierte Views, `vendor/`) werden **nie von Hand geändert** und von der Prüfung
  ausgenommen (Ausschlüsse in `harness/phpstan.neon`).
- Generierte Stubs werden nach dem Erzeugen auf diesen Stil gebracht (Promotion,
  `final`, `strict_types`), sobald sie eigener Code werden.
- Migrationen sind generiert *und* gepflegt: sie folgen den Regeln in `_data.md` §11 und
  bleiben trotz Generator-Herkunft eigenverantwortlicher Code.

---

## Verweise

- `_architecture.md` — was wohin gehört (Schichten, Modulgrenzen).
- `_design.md` — Muster je Schicht (Entität, Vorgang, Abfrage, Komponente).
- `_data.md` §2 — Namensgebung der Datenbank.
- `_test-strategy.md` §3 — Benennung der Testklassen.
- `_uiux.md` §10, §12 — Umsetzung und Klassenverzeichnis der Oberfläche.
