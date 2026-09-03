# _security.md — Anmeldung, Rechte, Nachweisbarkeit

Verbindliche Regeln dafür, **wer was darf**, **wie das durchgesetzt wird** und **was
nachweisbar bleibt**.

Zwei Randbedingungen prägen dieses Dokument: Die Anwendung läuft **im Haus des Kunden**, also
in fremder Infrastruktur mit fremder Benutzerverwaltung. Und ein Produkt kann Daten
verarbeiten, deren Offenlegung nicht nur einen Datenschutzvorfall bedeutet, sondern Menschen
gefährden kann — welche Daten das sind, benennt `projekt/_domaene.md` als **höchste
Schutzklasse**.

Übergeordnet: `_architecture.md`.

---

## 1. Anmeldung von Anwendern

Die Anwendung bietet **zwei gleichwertige Anmeldewege**: OpenID Connect gegen einen
Identity-Provider (§1.1) und die **eigene lokale Benutzerverwaltung** (§1.2). Welche Wege
aktiv sind — einer allein oder beide nebeneinander —, wird je Installation festgelegt
(`_operations.md` §4.3). Die lokale Anmeldung ist ein vollwertiger, eigenständiger Weg,
keine bloße Rückfallebene: **keine Installation ist von einem externen Identity-Provider
abhängig.**

### 1.1 Identity-Provider

- Anmeldung über **OpenID Connect** gegen den Identity-Provider des Kunden. Anbieteroffen —
  Entra ID, AD FS, Keycloak oder jeder andere standardkonforme Anbieter.
- Für Anwender dieses Wegs verwaltet und speichert die Anwendung **keine Kennwörter**.
- **Mehrfaktor-Anmeldung wird vom Identity-Provider erwartet** und nicht nachgebaut.
- Bei von uns betriebenen Installationen (`_operations.md` §4.4) kann der Identity-Provider
  **von uns gestellt** werden — dann mit einem **eigenen Mandanten je Kunde**, nie mit einem
  gemeinsamen Benutzerbestand über Kunden hinweg.

### 1.2 Lokale Benutzerverwaltung

Benutzer und Kennwörter verwaltet die **Anwendung selbst** — ohne jede Abhängigkeit von
einem externen Dienst, auch nicht für den zweiten Faktor. Anlage, Sperrung, Kennwort- und
Faktor-Rücksetzung sind Verwaltungsvorgänge der Anwendung und protokollpflichtig (§5.1).

- Kennwortanforderungen und Sperrung nach wiederholten Fehlversuchen sind Pflicht.
- Kennwörter werden ausschließlich mit einem für diesen Zweck vorgesehenen Verfahren
  gespeichert, niemals verschlüsselt oder gar im Klartext.
- **Der zweite Faktor ist Bestandteil der lokalen Anmeldung** und wird von der Anwendung
  selbst bereitgestellt: zeitbasierte Einmalcodes nach offenem Standard (TOTP), einrichtbar
  mit jeder üblichen Authenticator-App. Bei der Einrichtung entstehen
  **Wiederherstellungscodes** für den Geräteverlust.
- Der zweite Faktor ist **Voreinstellung für jedes lokale Konto**. Je Installation
  abschaltbar ist er nur für Rollen ohne erhöhte Rechte; **für jede Rolle mit erhöhten
  Rechten bleibt er verpflichtend.**
- **Faktor- und Kennwortrücksetzung sind nie Selbstbedienung** über einen unbestätigten
  Kanal (E-Mail-Link), sondern ein protokollierter Verwaltungsvorgang durch eine dafür
  berechtigte Rolle.

### 1.3 Notfallzugang

Ein Kunde, dessen Verzeichnisdienst ausfällt, darf nicht ohne Warenwirtschaft dastehen.

- Mindestens **ein lokales Konto mit vollem Zugriff**, unabhängig vom Identity-Provider.
- Zugangsdaten werden beim Kunden **versiegelt hinterlegt**, die Herausgabe folgt dem
  Vier-Augen-Prinzip.
