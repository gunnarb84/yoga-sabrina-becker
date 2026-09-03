# _operations.md — Betrieb und Beobachtbarkeit

Verbindliche Regeln dafür, wie die Anwendung sich im Betrieb verhält und wie sie beim Kunden
installiert, aktualisiert, überwacht und gesichert wird.

Besonderheit dieses Vorhabens: Die Software läuft in vielen **voneinander unabhängigen
Installationen** — je Kunde eine, betrieben im Haus des Kunden oder von uns als Dienst
(§4.4). Beim Kunden schaut niemand aus eurem Team mal eben auf den Server; und auch im
eigenen Haus gilt dieselbe Grundhaltung: Alles, was im Fehlerfall gebraucht wird, muss
**vorher** eingebaut sein — nicht der Handgriff des Betreibers ist der Plan, sondern die
Ausnahme.

Übergeordnet: `_architecture.md`. Die Leistungsbudgets aus dessen §9 sind hier die Messlatte.

---

## 1. Protokollierung

### 1.1 Form

- **Strukturiert**, maschinell auswertbar — kein zusammengesetzter Freitext.
- Pflichtangaben je Eintrag: Zeitpunkt in UTC, Stufe, **Korrelations-ID**, Identität
  (als Kennung, nicht als Klartextname), Modul, Vorgang, Meldung.
- Die **Korrelations-ID** entsteht am Eingang — bei einer Interaktion in der Oberfläche, einem
  Aufruf der Integrations-API oder dem Start eines Hintergrundauftrags — und begleitet den
  gesamten Weg über Vorgang, Datenbank und ausgehende Aufrufe. Ohne sie ist ein Fehlerbild
  beim Kunden nicht rekonstruierbar.

### 1.2 Stufen

| Stufe | Bedeutung |
|---|---|
| **Fehler** | Ein Vorgang ist gescheitert, jemand ist betroffen |
| **Warnung** | Selbstheilung oder eingeschränkter Betrieb — etwa Wiederholung nach Zustellfehler |
| **Information** | Fachlich bedeutsamer Vorgang — Anmeldung, Buchung, Export, Aktualisierung |
| **Ablaufverfolgung** | Nur in der Entwicklung, in Produktion abgeschaltet |

### 1.3 Was niemals ins Protokoll gehört

Passwörter, Token, Schlüssel, vollständige personenbezogene Datensätze, Beleginhalte sowie
**sämtliche Daten der höchsten Schutzklasse** (`projekt/_domaene.md`). Im Zweifel wird die Kennung
protokolliert, nicht der Inhalt. Ergänzend gilt `_security.md`.

### 1.4 Abgrenzung zum Audit

Betriebsprotokoll und Audit-Protokoll sind **zwei verschiedene Dinge** und dürfen nicht
vermischt werden:

| | Betriebsprotokoll | Audit-Protokoll |
|---|---|---|
| Zweck | Fehlersuche und Betrieb | Nachweis, wer was fachlich getan hat |
| Speicherort | Protokollablage | Datenbank, nur Einfügen |
| Aufbewahrung | 90 Tage | Gesetzliche Fristen (`_data.md` §9) |
| Verzichtbar | Ja | Nein |

Ein Betriebsprotokoll ersetzt kein Audit, und ein Audit ist kein Ersatz für Protokollierung.

---

## 2. Beobachtbarkeit

### 2.1 Health

Zwei getrennte Endpunkte:

- **Lebt der Prozess** — beantwortet, ob der Dienst neu gestartet werden muss.
- **Ist er bereit** — prüft zusätzlich Datenbankverbindung, angewandten Migrationsstand und
  Verfügbarkeit der Plattformdienste. Weist **Anwendungs- und Plattformversion** aus, damit
  jederzeit feststellbar ist, welcher Stand bei welchem Kunden läuft.

### 2.2 Kennzahlen

Technisch:

- Antwortzeiten je Vorgangsart (95. und 99. Perzentil), gemessen gegen die Budgets aus
  `_architecture.md` §9,
