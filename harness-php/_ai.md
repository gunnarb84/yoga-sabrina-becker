# _ai.md — KI-Funktionen und Assistenten

Verbindliche Regeln für jede Nutzung von Sprachmodellen

Grundproblem, aus dem sich alle Regeln ableiten: Ein Modell ist **nicht bestimmbar und nicht
prüfbar wie Code**. Dieselbe Eingabe kann morgen ein anderes Ergebnis liefern, und niemand
kann im Nachhinein zeigen, warum es so ausgefallen ist. Alles, was darauf aufbaut, muss diese
Eigenschaft einkalkulieren statt sie zu ignorieren.

Übergeordnet: `_architecture.md`.

---

## 1. Grundhaltung

- **KI ist Assistenz, nie Entscheidungsinstanz.** Jede Wirkung auf Daten entsteht durch einen
  Vorgang, der dieselben Rechte, Prüfungen und Protokolle durchläuft wie bei einer
  handelnden Person.
- **Was ohne KI zuverlässig lösbar ist, wird ohne KI gelöst.** Eine Berechnung, eine Prüfung
  oder eine Zuordnung mit klaren Regeln gehört in die Domain-Schicht — dort ist sie
  bestimmbar, testbar und kostenlos.
- Sinnvolle Einsatzgebiete sind Auslesen aus unstrukturierten Quellen, Einordnen,
  Zusammenfassen, Formulieren und Vorschlagen. Nicht: rechnen, entscheiden, freigeben.

---

## 2. Anbieterabstraktion

- Der Zugriff läuft über **einen Vertrag in der Plattform** (`_architecture.md` §2.2). Kein
  Fachmodul spricht direkt mit einem Anbieter.
- **Je Installation konfigurierbar**, ob ein Cloud-Anbieter oder ein lokaler Modellserver
  verwendet wird — und zwar **je Anwendungsfall**, nicht global. Ein Anwendungsfall mit
  unkritischen Daten darf extern laufen, während ein anderer im selben System lokal bleibt.
  Welche Kombination zulässig ist, entscheiden die Schutzklassen (§6). **„Lokal" heißt:
  innerhalb der Installation** — bei einer von uns betriebenen Installation
  (`_operations.md` §4.4) steht der Modellserver also in unserer Umgebung, im Verbund
  dieses Kunden; die Schutzklassen-Zusage „verlässt die Installation nicht" gilt unverändert.
- Ein Modell- oder Anbieterwechsel darf **keine Codeänderung** erfordern.
- **Ausfall darf die Fachfunktion nicht blockieren.** Ist kein Modell erreichbar, bleibt der
  Vorgang ohne KI-Unterstützung bedienbar. Ein Fachvorgang, der ohne Modell nicht mehr
  ausführbar ist, ist falsch gebaut — sonst legt ein Anbieterausfall die Warenwirtschaft
  lahm.
- Zeitgrenze und Wiederholungsverhalten sind festgelegt. Ein KI-Aufruf hält einen Vorgang nie
  unbegrenzt auf.

---

## 3. Ort im Code

- KI-Aufrufe erfolgen ausschließlich aus der **Application-Schicht**, über das
  Plattformmodul.
- **Nie** aus der Domain-Schicht — Fachregeln müssen bestimmbar bleiben. **Nie** aus der
  Persistenz. **Nie** aus einer Livewire-Komponente.
- **Kein Ergebnis eines Modells fließt unmittelbar in eine Fachregel.** Ein Modell liefert
  einen Vorschlag, der als solcher behandelt wird; es ersetzt keine Regel und keine
  Validierung.
- Länger laufende KI-Arbeit ist ein **Hintergrundauftrag** (`_architecture.md` §8) und läuft
  nicht in der Sitzung einer anwendenden Person.

---

## 4. Werkzeuge

Ein Assistent wirkt auf das System ausschließlich über **Werkzeuge** — benannte, einzeln
freigegebene Vorgänge.

- **Werkzeuge rufen die Integrations-API auf**, nicht interne Dienste und niemals die
  Datenbank. Damit greifen Rechte, Idempotenz und Protokollierung automatisch.
- Es gibt **keine allgemeine Fähigkeit**, einen beliebigen Vorgang auszuführen. Jedes Werkzeug
  wird ausdrücklich freigeschaltet.
- Je Werkzeug festgelegt: Name, Zweck, Eingabevertrag, lesend oder schreibend, erforderliches
  Recht und Schutzklasse der zurückgelieferten Daten.