- **Jede Nutzung löst einen Alarm aus** und wird im Audit festgehalten.
- Nach jeder Nutzung wird das Kennwort gewechselt. Ein Notfallzugang ist kein Arbeitszugang.
- Die Funktionsfähigkeit wird **mindestens jährlich geprüft und protokolliert** — genau wie
  die Wiederherstellung einer Sicherung (`_operations.md` §6).

### 1.4 Sitzungen

- **Leerlaufabmeldung nach 30 Minuten** als Voreinstellung, je Installation konfigurierbar.
  Absolute Höchstdauer einer Sitzung: 12 Stunden.
- Hintergrundaktivität einer geöffneten Seite verlängert die Sitzung **nicht**. Nur echte
  Benutzerinteraktion tut das.
- **Sitzungen liegen serverseitig** (Session-Treiber Datenbank, `_architecture.md` §3) und
  sind vom Browser-Cookie losgelöst prüfbar: Wird eine Person im Identity-Provider
  deaktiviert oder werden ihre Rechte entzogen, darf sie nicht bis zum Feierabend
  weiterarbeiten. Gültigkeit und Rechte werden deshalb **spätestens alle 15 Minuten erneut
  geprüft** (Middleware vor jedem wirksamen Vorgang); fallen sie weg, wird die Sitzung
  beendet.
- Abmelden beendet die Sitzung serverseitig, nicht nur im Browser.
- **Mehrere gleichzeitige Sitzungen derselben Person sind zulässig.** Zwei Fenster
  nebeneinander sind der vorgesehene Weg für zwei Arbeitsgänge gleichzeitig
  (`_uiux.md` §3a); an mehreren Bildschirmen ist das Normalbetrieb. Jede Sitzung zählt
  einzeln gegen die Budgets (`_architecture.md` §9), und der Leerlauf läuft je Sitzung.
- **Keine Bindung an Browser, Gerät, Adresse oder Standort und keine Fernabmeldung.** Eine
  Anmeldung beendet nie eine laufende Sitzung derselben Person und überträgt keine. Drei
  Gründe, damit das nicht später „aus Sicherheitsgründen" umgedreht wird: „derselbe
  Browser" ist nur ein Cookie — zweites Profil, privates Fenster, zwei Browser auf einem
  Rechner, Terminalserver, Feld-Gerät neben dem Schreibtischrechner sehen alle aus wie ein
  anderer; eine Fernabmeldung verwirft ungespeicherte Arbeit aus der Ferne, was
  `_uiux.md` §1.8 und §4a ausschließen; und eine Sitzung lässt sich auf einem fremden Gerät
  nicht übernehmen — sie hängt an ihrer serverseitigen Sitzungskennung, nicht an einer
  Browser-Bindung.
- **Obergrenze je Konto** statt Bindung: `[budgets] sitzungenJeKonto`, Vorgabe 3. Ist sie
  erreicht, wird die **neue** Sitzung mit klarer Meldung abgewiesen; die laufenden bleiben
  unberührt. Der Deckel richtet sich gegen Kontoteilung (§1.5), nicht gegen den Anwender —
  **1 ist kein zulässiger Wert**, er verbietet den Weg aus `_uiux.md` §3a.

### 1.5 Lizenzgrenze und Kontenteilung

Lizenzen sind an den Bediener gebunden. Die Grenze zählt deshalb **aktive Benutzerkonten**
je Installation — nicht Sitzungen, nicht Fenster, nicht Geräte.

- **Ein Konto gehört genau einer Person.** Sammelkonten für eine Abteilung, eine Schicht
  oder ein Lager sind unzulässig; der Notfallzugang (§1.3) ist die geregelte Ausnahme und
  kein Arbeitszugang.
- **Durchsetzung beim Anlegen und Entsperren, nicht beim Anmelden.** Ist die Grenze
  erreicht, entsteht kein weiteres aktives Konto, bis ein anderes gesperrt wird. Niemand
  wird mitten in der Arbeit ausgesperrt, und die Grenze ist an genau einer Stelle
  wirksam — dort, wo sie auch protokolliert wird (§1.2).