- Zahl offener Sitzungen und Arbeitsspeicher je Sitzung,
- belegte Datenbankverbindungen gegen die Poolgrenze,
- **Rückstand der Outbox** und Zahl fehlgeschlagener Ereigniszustellungen,
- Laufzeit und Ergebnis von Hintergrundaufträgen,
- Zahl, Dauer und Kosten der KI-Aufrufe (`_ai.md`).

Fachlich:

- Belege je Tag,
- offene Klärfälle im Auftragseingang und **Alter des ältesten unverarbeiteten Eingangs**,
- ausstehende Abgleiche von Feld-Clients,
- **lizenzierte gegen aktive Benutzerkonten** und der Höchstwert gleichzeitiger Sitzungen
  je Konto im Zeitraum (`_security.md` §1.4, §1.5) — die Zahlen, mit denen ein
  Lizenzgespräch geführt wird.

### 2.3 Alarme

Mindestens bei: verletztem Antwortzeitbudget über einen längeren Zeitraum, wachsendem
Outbox-Rückstand, Speicher- oder Plattenknappheit, gescheiterter Sicherung, gehäuften
fehlgeschlagenen Anmeldungen und stehengebliebenem Auftragseingang.

### 2.4 Datenhoheit

Protokolle, Kennzahlen und Ablaufverfolgungen bleiben grundsätzlich **im Netz des
Betreibers der Installation** — beim Kunden also im Kundennetz. Bei von uns betriebenen
Installationen (§4.4) liegen sie in unserer Umgebung; die zentrale Überwachung ist dort
Teil des Betriebsvertrags, die Ausschlüsse aus §1.3 gelten unverändert.

- Eine Übermittlung an euch zu Zwecken der Fehlersuche und Überwachung ist zulässig, **soweit
  eine entsprechende Vereinbarung mit dem Kunden besteht**. Ohne Vereinbarung bleibt alles
  lokal.
- Die Übermittlung ist **je Installation konfigurierbar** und im Störungsfall abschaltbar
  (Betriebsschalter, §5.6).
- Die Ausschlüsse aus §1.3 gelten unverändert: Was nicht ins Protokoll gehört, wird auch nicht
  übermittelt. Das betrifft insbesondere Daten der höchsten Schutzklasse.
- Was übermittelt wird, ist in der Betriebsdokumentation der Installation benannt (§8).

---

## 3. Fehlerbehandlung

- **Zentral behandelt**, nicht in jedem Vorgang einzeln.
- Die anwendende Person sieht eine **verständliche Meldung und eine Vorgangskennung** — die
  Korrelations-ID. Damit kann der Support den Fall im Protokoll finden. **Niemals** ein
  Stapelabbild oder eine technische Meldung in der Oberfläche.
- Fehlerklassen mit definiertem Verhalten: Eingabefehler, fehlende Berechtigung,
  Nebenläufigkeitskonflikt, fachlicher Fehler, technischer Fehler.
- **Kein stiller Fehler.** Jeder abgefangene Fehler wird protokolliert. Ein leerer
  Ausnahmeblock ist ein Regelverstoß.

---

## 4. Auslieferung und Erstinstallation

### 4.1 Zwei Betriebsformen

Eine Installation läuft in einer von zwei **gleichwertigen Betriebsformen** (`_architecture.md`
§3); welche, steht im Projektprofil und in der Betriebsdokumentation (§8):

**a) Container-/VM-Installation** beim Kunden im Haus oder von uns als Dienst (§4.4).
Container-Verbund: Anwendung (PHP-FPM hinter Webserver), MySQL oder MariaDB,
Automatisierungswerkzeug, Reverse-Proxy, optional lokaler Modellserver. In dieser Form sind
dauerhafte Warteschlangen-Worker und zusätzliche Dienste (Zwischenspeicher, lokaler
Modellserver) möglich.

**b) Klassisches Webhosting** beim Hoster. Das ist die einfachste Form und muss **ohne
weiteres** funktionieren. Mindestanforderungen an den Hoster:

- PHP in der vom Produkt geforderten Version (als FPM hinter dem Hoster-Webserver),
- MySQL 8 oder MariaDB 10.6+,
- SSH-Zugang und Cron (minütlich),
- DocumentRoot legbar auf das `public/`-Verzeichnis; Schreibrechte nur auf
  `storage/` und `bootstrap/cache/`.