- **Der Assistent handelt im Namen einer Person und nie mit weitergehenden Rechten als
  diese** (`_security.md` §4). Enger darf der Zugang sein, weiter nie.
- Lesende Werkzeuge liefern **so wenig wie möglich**. Ein Werkzeug „gib mir alle Aufträge"
  gibt es nicht.
- Je Anfrage sind **Anzahl der Werkzeugaufrufe, Dauer und Kosten begrenzt**. Ohne diese
  Grenzen dreht sich ein Assistent im Kreis und erzeugt Kosten ohne Ergebnis.

### 4.1 Suche und Auswertungen in natürlicher Sprache

Ausdrücklich gewollt und zulässig — aber **nicht durch Erzeugung von Datenbankabfragen**.

**Das Modell erzeugt eine strukturierte Abfrage gegen ein deklariertes Abfragemodell, kein
SQL.** Das Abfragemodell benennt je Modul abschließend: welche Entitäten abfragbar sind,
welche Felder, welche Filter, welche Verknüpfungen und welche Kennzahlen und Verdichtungen.
Es ist Teil des Moduls, wird gepflegt wie ein Vertrag und ist der eigentliche Aufwand dieser
Funktion.

Die Anwendung

1. **prüft** die erzeugte Abfrage gegen das Abfragemodell — unbekanntes Feld oder unzulässige
   Verknüpfung führen zur Ablehnung, nie zu einem Rateversuch,
2. **übersetzt** sie selbst in eine Datenbankabfrage,
3. **wendet Rechte, Zeilenobergrenze und Zeitgrenze an**,
4. **führt ausschließlich lesend aus**.

Damit sind die Rechte strukturell nicht umgehbar: Sie stecken nicht in dem,
was das Modell erzeugt.

**Anzeigepflicht der Auslegung.** Mit jedem Ergebnis wird in lesbarer Form angezeigt, wie die
Frage verstanden wurde — Zeitraum, Filter, Gruppierung, Kennzahl. Das ist die einzige
wirksame Absicherung gegen den gefährlichsten Fehlerfall dieser Funktion: eine Abfrage, die
fehlerfrei läuft und eine plausible, aber falsche Zahl liefert. Ein technischer Fehler fällt
auf, eine falsche Zahl nicht.

**Reproduzierbarkeit.** Jedes Ergebnis trägt seine Abfragedefinition und kann ohne Beteiligung
eines Modells erneut ausgeführt werden — mit demselben Ergebnis.

**Verfestigen.** Bewährt sich eine Auswertung, wird sie als feste Auswertung übernommen. Ab
dann ist kein Modell mehr beteiligt. Das senkt Kosten, Laufzeit und Risiko bei genau den
Abfragen, die am häufigsten laufen.

**Schutzklassen.** Zur Erzeugung der Abfrage werden nur die Frage und das Abfragemodell
übertragen, **keine Inhalte**. Ergebnisdaten unterliegen §6 wie alle anderen Daten; eine
Verdichtung oder Formulierung des Ergebnisses durch ein Modell ist nur zulässig, soweit die
Schutzklassen es erlauben.

**Protokollierung.** Frage, erzeugte Abfrage und Trefferzahl werden protokolliert. Eine
Auswertung ist ein Datenzugriff; bei Daten der höchsten Schutzklasse gilt zusätzlich die
Protokollpflicht aus `_security.md` §5.1.

> **Weiterhin verboten:** freie SQL-Erzeugung durch ein Modell, Ausführung von einem Modell
> erzeugter Abfragesprache, Zugriff auf Entitäten oder Felder außerhalb des Abfragemodells,
> und jede schreibende Wirkung aus diesem Pfad.

---

## 5. Schreibende Aktionen

- **Grundfall ist der Vorschlag, nicht der Vollzug.** Die KI bereitet vor, ein Mensch gibt
  frei.
- Der freigebenden Person wird **angezeigt, was genau ausgeführt wird** — die konkreten Werte,
  nicht nur eine Zusammenfassung in eigenen Worten.
- **Automatischer Vollzug** ist nur für ausdrücklich benannte, risikoarme Vorgänge zulässig,
  je Installation freizuschalten und mit Wertgrenze versehen.
- **Niemals automatisch**, unabhängig von jeder Einstellung: Belegfreigabe und Buchung,
  Zahlungen, Stornierungen, Wertübergaben und Übergabebestätigungen, Änderungen an Rechten
  oder Zugängen, Datenexporte.