- **Die Zahl lizenzierter Konten ist Pflichtangabe der Installationskonfiguration**
  (`_operations.md` §4.2): sie kommt aus einer signierten Lizenzangabe, deren Signatur beim
  Start geprüft wird. Fehlt sie oder trägt sie keine gültige Signatur, startet die
  Anwendung nicht. **Keine Online-Aktivierung, kein Rückkanal** — die Anwendung läuft in
  Netzen ohne Internetzugang (`_uiux.md` §9, `_operations.md` §2.4).
- **Nachweis statt Sperre.** Dass mehrere Personen ein Konto teilen, wird nicht technisch
  verhindert, sondern belegbar: zweiter Faktor je lokalem Konto (§1.2), Audit je Vorgang
  mit Konto (§5.1), Sitzungsdeckel je Konto (§1.4) und die Kennzahl „lizenzierte gegen
  aktive Konten" samt Höchstwert gleichzeitiger Sitzungen je Konto (`_operations.md` §2.2).
  In einer Installation im Haus des Kunden ist „unmöglich" nicht erreichbar — „belegbar"
  ist es, und ein belegter Vertragsverstoß ist der wirksame Hebel.
- Ausdrücklich **nicht** Bestandteil: Bindung an Browser, Gerät, Adresse oder Standort;
  Fernabmeldung; Sperre eines Kontos wegen paralleler Nutzung. Begründung in §1.4.

---

## 2. Rechtemodell

### 2.1 Aufbau

- Ein **Recht** ist die kleinste Einheit und benennt Modul und Vorgang. Es entsteht
  **zusammen mit dem Vorgang**, nicht nachträglich. Ein Vorgang ohne zugehöriges Recht ist
  unvollständig.
- Eine **Rolle** bündelt Rechte und wird in der Anwendung gepflegt (die „Gruppe" der
  Bediener — beides meint dasselbe).
- **Eine Person erhält Rechte ausschließlich über Rollen** — nie als Einzelrecht direkt an
  der Person. Braucht jemand einen besonderen Zuschnitt, entsteht dafür eine **eigene,
  benannte Rolle**, auch wenn nur diese eine Person sie hat. So bleibt jede Berechtigung
  benennbar, prüfbar und als Ganzes entziehbar; Einzelrechte an Personen wuchern unbemerkt
  und überleben jeden Personalwechsel. Das Datenmodell sieht eine direkte
  Person-Recht-Zuweisung erst gar nicht vor.
- Die Zuweisung gilt **für die Installation**. Arbeitet eine Person in mehreren Firmen
  desselben Kunden, wird sie in jeder Installation eigenständig berechtigt (§3) — dieselbe
  Person kann so in Firma A bearbeiten und in Firma B nur lesen.
- Gruppen aus dem Identity-Provider dürfen auf Rollen abgebildet werden. **Die Rechtehoheit
  bleibt in jedem Fall in der Anwendung** — ein ERP hat hunderte feingranulare Rechte, die
  kein Verzeichnisadministrator pflegen kann.

### 2.2 Durchsetzung

- **Jeder Vorgang prüft die Berechtigung in der Application-Schicht** — auch dann, wenn die
  Oberfläche den Knopf ohnehin ausgeblendet hat (`_architecture.md` §4.1).
- **Verweigern ist der Grundzustand.** Kein Recht bedeutet keine Ausführung. Es gibt kein
  stillschweigendes Erlauben und keine Sonderbehandlung im Code für „Administratoren" —
  Vollzugriff entsteht durch Bündelung von Rechten, nicht durch eine Ausnahme.
- **Datenbezogene Einschränkungen** — nur eigene Belege, nur der eigene Arbeitsvorrat — sind
  Teil des Vorgangs und nicht der Rolle.

> **Nicht Bestandteil dieses Vertrags:** Ein Vier-Augen-Prinzip für kritische Vorgänge
> (Freigaben über Wertgrenzen, Bankverbindungen, Storno, Rechtevergabe) ist bewusst **nicht**
> festgeschrieben. Sollte es später gefordert werden, ist es keine nachträgliche Kleinigkeit,
> sondern greift in Vorgangsablauf, Statusmodell und Audit ein — dann über eine ADR.

---

## 3. Zugriff bei mehreren Firmen eines Kunden