Auf Webhosting gilt: **keine eigenen Dienste** (kein Redis, kein lokaler Modellserver, kein
Supervisor) — Sitzungen liegen in der Datenbank oder im Dateisystem, die Warteschlange läuft
über den Datenbank-Treiber mit minütlichem Cron-Aufruf (`queue:work --stop-when-empty`),
der Scheduler über minütlichen Cron (`schedule:run`). Deployment per `git pull`,
`composer install --no-dev --optimize-autoloader`, Konfigurations-/Zwischenspeicher-Bau und
`migrate` (§5). Die Integritätsprüfung (§2.4) meldet einen Hoster, der die Anforderungen
nicht erfüllt.

Systemvoraussetzungen werden aus den Budgets abgeleitet (`_architecture.md` §9) und mit jeder
Version überprüft.

### 4.2 Konfiguration

- Über Umgebungsvariablen und eine Konfigurationsdatei je Installation.
- **Geheimnisse liegen nie im Abbild und nie im Repository** (`_security.md`).
- Jede Konfigurationsgröße hat eine dokumentierte Vorbelegung. Die Anwendung startet nicht,
  wenn eine Pflichtangabe fehlt — sie startet nicht halb und läuft dann falsch.
- **Die Lizenzangabe ist eine solche Pflichtangabe** und liegt signiert vor; ihre Signatur
  wird beim Start geprüft, ohne Rückkanal nach außen (`_security.md` §1.5; Datenhoheit
  §2.4 dieses Dokuments).

### 4.3 Schritte der Erstinstallation

1. Datenbank anlegen, Sortier- und Vergleichsregel setzen (`_data.md` §2)
2. Migrationen anwenden
3. Auslieferungsdaten einspielen (`_data.md` §13)
4. Firmenstammdaten der Installation pflegen (`_data.md` §5.1), Nummernkreise einrichten
5. Notfallzugang einrichten; Anmeldewege festlegen (`_security.md` §1): Identity-Provider
   anbinden und/oder lokale Benutzerverwaltung mit zweitem Faktor einrichten
6. Modulumfang und **lizenzierte Kontenzahl** der Installation festlegen — beides aus der
   signierten Lizenzangabe (`_security.md` §1.5)
7. Sicherung einrichten und **eine Wiederherstellung testen**
8. Rauchtest, Übergabe an den Betrieb

Die Schritte sind **skriptbar und wiederholbar** — bei einer Handvoll Installationen
Sorgfalt, ab dem Betrieb als Dienst (§4.4) Voraussetzung.

### 4.4 Betrieb als Dienst (Managed Hosting)

Eine Installation kann statt beim Kunden **von uns betrieben** werden. Es ändert sich der
Betreiber, nicht die Bauart: dieselbe Software, dieselbe Betriebsform nach §4.1 (a), dieselben
Regeln dieses Dokuments. Zusätzlich gilt:

- **Je Kunde ein eigener Container-Verbund** einschließlich eigener Datenbank, fest
  getrennt von allen anderen: eigenes Netzsegment, eigene Geheimnisse, eigene Sicherungen.
  Keine geteilte Datenbankinstanz, kein geteilter Verbund — auch nicht „nur für kleine
  Kunden".
- **Ressourcengrenzen je Installation** (Prozessor, Arbeitsspeicher, Platte), damit die
  Lastspitze eines Kunden keinen anderen ausbremst.
- Die **erweiterte Sicherungsstufe** (§6.1) ist der Standard, nicht die Option — wir sind
  hier selbst der Betreiber und können uns den Verlust eines Arbeitstags nicht als
  „abgewählt" zurechnen.
- **Auftragsverarbeitungsvertrag vor Produktivsetzung** und protokollierter
  Betreiberzugriff: `_security.md` §6.4.
- Das **Betriebsmodell steht in der Betriebsdokumentation** (§8) und ist je Installation
  eindeutig.

---

## 5. Aktualisierung im laufenden Betrieb

### 5.1 Was möglich ist

