# PROJEKT.md — Projektprofil (Vorlage)

Diese Datei ist die **einzige Quelle für projektspezifische Werte**. Die Regeldateien unter
`/harness/` nennen keine Projektnamen, Pfade oder Domänenbegriffe — sie verweisen hierher.

**Verwendung:** beim Aufsetzen eines Projekts nach `PROJEKT.md` ins Wurzelverzeichnis des
Projekts kopieren (**nicht** unter `/harness/`, das ist geschützt und wird zentral gepflegt)
und ausfüllen. Jedes `AUSFÜLLEN` muss ersetzt werden; die Analyse-Skripte brechen mit einer
Meldung ab, solange ein von ihnen benötigter Wert noch offen ist.

---

## Maschinenlesbares Profil

Der folgende Block wird von `harness/analyse_*.py` mit `tomllib` gelesen. Es muss der **erste**
` ```toml `-Block der Datei sein. Nur flache Werte, Listen und Tabellen — keine Kommentare mit
Werten darin.

```toml
[projekt]
# Anzeigename und Kurzname (Kurzname wird für den PHP-Namensraum-Präfix verwendet)
name     = "AUSFÜLLEN"
kurzname = "AUSFÜLLEN"
# Bauart entscheidet, ob dieses Harness überhaupt passt — siehe Geltungsbereich in _index.md
bauart   = "php-laravel-livewire-mysql"
# Sprachen der Oberfläche, erste ist die Ausgangssprache
sprachen = ["de", "en"]

[pfade]
# Alle Pfade repo-relativ, mit Schrägstrich, ohne führenden Schrägstrich.
backend   = "AUSFÜLLEN"   # Wurzel der Modulverzeichnisse (Module mit den vier Namensräumen)
frontend  = "AUSFÜLLEN"   # Livewire-/Blade-Oberfläche
apitests  = "AUSFÜLLEN"   # Testverzeichnis für API-/Integrationstests (Pest)
uitests   = "AUSFÜLLEN"   # Playwright-Projekt
specs     = "specs"
ideen     = "ideas"
dokuUser  = "docs/user"
dokuDev   = "docs/dev"
analyse   = "analysis"
# Suchwurzel der Design-Prüfung: geprüft wird, was darunter liegt — nichts darüber. Setzen,
# sobald Code außerhalb von backend liegt (Plattform, Oberfläche); sonst bleibt er ungeprüft und
# der Lauf meldet trotzdem Erfolg. Ohne Eintrag gilt backend. analyse_design.py nennt am Ende
# jedes Modulverzeichnis der Modulwurzel, das nicht vorkam.
pruefwurzel = ""          # z. B. "app", wenn backend auf "modules" zeigt

[php]
# Wurzel des Composer-Projekts (dort liegen composer.json, vendor/, artisan)
wurzel      = "AUSFÜLLEN"           # z. B. "." oder "backend"
# Wurzel der Fachmodule; jedes Modul ist ein Verzeichnis darunter (_design.md §2)
modulwurzel = "modules"
# Regelvorlagen: ohne Eintrag gelten diese Vorgaben. deptrac.yaml ist die befüllte Kopie
# der Harness-Vorlage im Projekt (Platzhalter für Namensraum-Präfix und Modulnamen);
# phpstan.neon und pint.json werden direkt aus dem Harness verwendet.
# phpstanConfig = "harness/phpstan.neon"
# pintConfig    = "harness/pint.json"
# deptracConfig = "deptrac.yaml"

[ui]
# Zielname der Stylesheet-Kopie im Projekt; Vorlage bleibt harness/reference.css
stylesheet = "AUSFÜLLEN"               # z. B. "public/css/beispiel-ui.css"
# Datei mit den projektspezifischen Token-Überschreibungen (Akzentfarbe, Schriften)
theme      = "AUSFÜLLEN"               # z. B. "public/css/theme.css"

[ui.modulgruppen]
# Einstellige Modulgruppen der Navigation (siehe _uiux.md §4). Anzahl und Benennung sind
# projektspezifisch; das Schema „Gruppe.Untermodul" ist es nicht.
1 = "AUSFÜLLEN"