Je Installation gibt es genau **eine Firma** (`_data.md` §5). Eine „aktive Firma", ein
Firmenwechsel oder ein Firmenfilter existieren nicht — die Firmentrennung ist die
Installationstrennung selbst.

- Zugriff auf eine Firma bedeutet ein Konto **in deren Installation**. Arbeitet eine Person
  in mehreren Firmen, wird sie je Installation eigenständig angelegt und berechtigt.
- **Keine Anfrage trägt eine Firmenkennung.** Es gibt nur den einen Datenbestand der
  Installation; welche Firma das ist, entscheidet die Adresse der aufgerufenen
  Installation, nie ein Feld in der Anfrage.
- Außerhalb von Sitzung und maschinellem Zugang — Ereignisempfänger, Hintergrundaufträge,
  Plattformläufe — gilt der **Systemkontext** (`_architecture.md` §6.1): benannte
  Systemidentität, Rechteprüfung beim Einreihen.

---

## 4. Maschinelle Zugänge

Betrifft Kundensysteme, die Vorgänge anliefern, das Automatisierungswerkzeug, KI-Assistenten
und Feld-Geräte.

- **Eigene Identität je Zugang.** Kein geteiltes Konto, kein Sammelzugang für „alle Kunden".
- Authentifizierung über ein Maschinenverfahren (Client Credentials) oder einen Zugangs­
  schlüssel. **Schlüssel werden nur als Hashwert gespeichert** und genau einmal bei der
  Erstellung angezeigt.
- Jeder Zugang hat: Rechte wie ein Benutzer, Ablaufdatum,
  Durchsatzbegrenzung und, wo möglich, eine Einschränkung auf bekannte Absenderadressen.
  Er gehört zu genau einer Installation — und damit zu genau einer Firma (§3).
- **Sperrung wirkt sofort**, nicht erst beim Ablauf des nächsten Tokens.
- **KI-Assistenten handeln immer im Namen einer Person** und **nie mit mehr Rechten als
  diese**. Der Zugang darf enger gefasst sein, niemals weiter (`_ai.md`).
- Anlage und Rechteerweiterung eines maschinellen Zugangs unterliegen der ADR-Pflicht (§8).

---

## 5. Audit-Protokoll

### 5.1 Umfang

Protokollpflichtig sind mindestens:

- Anmeldung, fehlgeschlagene Anmeldung, Abmeldung,
- **jede Nutzung des Notfallzugangs**,
- Änderungen an Benutzern, Rollen, Rechten und maschinellen Zugängen — einschließlich
  Kennwort- und Faktor-Rücksetzungen lokaler Konten (§1.2),
- **Abweisungen an der Lizenzgrenze und am Sitzungsdeckel** (§1.4, §1.5) — sie sind die
  Spur, aus der eine Kontenteilung erkennbar wird,
- **jede schreibende Änderung an Stamm- und Konfigurationsdaten**, mit altem und neuem Wert
  (`_data.md` §9.1); versionierte Stammdaten (Preise, Steuersätze, Kurse) erfüllen das über
  ihre Versionen,
- Freigabe, Buchung und Stornierung von Belegen,
- **jede Änderung an einem gebuchten Beleg** über die Positivliste buchungsneutraler Felder
  (`_data.md` §7.3), mit altem und neuem Wert,
- Änderung von Bankverbindungen und Zahlungsdaten,
- **jeder Datenexport** — Buchhaltungsübergabe, Listen, Berichte, Massenabruf über die
  Integrations-API,
- **jede schreibende KI-Aktion** samt der Person, die sie freigegeben hat,
- Konfigurationsänderungen mit fachlicher Wirkung und das Schalten von Schaltern,
- **lesender Zugriff auf Daten der höchsten Schutzklasse** (Zuordnung in
  `projekt/_domaene.md`) — auch wenn er über die Verwendungsauskunft erfolgt
  (`_architecture.md` §6),
- bei von uns betriebenen Installationen: **jeder Zugriff des Betriebspersonals auf
  Kundendaten** (§6.4),
- Einsichtnahme in das Audit-Protokoll selbst.

### 5.2 Form