| Art der Änderung | Was nötig ist |
|---|---|
| **Konfiguration** — Belegvorlage, Textbaustein, Nummernkreis, Rechte, Preisliste, Ablauf im Automatisierungswerkzeug | Wirkt sofort, kein Neustart |
| **Code ohne Schemaänderung** | Neustart, unter einer Minute — die Anwendung ist zustandslos, ohne Wartungsfenster |
| **Code mit erweiternder Schemaänderung** (`_data.md` §11.1) | Sicherung, Migration, Neustart — bei eingehaltenem Erweiterungsprinzip ohne Wartungsfenster |
| **Code mit verändernder Schemaänderung** | Sicherung, Migration, Wartungsfenster mit Wartungsmodus (`down`) |

**Die Anwendung ist zustandslos** (`_architecture.md` §3): Sitzungen liegen außerhalb des
Prozesses, ein Austausch des Code-Stands verwirft keine geöffnete Arbeit. Ein Austausch
ohne Unterbrechung ist damit grundsätzlich möglich — über die erweiternden Migrationen aus
`_data.md` §11.1 und, im Container-Betrieb mit mehreren Instanzen, über rollierenden
Austausch. Was immer unterbrochen wird: **laufende Warteschlangen-Arbeit** — Worker werden
sauber beendet und neu gestartet (Webhosting: der nächste Cron-Lauf übernimmt), und die
Neu-Ladung der Oberfläche im Browser bleibt den Anwendenden als bewusster, angekündigter
Schritt sichtbar, wo sie es betrifft.

### 5.2 Ablauf

1. **Ankündigung** im System mit Zeitpunkt und erwarteter Dauer
2. **Anmeldung sperren**, laufende Sitzungen auslaufen lassen
3. **Hintergrundaufträge** abwarten oder sauber beenden
4. **Sicherung ziehen und auf Lesbarkeit prüfen** — eine ungeprüfte Sicherung ist keine
5. **Migration ausführen**, Laufzeit gegen die Messung aus der Freigabe halten
6. **Neues Abbild starten**
7. **Rauchtest:** Anmeldung, Liste laden, Beleg anlegen und buchen, Buchhaltungsexport,
   Health-Endpunkt, Versionsangaben
8. **Freigeben**, Anmeldung öffnen
9. **Nachbeobachten** — Fehlerrate und Antwortzeiten für mindestens 30 Minuten

Ohne größere Migration liegt das bei 5 bis 15 Minuten.

### 5.3 Rückkehr zur Vorversion

Ob eine Rückkehr möglich ist, entscheidet **die Art der Migration**, nicht die Liefertechnik:

- **Erweiternde Migration** (nur hinzufügen, nichts entfernen): Die alte Programmversion läuft
  weiterhin gegen das neue Schema. Rückkehr durch Starten des alten Abbilds — Sekunden, kein
  Datenverlust. **Das ist der Regelfall.**
- **Verändernde Migration:** Einziger Rückweg ist das Einspielen der Sicherung, alles seither
  Erfasste geht verloren.

Deshalb wird **Entfernen zeitlich getrennt**: neue Struktur hinzufügen und ausliefern, in
einer späteren Version die alte entfernen, wenn der neue Stand stabil läuft.

### 5.4 Freigabenotiz je Version

Verpflichtend, geht mit an den Kunden: Inhalt der Version, enthaltene Migrationen mit
**gemessener** Laufzeit, gesetzte Schalter, Rückkehrweg, Ergebnis des Rauchtests,
Plattformversion.

### 5.5 Rollout über mehrere Installationen

Gestaffelt: zuerst eine Pilotinstallation, dann die übrigen. Nie alle Kunden am selben Tag.
Welche Version bei welchem Kunden läuft, ist jederzeit nachweisbar (§2.1).

### 5.6 Schalter

Drei Arten, die **nicht** vermischt werden:

| Art | Zweck | Lebensdauer |
|---|---|---|
| **Release-Schalter** | Eine Änderung dunkel ausliefern und gestaffelt einschalten | Wochen — muss wieder entfernt werden |
| **Modulumfang je Installation** | Welche Module und Funktionen die Installation nutzt | Dauerhaft — das ist Konfiguration, kein Schalter |
| **Betriebsschalter** | Etwas im Störungsfall abschalten (KI, Auftragseingang, Abgleich) | Dauerhaft, sehr wenige |

