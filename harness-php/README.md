# Dev-Harness (PHP)

Verbindliche Entwicklungsrichtlinien für Anwendungen **einer festen Bauart**: PHP mit
**Laravel** als modularer, geschichteter Monolith, **Livewire** als Oberfläche, **MySQL 8
oder MariaDB 10.6+** als Datenbank, vorgangsgetriebene Fachlichkeit, Betrieb beim Kunden im
Haus (Container-/VM-Installation) oder auf klassischem Webhosting — mit **genau einer
Firma je Installation**: mehrere Firmen eines Kunden sind mehrere, komplett getrennte
Installationen.

Passt ein Projekt nicht dazu — andere Sprache, anderes Oberflächenmodell, anderer Datenbanktyp
— dann gilt dieser Harness dafür **nicht**. Er wird in diesem Fall nicht verwässert; es
entsteht ein eigener.

Dieses Repository enthält **ausschließlich Regeln**: keine Projektnamen, keine Pfade, keine
Herstellernamen, keine Fachbegriffe eines Produkts. Die stehen im Profil `PROJEKT.md` des
jeweiligen Projekts.

## Einstieg

**`_index.md`** ist der Einstieg: Geltungsbereich, vollständiger Regelindex, Dev-Workflow und
die Liste der geschützten Dateien.

Diese README beschreibt nur das Repository selbst — **jede inhaltliche Regel steht in
`_index.md` und den dort verzeichneten Dateien**, damit es keine zweite, driftende Fassung
gibt.

## In ein Projekt einbinden

Der Harness wird als Git-Subtree unter `harness/` eingebunden. Subtree statt Submodul, damit
die Regeldateien echte Dateien im Projekt-Checkout sind und die Analyse-Skripte sowie die
Importe der Einstiegsdatei ohne zusätzlichen Init-Schritt funktionieren.

```sh
git remote add harness <URL dieses Repositorys>
git subtree add --prefix=harness harness main --squash
```

> Die URL steht bewusst nicht in dieser Datei: Sie enthält den Herstellernamen, den
> `analyse_harness.py` als Projektliteral zurückweist. Sie gehört in die
> Entwicklerdokumentation des Projekts.

Danach im Projekt einmalig:

1. `harness/PROJEKT.template.md` als `PROJEKT.md` ins Wurzelverzeichnis kopieren und
   ausfüllen. Offene Werte lassen die Analyse-Skripte mit einer Meldung abbrechen, statt eine
   Prüfung durchzuführen, die nichts prüft.
2. `harness/reference.css` **unverändert** an das Ziel aus `[ui] stylesheet` kopieren. Farben
   und Schriften des Projekts überschreiben ausschließlich die Tokens in `[ui] theme`.
3. Die Prüfwerkzeuge als Entwicklungs-Abhängigkeiten aufnehmen (PHPStan, Laravel Pint,
   Deptrac — via Composer in den `[php] wurzel` genannten Ordner) und die Regelvorlagen
   übernehmen: `harness/phpstan.neon` und `harness/pint.json` unverändert ins Projekt
   (Ziel/Referenzierung nach Werkzeug-Mechanik), `harness/deptrac.yaml` kopieren und die
   Platzhalter für Namensraum-Präfix und Modulnamen aus dem Profil befüllen — Deptrac kann
   keine Profilwerte lesen.
4. Einstiegsdatei anlegen (`AGENTS.md`, bei Bedarf `CLAUDE.md` daneben), die `harness/_index.md`
   und `PROJEKT.md` importiert und selbst nur den Projektbezug enthält.

## Aktualisieren

```sh
git subtree pull --prefix=harness harness main --squash
```

`--squash` bleibt bei jedem Zug gesetzt — gemischt mit und ohne führt zu Konflikten in der
Subtree-Historie.

## Im Projekt unveränderlich

Regeländerungen entstehen **in diesem Repository** und werden von dort übernommen, nie
umgekehrt. Wer eine Harness-Datei ändern müsste, um ein Projekt abzubilden, hat einen Wert am
falschen Ort — er gehört ins Profil.

Für Code und Design sind die maschinenlesbaren Regeldateien führend, nicht die `.md`-Leitfäden:
`phpstan.neon` + `pint.json` (Code, PHPStan/Pint) und `deptrac.yaml` (Design, Deptrac).
Weichen Beschreibung und Regeldatei voneinander ab, gilt die Regeldatei.

## Analyse-Skripte

| Skript | Prüft | Läuft |
|---|---|---|
| `analyse_harness.py` | ob dieses Repository projektneutral geblieben ist | hier **und** im Projekt |
| `analyse_code.py` | PHP-Code gegen `phpstan.neon` + `pint.json` | nur im Projekt |
| `analyse_design.py` | Schichten-/Modulgrenzen gegen `deptrac.yaml` | nur im Projekt |
| `analyse_all.py` | ruft Code- und Design-Prüfung auf und schreibt das Sammelergebnis | nur im Projekt |

Die letzten drei erwarten das Profil `PROJEKT.md` eine Ebene über `harness/` und brechen
außerhalb eines Projekts mit einer Meldung ab. Nach jeder Änderung in diesem Repository:

```sh
python analyse_harness.py
```

Ein Fund ist ein Fehler, keine Warnung. Das Skript findet allerdings nur, was in seiner
Musterliste steht — wer eine Regeldatei aus einem anderen Projekt übernimmt, liest sie und
verlässt sich nicht auf das Skript.

## Ideen-Werkzeug

| Skript | Zweck | Läuft |
|---|---|---|
| `idee_neu.py` | legt die nächste Ideen-Datei an und pusht sie sofort — reserviert die laufende Nummer zentral gegen Doppelvergabe | nur im Projekt |

Regeln und Ablauf: `_ideas.md`, Abschnitt „Ablage".