- Inhalt je Eintrag: wer, wann, welcher Vorgang, betroffene Entität, alter und neuer Wert,
  Korrelations-ID und **Herkunft** (Oberfläche, Integrations-API, Automatisierungswerkzeug,
KI, Feld-Gerät).
- **Nur Einfügen.** Änderungs- und Löschrechte sind auf Datenbankebene entzogen.
- Speicherort ist die Datenbank der Installation, in einer eigenen Tabelle je Art
  (`_data.md` §9.2), getrennt vom Betriebsprotokoll (`_operations.md` §1.4).

### 5.3 Lesezugriffe und Mengengerüst

Die Protokollierung **lesender** Zugriffe auf Daten der höchsten Schutzklasse (Zuordnung in
`projekt/_domaene.md`) ist die einzige Regel dieses Abschnitts, die nennenswertes
Datenvolumen erzeugt. Dafür gelten drei Einschränkungen:

- **Protokolliert wird der Zugriff, nicht der Datensatz.** Eine Liste mit 200 Treffern erzeugt
  **einen** Eintrag mit Filterkriterien und Trefferzahl, nicht 200. Zeilenweise
  Protokollierung ist ein Regelverstoß.
- **Getrennte Aufbewahrung.** Schreibende Einträge folgen den gesetzlichen Fristen; Einträge
  über Lesezugriffe werden nach **24 Monaten** entfernt. Sie sind eine Sicherheitsmaßnahme,
  keine Buchführung.
- Die Tabelle wird in die Wachstumsprognose der Installation aufgenommen
  (`_operations.md` §7).

---

## 6. Datenschutz und Aufbewahrung

### 6.1 Personenbezogene Daten

**Personenbezogen ist jede Angabe zu einer natürlichen Person — unabhängig davon, in welcher
Rolle sie im System auftritt.** Der Grundsatz steht vor der Aufzählung, weil eine Aufzählung
von Rollen immer eine vergisst und der Schutz dann genau dort fehlt.

Insbesondere:

- Mitarbeitende,
- Ansprechpartner bei Geschäftspartnern,
- **Geschäftspartner, die selbst natürliche Personen sind** — Einzelunternehmen und
  Privatkunden. Ihre Identitätsfelder (Anrede, Vor-, Nachname) liegen am Stammsatz und nicht an
  einem Kontakt daneben; schutzbedürftig sind sie genauso,
- Empfänger, Zeugen und sonstige Dritte, deren Daten ein Vorgang festhält,
- bei Produkten mit Außeneinsatz zusätzlich das eingesetzte Personal samt Unterschriften, Fotos
  und Standortdaten.

- Je Produkt wird festgehalten, **welche Felder personenbezogen sind**. Ohne diese Übersicht
  ist ein Auskunftsersuchen nicht beantwortbar.

### 6.2 Standortdaten Beschäftigter

Dieser Abschnitt gilt, sobald ein Produkt Standortdaten Beschäftigter erfasst; andernfalls
entfällt er. Ortung von Beschäftigten ist in aller Regel mitbestimmungspflichtig. **Vor Inbetriebnahme ist
je Kunde zu klären, ob eine Betriebsvereinbarung erforderlich ist.** Weil diese
Vereinbarungen von Kunde zu Kunde unterschiedlich ausfallen, muss die Software den Rahmen
abbilden können — das sind konkrete Anforderungen, keine organisatorischen Hinweise:

- **Umfang der Erfassung ist je Installation einstellbar:** *aus*, *nur bei fachlichen
  Ereignissen* (im Profil benannt) oder *fortlaufend*. Voreinstellung ist die ereignisbezogene
  Erfassung. Eine fortlaufende Aufzeichnung wird nur eingeschaltet, wo sie vereinbart ist.
- **Zweckbindung ist technisch durchgesetzt:** Es gibt keine Auswertung, die Standort- oder
  Zeitdaten je Person verdichtet. Kein Bericht, keine Kennzahl, keine Sortierung nach
  Person. Das ist eine Anforderung an das Datenmodell und an jede Auswertung, nicht nur
  eine Absichtserklärung.