Regeln für **Release-Schalter** — sie sind die einzige Art, die wuchern kann:

- Entsteht **nur**, wenn die Änderung einen laufenden Produktivprozess berührt oder gestaffelt
  eingeführt werden soll. Eine neue Auswertung oder eine Fehlerbehebung bekommt keinen.
- Trägt ab Anlage ein **Verfallsdatum** und eine **verantwortliche Person** und steht in
  einem **versionierten Verzeichnis im Repository**.
- **Ein Konformitätstest prüft das Verzeichnis bei jedem Bau:** ein überfälliger Schalter
  oder die Überschreitung der Höchstzahl bricht den Bau. Die Regel ist damit Mechanik, nicht
  Disziplin.
- Die **Entfernung gehört zur Definition of Done der Folgeversion**. Ein überfälliger Schalter
  ist ein Fehler, kein Schönheitsmangel.
- **Höchstens zehn gleichzeitig aktiv.** Wer den elften braucht, räumt zuerst auf.
- Wird **an genau einer Stelle** abgefragt, in der Application-Schicht. Keine Verschachtelung
  zweier Schalter.
- Die Testsuite läuft im Auslieferungszustand; eine geschaltete Funktion bringt ihre eigenen
  Tests mit eingeschaltetem Schalter mit.

---

## 6. Sicherung und Wiederanlauf

### 6.1 Zwei Stufen

| Stufe | Verfahren | Möglicher Datenverlust |
|---|---|---|
| **Grundstufe (Pflicht)** | Tägliche Vollsicherung, geprüft | Bis zu einem Arbeitstag |
| **Erweitert (empfohlen)** | Zusätzlich fortlaufende Archivierung der Transaktionsprotokolle | Höchstens 15 Minuten |

- Die **Grundstufe ist in jeder Installation Pflicht**.
- Die erweiterte Stufe wird **je Installation** vereinbart. Kosten: der Speicherbedarf ist
  gering, der Aufwand liegt in Einrichtung und Überwachung.
- **Wird die erweiterte Stufe abgewählt, wird die Folge schriftlich festgehalten:** Bei einem
  Ausfall geht die Arbeit seit der letzten Nachtsicherung verloren — für ein ERP mit 300
  Anwendenden ist das ein voller Arbeitstag.
- **Bei aktiver Archivierung ist ein Alarm auf das Archivziel Pflicht.** Läuft es voll,
  nimmt die Datenbank keine Schreibvorgänge mehr an und die Anwendung steht. Dieser Alarm ist
  keine Option.
- Zielwert für die Wiederherstellung in beiden Stufen: **innerhalb von 4 Stunden**. Die Werte
  werden je Kunde vertraglich bestätigt.
### 6.2 Weitere Regeln

- **Sicherung vor jeder Migration** ist Pflicht (`_data.md` §11).
- **Die Dokumentenablage (`_data.md` §9.4) wird mit der Datenbank zusammen gesichert und
  zusammen geprobt.** Eine Wiederherstellung, nach der Metadaten auf fehlende Dateien
  verweisen, gilt als gescheitert.
- Sicherungen werden **verschlüsselt** und **getrennt vom Server** abgelegt.
- **Die Wiederherstellung wird geprobt** — bei der Erstinstallation und danach mindestens
  jährlich, mit Protokoll. Eine nie geprobte Sicherung ist eine Vermutung, keine Sicherung.
- Der Wiederanlauf nach einem Ausfall ist als Ablauf dokumentiert: Reihenfolge der Dienste,
  Prüfungen, Umgang mit unvollständig verarbeiteten Eingängen und offenen Abgleichen von
  Feld-Clients.

---

## 7. Lasttest und Kapazität

Der Umfang richtet sich nach der Art der Version:

