# PROJEKT.md — Projektprofil

Diese Datei ist die **einzige Quelle für projektspezifische Werte**. Die Regeldateien unter
`/harness/` nennen keine Projektnamen, Pfade oder Domänenbegriffe — sie verweisen hierher.

---

## Maschinenlesbares Profil

```toml
[projekt]
name     = "Yoga Sabrina Becker"
kurzname = "yoga"
bauart   = "php-laravel-livewire-mysql"
sprachen = ["de"]

[pfade]
backend   = "modules"
frontend  = "src/resources/views"
apitests  = "src/tests/Feature"
uitests   = "tests-e2e"
specs     = "specs"
ideen     = "ideas"
dokuUser  = "docs/user"
dokuDev   = "docs/dev"
analyse   = "analysis"
harness   = "harness-php"
pruefwurzel = "."

[php]
wurzel      = "."
modulwurzel = "modules"
phpstanConfig = "phpstan.neon"
pintConfig    = "pint.json"
deptracConfig = "deptrac.yaml"

[ui]
stylesheet = "src/public/css/yoga-ui.css"
theme      = "src/public/css/theme.css"

[ui.modulgruppen]
1 = "Webseite"
2 = "Verwaltung"

[daten]
hauswaehrung = "EUR"
gewichtBasis = "KGM"
belegnummernLueckenlos = true

[budgets]
sitzungen = 300
```

---

## Produkt und Domäne

Eine öffentliche Webseite für Yoga-Kurse, -Events und -Workshops von Sabrina Becker mit einem
einheitlichen Backend. Das Backend dient gleichzeitig als Content-Management-System (CMS) für die
Webseite und als Verwaltungstool für Kurse/Events/Workshops, Teilnehmer, Anmeldungen inklusive
Warteliste sowie Bar- und Überweisungszahlungen mit fortlaufenden Belegnummern. Es gibt keine
Vorgänger-Webseite; der bisherige Betrieb läuft über Social Media, handschriftliche Barquittungen
und eine manuelle Bareinnahmenliste.

## Domänenergänzungen

Die Regeldateien im Harness sind fachlich neutral. Alles, was nur für dieses Produkt gilt,
liegt in `projekt/_domaene.md` und wird von dort aus den Regeldateien zugeordnet:

| Bereich | Was das Projekt ergänzen muss |
|---|---|
| Datenmodell (`_data.md`) | fachliche Datentypen (Preis, Dauer, maximale Teilnehmerzahl), Belegrollen (Bareinnahmenbeleg, Rechnung), Buchungszeitpunkt, fortlaufende Belegnummern |
| Architektur (`_architecture.md`) | Produktlandschaft (Webseite + Backend), Modulschnitt (CMS, Kursverwaltung, Teilnehmerverwaltung, Zahlungsverwaltung), Vorgänge je Modul |
| Integration (`_integration.md`) | E-Mail-Versand an Teilnehmer, ggf. Social-Media-Öffnung |
| KI (`_ai.md`) | Gesundheitsinformationen von Teilnehmern dürfen nicht an externe KI-Dienste übertragen werden |
| Sicherheit (`_security.md`) | Rollen: Administrator/in (Sabrina Becker), ggf. Helfer/in mit eingeschränkten Rechten |
| Oberfläche (`_uiux.md`) | Modulgruppen Webseite/Verwaltung, Token-Überschreibungen im Theme |

## Glossar

| Oberfläche | Code | Bedeutung |
|---|---|---|
| Kurs | Course | Wiederkehrender Yoga-Kurs mit Terminserie |
| Event | Event | Einmalige Veranstaltung |
| Workshop | Workshop | Meist längere, thematisch fokussierte Veranstaltung |
| Veranstaltung | Activity | Übergeordneter Begriff für Kurs, Event oder Workshop |
| Termin | Session | Einzelner Termin einer Veranstaltung |
| Teilnehmer/in | Participant | Person, die an Kursen/Events/Workshops teilnimmt |
| Anmeldung | Registration | Verknüpfung zwischen Teilnehmer/in und einer Veranstaltung |
| Warteliste | WaitingList | Rangfolge von Anmeldungen über der maximalen Teilnehmerzahl |
| Zahlung | Payment | Barzahlung oder Überweisung für eine Anmeldung |
| Bareinnahmenbeleg | CashReceipt | Forlaufend nummerierter Beleg für eine Barzahlung |
| Rechnung | Invoice | Forlaufend nummerierte Rechnung für eine Überweisung |
| Gutschrift | CreditNote | Forlaufend nummerierte Gutschrift zu einer Rechnung |
| Rückgabebestätigung | CashReturn | Forlaufend nummerierter Beleg für Rückzahlung in bar |
| Vorlage | CourseTemplate | Wiederverwendbare Vorlage für wiederkehrende Kurse |
| Ausgehende Nachricht | OutboundMessage | Im System protokolliert versandte E-Mail |

## Stakeholder und Rollen

| Rolle | Person / Funktion |
|---|---|
| Fachliche Entscheidung | Sabrina Becker (Inhaberin/Yogalehrerin) |
| Betrieb | Zunächst Sabrina Becker; bei Bedarf später externer Dienstleister/Hosting-Partner |
| Anwender (Endkunden-Doku) | Teilnehmer/innen der Kurse/Events/Workshops |
| Anwender (Backend) | Sabrina Becker und ggf. zukünftige Helfer/innen |

## Betrieb

Zunächst klassisches Webhosting oder einfacher Container-Betrieb bei einem Hosting-Dienstleister.
Da es nur eine Firma/Inhaberin gibt, gibt es genau eine Installation. Die Sitzungszahl von 300 ist
die Harness-Vorgabe und reichlich dimensioniert für einen Ein-Personen-Betrieb mit gelegentlichen
Helfer/innen.