- **Eigenes Recht für den Zugriff** auf Standortdaten, nicht in allgemeinen Rollen enthalten.
- **Kurze Aufbewahrung:** Standortdaten werden nach Abschluss und Abrechnung des Vorgangs
  zuzüglich einer je Installation festgelegten Reklamationsfrist gelöscht — nicht nach den
  Fristen der Belegdaten.
- **Transparenz:** Beschäftigte können die zur eigenen Person erfassten Daten einsehen.
- Jeder Zugriff darauf ist protokollpflichtig (§5.1).

### 6.3 Löschung gegen Aufbewahrung

- **Buchungsrelevante Daten werden nicht gelöscht**, sondern gesperrt und in der Verarbeitung
  eingeschränkt. Die handels- und steuerrechtlichen Fristen gehen einem Löschverlangen vor
  (`_data.md` §9.3).
- Nicht buchungsrelevante personenbezogene Daten werden nach Zweckfortfall gelöscht.
- Je Datenart existiert eine dokumentierte Frist. „Wir behalten alles" ist kein Löschkonzept.

### 6.4 Auftragsverarbeitung bei von uns betriebenen Installationen

Betreiben wir eine Installation als Dienst (`_operations.md` §4.4), sind wir
**Auftragsverarbeiter** für die Daten des Kunden. Daraus folgt:

- **Ein Auftragsverarbeitungsvertrag ist Voraussetzung der Produktivsetzung** — nicht ihre
  Nachbereitung.
- **Zugriffe unseres Betriebspersonals auf Kundendaten** erfolgen nur begründet — Störung,
  Wartung, ausdrücklicher Auftrag des Kunden — und sind **protokollpflichtig** (§5.1),
  einschließlich lesender Zugriffe.
- Kundendaten werden **ausschließlich zur Vertragserfüllung** verarbeitet — keine
  Auswertung über Kunden hinweg, keine Nutzung für eigene Zwecke ohne ausdrückliche
  Vereinbarung.
- Die **Trennung je Kunde** (Netz, Geheimnisse, Sicherungen, Datenbank) regelt
  `_operations.md` §4.4; sie ist die technische Seite dieser Zusage.

---

## 7. Feld-Client

Ein Gerät im Außeneinsatz kann verloren gehen oder entwendet werden. Führt es Daten hoher
Schutzklasse mit sich, ist das kein theoretischer Fall, sondern ein Angriffsweg.

- **Jedes Gerät ist eine eigene, registrierte Identität**, an eine Person gebunden, mit
  eigenem Zugang und eigenem Sperrstatus.
- **Lokal gespeicherte Daten sind verschlüsselt.**
- **Nur der benötigte Ausschnitt wird vorgehalten** — der aktuell zugeteilte Arbeitsvorrat,
  nicht der gesamte Bestand. Summen und Planungsdaten künftiger Einsätze liegen nicht auf dem
  Gerät.
- **Lokale Entsperrung** über PIN oder biometrisches Merkmal, mit **begrenzter Offline-
  Gültigkeit**: nach spätestens 24 Stunden ohne Verbindung ist eine erneute Anmeldung
  erforderlich, bis dahin ist kein Zugriff auf lokale Daten möglich.
- **Fernsperre und Fernlöschung** sind vorgesehen. Sie wirken beim nächsten Verbindungsaufbau;
  bis dahin schützt die ablaufende Offline-Gültigkeit.
- Erfasste Nachweise werden verschlüsselt abgelegt und **nach erfolgreichem Abgleich lokal
  gelöscht**.

---

## 8. Geheimnisse, Verschlüsselung, Änderungen

- **Geheimnisse liegen nie im Repository, nie im Abbild, nie im Protokoll.** Herkunft sind
  Umgebungsvariablen oder der Geheimnisspeicher des Betreibers der Installation — bei von
  uns betriebenen Installationen je Kunde getrennt (`_operations.md` §4.4).