| Anlass | Prüfung |
|---|---|
| Schemaänderung, Eingriff in häufig genutzte Abläufe, neues Modul | **Voller Lasttest** mit der im Profil festgelegten Sitzungszahl gegen die Budgets aus `_architecture.md` §9 |
| Fehlerbehebung, neue Auswertung, abgegrenzte Änderung | **Verkürzter Durchlauf** auf den Hauptabläufen, deutlich kleinere Sitzungszahl |

- Wird beim **vollen** Lasttest ein Budget verletzt, wird die Version nicht freigegeben.
- Zeigt der **verkürzte** Durchlauf eine Verschlechterung gegenüber der Vorversion, wird auf
  den vollen Lasttest hochgestuft.
- Gemessen werden Antwortzeiten, Speicher je Sitzung, Datenbankverbindungen und Fehlerrate
  unter Last.
- Im Betrieb wird der **Trend** beobachtet: Zahl der Sitzungen, Datenbankgröße, Wachstum der
  größten Tabellen.
- Für jede Installation gibt es eine **Wachstumsprognose** und, sobald sie greift, eine
  Archivierungsstrategie für Altdaten.

---

## 8. Betriebsdokumentation

Je Installation dokumentiert und aktuell gehalten: **Betriebsmodell** (beim Kunden oder von
uns betrieben, §4.4), Umgebung und Systemvoraussetzungen, Konfigurationsgrößen, Anbindung an
den Identity-Provider, aktive Schalter, Anwendungs- und Plattformversion, Sicherungsverfahren
mit letztem Wiederherstellungstest, Wartungsfenster und Ansprechpartner. Nutzt die
Installation die rechtssichere Archivierung, gehört die **Verfahrensdokumentation** dazu
(`_data.md` §9.4).

Form und Ablage regelt `_documentation.md`. Zu jeder Version gehört eine
aktualisierte Betriebsanleitung.

---

## 9. Verbotsliste

1. Unstrukturierte Protokolleinträge ohne Korrelations-ID.
2. Passwörter, Token, Beleginhalte oder Daten der höchsten Schutzklasse im Protokoll.
3. Betriebsprotokoll als Ersatz für das Audit-Protokoll — oder umgekehrt.
4. Technische Fehlermeldung oder Stapelabbild in der Oberfläche.
5. Abgefangener Fehler ohne Protokolleintrag.
6. Übermittlung von Protokollen oder Kennzahlen nach außen ohne Vereinbarung mit dem Kunden
   — oder Übermittlung von Inhalten, die nach §1.3 ausgeschlossen sind.
7. Geheimnisse im Abbild, in der Konfigurationsdatei im Repository oder im Protokoll.
8. Start der Anwendung mit fehlender Pflichtkonfiguration.
9. Migration in Produktion ohne vorherige, auf Lesbarkeit geprüfte Sicherung.
10. Verändernde Migration ohne dokumentierten Rückkehrweg.
11. Freigabe einer Version ohne gemessene Migrationslaufzeit oder ohne die nach §7 fällige
    Lastprüfung.
12. Abgewählte Transaktionsprotokoll-Archivierung ohne schriftlich festgehaltene Folge — oder
    aktive Archivierung ohne Alarm auf das Archivziel.
13. Rollout auf alle Kundeninstallationen ohne vorherige Pilotinstallation.
14. Mehr als zehn gleichzeitig aktive Release-Schalter oder ein Schalter ohne Verfallsdatum.
15. Modulumfang einer Installation als Release-Schalter abgebildet.
16. Sicherung, deren Wiederherstellung nie geprobt wurde.
17. Mehrere Kundeninstallationen in einem gemeinsamen Container-Verbund oder auf einer
    gemeinsamen Datenbankinstanz.
18. Von uns betriebene Installation ohne Ressourcengrenzen oder ohne erweiterte
    Sicherungsstufe.

---

## Verweise

- Übergeordnet, Budgets: `_architecture.md`
- Migrationen, Sperren, Laufzeit: `_data.md` §11
- Geheimnisse, Audit-Umfang, Datenschutz: `_security.md`
- Outbox, Auftragseingang, Offline-Abgleich: `_integration.md`
- Kosten und Protokollierung der KI-Aufrufe: `_ai.md`
- Form der Betriebsdokumentation: `_documentation.md`