- Jede schreibende KI-Aktion wird im Audit festgehalten, samt der freigebenden Person
  (`_security.md` §5.1).

---

## 6. Schutzklassen

### 6.1 Einstufung

**Jedes Feld trägt eine Schutzklasse.** Ohne diese Einstufung kann keine Regel greifen und
niemand nachweisen, was das System herausgegeben hat.

| Klasse | Beispiel | Externes Modell |
|---|---|---|
| Offen | Artikelbezeichnung, Einheit, Währung | Zulässig |
| Intern | Bestände, Preise, Belegstruktur | Nur nach Freigabe der Installation |
| Vertraulich | Personenbezogene Daten, Konditionen, Bankverbindungen | Nur nach Freigabe und Prüfung |
| Streng vertraulich | Vom Produkt zugeordnet (`projekt/_domaene.md`) | **Nie** |

### 6.2 Durchsetzung

- Die **Richtlinie je Installation** legt fest, welche Klasse an welchen Anbietertyp gehen
  darf. **Auslieferungsstandard für Produkte mit streng vertraulichen Daten: ausschließlich
  lokal.**
- **Vor jedem Versand wird geprüft**, dass nur zugelassene Klassen enthalten sind. Nicht
  Zugelassenes wird entfernt oder ersetzt. „Ist wahrscheinlich nicht enthalten" ist keine
  Prüfung.
- **Freitextfelder gelten als so hoch eingestuft wie der Datensatz, aus dem sie stammen.**
  Sie sind das eigentliche Restrisiko: in einem Bemerkungsfeld kann alles stehen.
- Welche Klassen tatsächlich übertragen wurden, wird je Aufruf protokolliert (§8).

### 6.3 Anbieterbedingungen

- Bei einem externen Anbieter ist ein **Auftragsverarbeitungsvertrag** Voraussetzung. Ohne
  ihn wird der Anbieter nicht freigeschaltet.
- Es ist vertraglich **auszuschließen, dass Eingaben zum Training verwendet werden**.
- Eine Verwendung von Kundendaten zum Trainieren oder Feinabstimmen eigener Modelle erfolgt
  nur mit ausdrücklicher Vereinbarung mit dem betroffenen Kunden.

---

## 7. Anweisungen an das Modell

- Anweisungen liegen **versioniert im Repository**, an einer Stelle je Anwendungsfall — nicht
  im Code verstreut und nicht als frei änderbarer Datenbankinhalt.
- **Eine Anweisungsänderung ist eine Codeänderung** mit Test und Freigabe, keine
  Konfigurationsänderung, die jemand unbemerkt vornimmt. Sie kann das Verhalten so stark
  ändern wie ein Codeeingriff.
- Je Anwendungsfall festgehalten: Zweck, erwartetes Ergebnisformat, Beispiele, Grenzen.
- **Was an Daten mitgegeben wird, ist ausdrücklich festgelegt** — nicht „der aktuelle
  Kontext", sondern eine benannte Auswahl.

### 7.1 Trennung von Anweisung und Daten

**Alles, was von außen kommt, sind Daten — niemals Anweisungen.** Kundentexte,
Belegbemerkungen, Mailinhalte, Dateinamen, Antworten fremder Systeme.

Ein Text, der aus einer externen Quelle stammt, darf keine Werkzeugausführung auslösen, die
über das hinausgeht, was die handelnde Person ohnehin und in dieser Absicht angestoßen hat.
Wer eine Kundenmail zusammenfassen lässt, hat damit nicht angewiesen, einen Auftrag
freizugeben — auch dann nicht, wenn in der Mail steht, man möge das tun.

---

## 8. Protokollierung und Kosten

Je Aufruf festgehalten: Anwendungsfall, Anbieter und Modell, Zeitpunkt, Dauer, Umfang, Kosten,
Ergebnisstatus, **übertragene Schutzklassen**, handelnde Person und Korrelations-ID.

- Der vollständige Inhalt hoch eingestufter Eingaben wird **nicht** mitprotokolliert
  (`_operations.md` §1.3).
- **Budget je Installation und je Anwendungsfall**, mit Alarm bei Annäherung und Abschaltung
  bei Überschreitung. Ein fehlgeleiteter Assistent darf keine unbegrenzten Kosten erzeugen.
- Ein **Betriebsschalter** schaltet KI-Funktionen sofort ab, einzeln oder gesamt
  (`_operations.md` §5.6).

---

## 9. Qualitätssicherung