- **Verschlüsselte Übertragung überall**, auch innerhalb des Kundennetzes.
- Sicherungen werden verschlüsselt und getrennt gelagert (`_operations.md` §6).
- Schlüssel und maschinelle Zugänge haben ein Ablaufdatum und werden gewechselt.
- Abhängigkeiten werden **bei jedem Bau** auf bekannte Schwachstellen geprüft
  (`composer audit` für Composer-Pakete; dasselbe für die NPM-Abhängigkeiten der Oberfläche);
  ein Fund mit hoher Einstufung blockiert die Freigabe.
- **ADR-Pflicht** bei: neuem maschinellem Zugang, Erweiterung der Rechte eines bestehenden
  Zugangs, Änderung am Rechtemodell und Anbindung eines neuen externen Dienstes.

---

## 9. Verbotsliste

1. Berechtigungsprüfung ausschließlich in der Oberfläche.
2. Vorgang ohne zugehöriges Recht.
3. Sonderbehandlung im Code, die Rechte umgeht — auch für Administratorrollen.
4. Firmenkennung in einer Anfrage, die bestimmen soll, in welchem Datenbestand gearbeitet
   wird — die Installation ist der Datenbestand (§3).
5. Geteiltes Konto für mehrere Kunden, Systeme oder Geräte.
6. Zugangsschlüssel im Klartext gespeichert oder nach der Erstellung erneut anzeigbar.
7. KI-Zugang mit weitergehenden Rechten als die Person, in deren Namen er handelt.
8. Kennwort verschlüsselt oder im Klartext gespeichert.
9. Weiterarbeit einer offenen Sitzung nach Entzug der Rechte oder Deaktivierung der Person.
10. Notfallzugang als Arbeitszugang, ohne Alarm oder ohne Kennwortwechsel nach Nutzung.
11. Fehlender Audit-Eintrag bei einem protokollpflichtigen Vorgang nach §5.1.
12. Lesezugriff je Datensatz protokolliert statt je Zugriff (§5.3).
13. Änderung oder Löschung im Audit-Protokoll.
14. Löschung buchungsrelevanter Daten aufgrund eines Löschverlangens.
15. Auswertung von Standortdaten Beschäftigter zur Leistungskontrolle.
16. Fortlaufende Standortaufzeichnung, wo die Installation auf ereignisbezogene Erfassung
    eingestellt ist.
17. Unverschlüsselte lokale Daten auf einem Feld-Gerät oder Vorhalten von Daten über den
    aktuell zugeteilten Arbeitsvorrat hinaus.
18. Geheimnis im Repository, im Abbild oder im Protokoll.
19. Freigabe einer Version mit bekannter, hoch eingestufter Schwachstelle in einer
    Abhängigkeit.
20. Produktivsetzung einer von uns betriebenen Installation ohne
    Auftragsverarbeitungsvertrag.
21. Zugriff des Betriebspersonals auf Kundendaten ohne Audit-Eintrag oder ohne Begründung.
22. Einzelrecht direkt an einer Person statt über eine Rolle (§2.1) — auch nicht als
    Datenmodell-Möglichkeit.
23. Lokales Konto einer Rolle mit erhöhten Rechten ohne zweiten Faktor (§1.2).
24. Kennwort- oder Faktor-Rücksetzung als Selbstbedienung über einen unbestätigten Kanal
    oder ohne Audit-Eintrag (§1.2).
25. Sammelkonto für mehrere Personen — eine Abteilung, eine Schicht, ein Lager (§1.5).
26. Bindung einer Sitzung an Browser, Gerät, Adresse oder Standort (§1.4).
27. Beenden oder „Übertragen" einer laufenden Sitzung, weil sich dieselbe Person erneut
    anmeldet (§1.4).
28. Aktives Konto über die lizenzierte Kontenzahl hinaus (§1.5).

---

## Verweise

- Übergeordnet: `_architecture.md`
- Pflichtspalten, Aufbewahrung: `_data.md`
- Maschinelle Zugänge der Integrations-API, Idempotenz, Offline-Abgleich: `_integration.md`
- Schutzklassen, Freigabe schreibender KI-Aktionen: `_ai.md`
- Betriebsprotokoll, Alarme, Sicherung, Lizenzangabe der Installation: `_operations.md`
- Anmeldemaske, Sitzungsmeldungen, mehrere Fenster derselben Anmeldung: `_uiux.md` §3a