[daten]
# Fachliche Grundgrößen des Datenmodells (_data.md §6). Hauswährung als ISO-4217-Code,
# Gewichtsbasis als UN/ECE-Einheitencode — beide entscheiden, in welcher Einheit gespeichert
# und gerechnet wird, nicht nur wie angezeigt wird.
hauswaehrung = "AUSFÜLLEN"             # z. B. "EUR"
gewichtBasis = "AUSFÜLLEN"             # z. B. "GRM" (Gramm) oder "KGM" (Kilogramm)
# Nur setzen, wenn das Produkt harte Lückenlosigkeit der Belegnummern verlangt (_data.md §7.4).
# Folge: Vergabe in der Vorgangstransaktion, Freigaben je Belegart serialisiert.
# belegnummernLueckenlos = true

[budgets]
# Leistungsbudgets aus _architecture.md §9. Eingetragen wird nur, was von der Harness-Vorgabe
# abweicht; ohne Eintrag gilt die Vorgabe dort. Ein Profilwert ist eine Festlegung und braucht
# keine ADR — eine Verletzung des geltenden Werts schon (§11).
sitzungen = 300                        # gleichzeitig offene Sitzungen auf einer Instanz
```

---

## Produkt und Domäne

Zwei bis fünf Sätze: Was ist das Produkt, für wen, welches Altsystem löst es ab. Fachliche
Einzelheiten gehören **nicht** hierher, sondern nach `projekt/_domaene.md` (siehe unten).

## Domänenergänzungen

Die Regeldateien im Harness sind fachlich neutral. Alles, was nur für dieses Produkt gilt,
liegt in `projekt/_domaene.md` und wird von dort aus den Regeldateien zugeordnet:

| Bereich | Was das Projekt ergänzen muss |
|---|---|
| Datenmodell (`_data.md`) | fachliche Datentypen und ihre Genauigkeit (Geld, Mengen, Maße), fachliche Schlüssel, je Belegart: Belegrolle und Buchungszeitpunkt, Änderbarkeit in Freigegeben, Positivliste buchungsneutraler Felder (§7.3), Dokumentarten mit Aufbewahrungsfristen (§9.4) |
| Architektur (`_architecture.md`) | Produktlandschaft, Modulschnitt, Vorgänge je Modul |
| Integration (`_integration.md`) | externe Systeme, Austauschformate, Ablaufwerkzeuge |
| KI (`_ai.md`) | Schutzklassen der Daten, welche Anwendungsfälle lokal bleiben müssen |
| Sicherheit (`_security.md`) | Rollen und ihre fachlichen Rechte |
| Oberfläche (`_uiux.md`) | Modulgruppen, Farb-/Schrifttokens, fachspezifische Detail-Tabs |

## Glossar

Verbindliche Zuordnung des Oberflächenbegriffs (Ausgangssprache) zum Bezeichner im Code
(englisch). Ohne Eintrag kein neuer Begriff — gefordert von `_data.md`, `_code.md` und
`_uiux.md` §2. Datenbanknamen (Tabellen und Spalten) entstehen aus dem Oberflächenbegriff:
deutsch, klein, ohne Umlaute (`_data.md` §2).

| Oberfläche | Code | Bedeutung |
|---|---|---|
| AUSFÜLLEN | AUSFÜLLEN | AUSFÜLLEN |

## Stakeholder und Rollen

Wer bringt Ideen ein und entscheidet fachlich (`_ideas.md` setzt eine benannte
Stakeholder-Rolle voraus), wer betreibt, wer nutzt.

| Rolle | Person / Funktion |
|---|---|
| Fachliche Entscheidung | AUSFÜLLEN |
| Betrieb | AUSFÜLLEN |
| Anwender (Zielgruppe der Endkunden-Doku) | AUSFÜLLEN |

## Betrieb

Betriebsform (Container/VM beim Kunden, von uns als Dienst oder klassisches Webhosting —
`_operations.md` §4), Hoster-Anforderungen belegt, Ports, Umgebungen, die Firma der
Installation und ggf. weitere Installationen desselben Kunden (je Firma eine,
`_data.md` §5) — soweit für `_operations.md` und `_security.md` nötig. Die Sitzungszahl
steht als Zahlenwert im Profil (`[budgets] sitzungen`); hier gehört nur hin, was sie
begründet.