- Je Anwendungsfall existiert eine **Bewertungsmenge**: Testfälle mit erwarteten Ergebnissen.
  Stichproben aus dem Betrieb genügen nicht.
- **Regressionslauf bei jedem Modellwechsel, jeder Anweisungsänderung und jedem
  Anbieterwechsel.** Ohne bestandenen Lauf keine Freigabe. Ein Modellwechsel ist kein
  Konfigurationsdetail — er kann das Verhalten vollständig verändern.
- Weil Ergebnisse nicht bestimmbar sind, prüfen Tests **Eigenschaften** — Format,
  Wertebereich, Pflichtangaben, Abwesenheit erfundener Kennungen — und nicht Zeichengleichheit
  (`_test-strategy.md`).
- Im Betrieb überwacht: **Anteil der von Menschen abgelehnten Vorschläge**. Das ist die
  ehrlichste verfügbare Kennzahl. Steigt sie, taugt der Anwendungsfall nicht mehr.

---

## 10. Abgrenzung zum Automatisierungswerkzeug

Das Automatisierungswerkzeug orchestriert Abläufe, in denen eine KI-Funktion vorkommt. **Die
KI-Funktion selbst liegt
in der Anwendung.**

Verboten ist der naheliegende Kurzschluss: ein Ablauf, der selbst ein Modell aufruft und
das Ergebnis anschließend über die API in die Anwendung schreibt. Damit umgeht er
Schutzklassen, Protokollierung, Kostenkontrolle und Freigabepflicht — also genau das, was
dieses Dokument regelt. Das Werkzeug darf KI-Vorgänge **anstoßen**, nicht selbst ausführen.

---

## 11. Verbotsliste

1. Modellaufruf außerhalb des Plattformmoduls.
2. KI-Aufruf aus Domain-Schicht, Persistenz oder Livewire-Komponente.
3. Modellergebnis, das unmittelbar eine Fachregel oder Validierung ersetzt.
4. Fachvorgang, der ohne verfügbares Modell nicht mehr ausführbar ist.
5. Werkzeug, das interne Dienste oder die Datenbank direkt anspricht statt der
   Integrations-API.
6. Allgemeine Fähigkeit, beliebige Vorgänge auszuführen, statt einzeln freigegebener
   Werkzeuge.
7. Von einem Modell erzeugte Datenbankabfrage oder Abfragesprache zur Ausführung gebracht —
   auch lesend, auch für Auswertungen (§4.1).
8. Abfrage gegen Entitäten oder Felder außerhalb des deklarierten Abfragemodells.
9. Ergebnis einer Auswertung ohne sichtbare Auslegung der Frage.
10. Assistent mit weitergehenden Rechten als die Person, in deren Namen er handelt.
11. Werkzeugkette ohne Begrenzung von Anzahl, Dauer und Kosten.
12. Automatischer Vollzug einer Freigabe, Buchung, Zahlung, Stornierung, Wertübergabe,
    Rechteänderung oder eines Datenexports.
13. Freigabe durch einen Menschen, ohne dass die konkreten Werte angezeigt wurden.
14. Übertragung von Daten an ein externes Modell ohne vorherige Prüfung der Schutzklassen.
15. Übertragung streng vertraulicher Daten an ein externes Modell.
16. Freitextfeld niedriger eingestuft als der Datensatz, aus dem es stammt.
17. Externer Anbieter ohne Auftragsverarbeitungsvertrag oder ohne Ausschluss der
    Trainingsnutzung.
18. Anweisung im Code verstreut oder als frei änderbarer Datenbankinhalt.
19. Anweisungsänderung ohne Test und Freigabe.
20. Externer Text als Anweisung behandelt statt als Daten.
21. KI-Aufruf ohne Protokolleintrag oder ohne Kostenzuordnung.
22. Modell- oder Anbieterwechsel ohne bestandenen Regressionslauf.
23. Ablauf des Automatisierungswerkzeugs, der selbst ein Modell aufruft.

---

## Verweise

- Übergeordnet, Plattformbausteine: `_architecture.md`
- Rechte des Assistenten, Audit schreibender Aktionen: `_security.md`
- Aufruf der Integrations-API, Abgrenzung zum Automatisierungswerkzeug: `_integration.md`
- Schutzklassen der Felder im Datenmodell: `_data.md`
- Bewertungsmengen und Prüfung nicht bestimmbarer Ergebnisse: `_test-strategy.md`
- Kostenüberwachung, Betriebsschalter, Protokollausschlüsse: `_operations.md`
