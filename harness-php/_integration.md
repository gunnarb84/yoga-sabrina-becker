# _integration.md — Integrations-API, Ereignisse, Automatisierungswerkzeug,
# Buchhaltungsübergabe, Offline-Abgleich

Verbindliche Regeln für alles, was die Anwendung mit der Außenwelt austauscht.

Leitgedanke: **Die Integrations-API ist ein Produktversprechen.** Sobald ein Kunde seine
Aufträge dagegen schickt, ist sie nicht mehr frei änderbar — anders als die eigene
Oberfläche, die jederzeit umgebaut werden kann. Genau deshalb ist sie von der Oberfläche
getrennt (`_architecture.md` §3).

Übergeordnet: `_architecture.md`.

---

## 1. Geltungsbereich

- Die Integrations-API ist die **einzige Tür von außen**. Kundensysteme, das
  Automatisierungswerkzeug, KI-Assistenten
  und Feld-Clients nutzen ausschließlich sie. Ein direkter Datenbankzugriff von außen ist
  ausnahmslos verboten (`_architecture.md` §3).
- Sie ist **nicht** die Schnittstelle der eigenen Oberfläche. Livewire ruft die
  Application-Schicht direkt auf; die API wird davon nicht mitgezogen.
- Jeder Zugang hat eine eigene Identität und eigene Rechte (`_security.md` §4).
- **Auch eine andere Installation desselben Kunden ist ein externes System.** Abgleiche und
  Geschäfte zwischen den Firmen eines Kunden (je Firma eine Installation, `_data.md` §5)
  laufen ausschließlich über diese API und werden **je Fall als Spec definiert**
  (`_requirements.md`): welche Daten, welche Richtung, welcher Auslöser, welche
  Fehlerbehandlung. Jede Installation bucht ihre eigenen Belege; die Zuordnung läuft über
  externe Kennungen (§6).

---

## 2. Versionierung und Kompatibilität

- Version im Pfad: `/api/v1/…`.
- **Innerhalb einer Hauptversion sind nur abwärtskompatible Änderungen erlaubt.**

| Erlaubt | Verboten |
|---|---|
| Neues optionales Feld in der Anfrage | Feld entfernen oder umbenennen |
| Neues Feld in der Antwort | Typ oder Bedeutung eines Feldes ändern |
| Neuer Endpunkt | Pflichtfeld hinzufügen |
| Neuer optionaler Filter | Statuscode oder Fehlercode ändern |
| Neuer Aufzählungswert, sofern dokumentiert ist, dass Verbraucher unbekannte Werte tolerieren müssen | Vorbelegung ändern |

- Eine neue Hauptversion entsteht nur, wenn es unvermeidlich ist. Dann laufen beide Versionen
  parallel.
- **Abkündigungsfrist: mindestens 12 Monate** ab Ankündigung. Während dieser Zeit weist die
  alte Version einen Hinweis in der Antwort aus.
- **Es ist jederzeit nachweisbar, welcher Zugang noch welche Version nutzt.** Ohne diese
  Auswertung wird keine Version abgeschaltet.
- Eine brechende Änderung erfordert eine ADR (`_architecture.md` §11).

---

## 3. Form des Vertrags

### 3.1 Beschreibung

Die OpenAPI-Beschreibung wird **aus dem Code erzeugt** und ist Teil der Auslieferung. Eine
handgepflegte Beschreibung läuft auseinander und ist damit wertlos.

### 3.2 Benennung und Datenformate

- Bezeichner englisch nach dem Glossar; Felder im JSON in gemischter Schreibweise mit kleinem
  Anfangsbuchstaben.
- Ressourcen im Plural, Fachvorgänge als benannter Unterpfad:
  `POST /api/v1/sales-orders/{id}/confirm` (`_architecture.md` §7).
- **Zeitpunkte** nach ISO 8601 in UTC.
- **Geldbeträge und Mengen werden als Zeichenkette übertragen**, nicht als JSON-Zahl. Grund:
  Verbraucher lesen JSON-Zahlen üblicherweise als Gleitkommazahl doppelter Genauigkeit; bei
  den zugelassenen Nachkommastellen (`_data.md` §6) sind damit Rundungsfehler möglich. Ein
  Betrag wird immer zusammen mit seinem Währungscode übertragen, eine Menge mit ihrer
  Einheit.

### 3.3 Fehler

- Einheitliches Fehlerformat für **alle** Endpunkte, mit Typ, Titel, Statuscode, Beschreibung
  und Bezug zur Anfrage.
- **Zusätzlich ein stabiler, maschinenlesbarer Fehlercode.** Der Meldungstext darf sich
  ändern und ist übersetzt; **der Code niemals** — Verbraucher werten ihn aus.
- Bei Eingabefehlern wird **je betroffenem Feld** ein Eintrag geliefert, nicht eine
  Sammelmeldung.
- Meldungstexte in der Sprache des Zugangs (Deutsch oder Englisch), der Code sprachunabhängig.

### 3.4 Statuscodes

| Code | Bedeutung |
|---|---|
| 200 | Erfolgreich gelesen oder ausgeführt |
| 201 | Ressource angelegt |
| 202 | Angenommen, wird verarbeitet (Auftragseingang, §5) |
| 204 | Erfolgreich, kein Inhalt |
| 400 | Eingabe fehlerhaft |
| 401 / 403 | Nicht angemeldet / nicht berechtigt |
| 404 | Nicht vorhanden oder für diesen Zugang nicht sichtbar |
| 409 | Konflikt — Nebenläufigkeit, Zustand passt nicht, Idempotenzkollision |
| 422 | Fachlich abgelehnt, obwohl formal korrekt |
| 429 | Durchsatzbegrenzung erreicht |

### 3.5 Listen

- **Seitenweise über einen Fortsetzungszeiger**, nicht über einen Zeilenversatz. Grund: bei
  Versatz überspringt oder wiederholt man Datensätze, sobald sich die Daten während des
  Blätterns ändern, und die Abfrage wird bei hohen Werten langsam.
- Standardgröße 50, Höchstwert 200 (`_architecture.md` §9).
- **Filter und Sortierung sind je Endpunkt ausdrücklich festgelegt.** Eine allgemeine
  Abfragesprache ist verboten — sie wäre ein unbegrenzter Vertrag und ein Leistungsrisiko.

---

## 4. Idempotenz

- **Jede schreibende Anfrage trägt einen vom Aufrufer vergebenen Idempotenzschlüssel.**
- Der Server hält Schlüssel und Ergebnis fest. Eine Wiederholung liefert **dasselbe
  Ergebnis** und führt den Vorgang **nicht erneut** aus.
- Aufbewahrung der Schlüssel: **30 Tage**.
- **Gleicher Schlüssel mit abweichendem Inhalt ist ein Konflikt** (409), kein stillschweigend
  akzeptierter Sonderfall.
- Lesende Anfragen brauchen keinen Schlüssel.

Ohne diese Regel erzeugt ein Kunde, dessen Anfrage in eine Zeitüberschreitung läuft und der
sie wiederholt, denselben Auftrag zweimal.

---

## 5. Auftragseingang

### 5.1 Grundsatz

**Ein eingehender Kundenauftrag wird nicht unmittelbar zum Beleg.** Er wird als Eingang
angenommen, geprüft und erst dann übernommen. Andernfalls gibt es keinen Ort für die Fälle,
die es in der Praxis immer gibt: unbekannte Artikelnummer, abweichender Preis, überschrittenes
Kreditlimit, unplausible Menge.

### 5.2 Ablauf

```
Annahme (202)  →  Prüfung  →  übernommen | Klärfall | abgelehnt
```

- Die **Annahme ist von der Verarbeitung entkoppelt**. Der Kunde erhält sofort eine
  Eingangskennung, nicht das Ergebnis der fachlichen Prüfung.
- **Der Rohdatensatz bleibt unverändert erhalten** — er ist der Nachweis, was der Kunde
  tatsächlich geschickt hat.
- Zustände: `eingegangen` → `geprüft` → `übernommen` | `klärung` | `abgelehnt`.

### 5.3 Prüfungen

Mindestens: Formatgültigkeit, Auflösung aller externen Kennungen (§6), Preisabweichung
gegenüber der gültigen Preisliste, Kreditlimit, Plausibilität der Mengen.

### 5.4 Klärfälle

- Klärfälle brauchen eine **Oberfläche im BackOffice**. Ein Eingang, den niemand sieht, ist
  ein verlorener Auftrag.
- Das **Alter des ältesten unbearbeiteten Eingangs** ist eine überwachte Kennzahl
  (`_operations.md` §2.2).
- Ob ein fehlerfrei geprüfter Eingang **automatisch** übernommen wird oder eine Freigabe
  braucht, ist je Zugang einstellbar.

### 5.5 Rückmeldung an den Kunden

- Statusabruf über die API unter der Eingangskennung, zusätzlich optional als Ereignis (§7).
- Bei Ablehnung: stabiler Fehlercode mit Feldbezug (§3.3), damit der Kunde den Fehler
  maschinell auswerten kann.

### 5.6 Doppelte Übertragung

Zwei voneinander unabhängige Sicherungen: der technische Idempotenzschlüssel (§4) **und** eine
fachliche Dublettenprüfung über die Bestellnummer des Kunden.

---

## 6. Abbildung externer Kennungen

- Externe Kennungen — Artikelnummer des Kunden, dessen eigene Kundennummer, Bestellnummern —
  werden auf interne Kennungen abgebildet.
- **Die Abbildung gilt je Zugang beziehungsweise je Geschäftspartner.** Dieselbe
  Artikelnummer kann bei zwei Kunden zwei verschiedene Artikel bezeichnen; eine globale
  Abbildungstabelle wäre falsch.
- **Eine unbekannte Kennung führt zum Klärfall, niemals zu einem Rateversuch.** Keine
  Ähnlichkeitssuche, die im Zweifel den falschen Artikel liefert.
- Die externe Kennung wird am entstehenden Beleg mitgeführt, damit Rückfragen des Kunden
  zuordenbar sind.

---

## 7. Ausgehende Ereignisse

### 7.1 Zwei Arten, die nicht vermischt werden

| | Domain-Ereignis | Integrationsereignis |
|---|---|---|
| Reichweite | Modulintern | Nach außen |
| Vertrag | Frei änderbar | Stabil, versioniert wie die API |
| Inhalt | Was das Modul braucht | Bewusst minimal |

Ein Domain-Ereignis wird **nie ungefiltert** nach außen weitergereicht.

### 7.2 Zustellung

- Veröffentlichung ausschließlich über die **Outbox**, im selben Commit wie die fachliche
  Änderung (`_architecture.md` §8).
- Zustellung **mindestens einmal**, mit Wiederholung in wachsendem Abstand. Empfänger müssen
  wiederholungsfest sein.
- **Die Reihenfolge ist nur je Entität zugesichert, nicht global.** Wer eine globale
  Reihenfolge annimmt, baut einen Fehler ein.
- Der **Rückstand der Outbox** ist eine überwachte Kennzahl mit Alarm
  (`_operations.md` §2.2).

### 7.3 Webhooks

- Ziel je Zugang einstellbar, Zustellung **signiert** und mit Zeitstempel gegen
  Wiedereinspielung gesichert.
- Zustellversuche werden protokolliert. Nach einer festgelegten Zahl von Fehlversuchen wird
  das Ziel deaktiviert und ein Alarm ausgelöst.

### 7.4 Inhalt

**Ereignisse enthalten keine sensiblen Nutzdaten** — nur Art, Kennung und Zeitpunkt.
Details holt der Empfänger über die API, wo Rechte und Protokollierung greifen. Für Daten der
höchsten Schutzklasse ist das zwingend (`_security.md` §5.1).

---

## 8. Automatisierungswerkzeug

- Läuft **im Netz des Kunden**, als Teil der Auslieferung.
- Authentifizierung über einen **eigenen maschinellen Zugang** mit eigenen Rechten
  (`_security.md` §4). Niemals ein Zugang mit Vollrechten, weil es bequem ist.
- Zwei Richtungen: Anwendung → Werkzeug über Webhook (§7.3), Werkzeug → Anwendung über die
  Integrations-API. **Ein direkter Datenbankzugriff ist auch hier verboten.**
- **Keine Fachlogik im Automatisierungswerkzeug** (`_architecture.md` §10.3):

| Erlaubt | Verboten |
|---|---|
| Ablaufsteuerung und Verkettung | Berechnungen |
| Benachrichtigung, Mailversand | Prüfungen und Validierung |
| Datentransport zwischen Systemen | Statusentscheidungen |
| Anbindung fremder Systeme | Ableitung von Kontierung oder Preisen |

- **Abläufe werden exportiert und versioniert im Repository abgelegt.** Ein Ablauf, der nur
  in der Oberfläche des Werkzeugs existiert, ist Schattenwissen: nicht überprüfbar, nicht
  wiederherstellbar, beim nächsten Personalwechsel verloren.
- Jeder Ablauf hat einen **definierten Fehlerpfad**. Stillschweigendes Scheitern ist
  verboten — ein fehlgeschlagener Ablauf meldet sich.
- Änderungen an Abläufen sind protokollpflichtig (`_security.md` §5.1).

---

## 9. Buchhaltungsübergabe

Die Anwendung führt keine eigene Buchhaltung, sondern erzeugt einen Buchungsstapel
(`_data.md` §8). **Die Umsetzung liegt je Produkt** (`_architecture.md` §2.3) — das hier
beschriebene Muster ist für jedes Produkt verbindlich. Zielformat und Satzaufbau des
Buchhaltungssystems stehen im Projektprofil.

- Erzeugt **je Zeitraum** aus den eingefrorenen Kontierungen gebuchter Belege — die
  Installation ist genau eine Firma und damit genau ein Buchhaltungsmandant (`_data.md` §5).
- Inhalt je Buchungssatz: Belegdatum, Belegnummer, Konto und Gegenkonto, Betrag,
  Steuerschlüssel, Buchungstext, gegebenenfalls Kostenstelle.
- Debitoren- und Kreditorennummern stammen direkt vom Geschäftspartner
  (`_data.md` §5.1).
- **Sperre nach Übergabe:** Übergebene Belege sind gekennzeichnet und werden nicht erneut
  übergeben. Ein zweiter Lauf über denselben Zeitraum liefert nichts, sofern er nicht
  ausdrücklich als Wiederholung angefordert wird.
- **Korrekturen laufen über Storno und Neubeleg**, niemals über einen geänderten Export.
- Der Export ist ein **Hintergrundauftrag** (`_architecture.md` §8) und protokollpflichtig
  (`_security.md` §5.1).
- Das Protokoll weist **Zeitraum, Belegzahl und Summen je Konto** aus, damit die Buchhaltung
  abstimmen kann.
- Formatvariante und -version sind einstellbar; die Formatlogik liegt an genau einer Stelle.

---

## 10. Offline-Abgleich des Feld-Clients

### 10.1 Grundlagen

- Der Client arbeitet auf einem **lokalen Ausschnitt** — dem aktuell zugeteilten Arbeitsvorrat,
  nicht dem Gesamtbestand (`_security.md` §7).
- **Schlüssel werden auf dem Gerät vergeben** (`_data.md` §3.1). Nur so ist ein offline
  erfasster Datensatz von Anfang an identifizierbar.
- Übertragen wird ein **Stapel von Vorgängen**, jeder mit eigenem Idempotenzschlüssel (§4),
  in der Reihenfolge ihrer Entstehung.

### 10.2 Zeit

- Jeder Datensatz trägt **zwei Zeitpunkte**: die Erfassung auf dem Gerät und den Eingang auf
  dem Server. **Beide werden gespeichert**, keiner wird in den anderen umgerechnet.
- **Die Uhr des Geräts gilt als nicht vertrauenswürdig.** Die Abweichung zur Serverzeit wird
  beim Abgleich ermittelt und mitgespeichert. Bei einem Nachweis, der später eine Übergabe
  belegen soll, ist das der Unterschied zwischen belastbar und wertlos.

### 10.3 Konflikte

- Übergaben sind **Ereignisse in einer Kette** und werden angefügt, nicht überschrieben.
  Echte Schreibkonflikte sind dadurch selten.
- Wo sie auftreten — etwa weil der Arbeitsvorrat serverseitig umdisponiert wurde, während
  offline erfasst wurde — gilt: **Der Server gewinnt bei Stammdaten, das Feldereignis geht nie
  verloren.** Es wird zum Klärfall.
- **Stilles Verwerfen einer Felderfassung ist verboten.** Was draußen erfasst wurde, ist
  entweder übernommen oder ein sichtbarer Klärfall.

### 10.4 Nachweise und Überwachung

- Unterschriften und Fotos werden als eigene Anhänge mit Bezug zum Vorgang übertragen und
  **erst nach bestätigtem Empfang lokal gelöscht** (`_security.md` §7).
- Der Abgleichsstand je Gerät ist eine überwachte Kennzahl (`_operations.md` §2.2). Ein Gerät,
  das seit Stunden nichts geliefert hat, muss auffallen.

---

## 11. Weitere Anbindungen

Absehbar sind Versanddienstleister, Telematik, Kassensysteme, Kursdaten und
Zahlungsverkehr.

Für **jede** neue Anbindung gilt ohne Ausnahme: eigener maschineller Zugang, Idempotenz bei
schreibenden Aufrufen, einheitliches Fehlerformat, Protokollierung, definierter Fehlerpfad,
und keine Fachlogik außerhalb der Anwendung. Eine neue externe Anbindung erfordert eine ADR
(`_architecture.md` §11).

---

## 12. Verbotsliste

1. Direkter Datenbankzugriff von außen — auch durch das Automatisierungswerkzeug oder ein
   Auswertungswerkzeug.
2. Brechende Änderung innerhalb einer Hauptversion der API.
3. Abschaltung einer Version ohne Nachweis, wer sie noch nutzt.
4. Handgepflegte statt erzeugter Schnittstellenbeschreibung.
5. Geldbetrag oder Menge als JSON-Zahl statt als Zeichenkette.
6. Geldbetrag ohne Währung, Menge ohne Einheit.
7. Fehlerantwort ohne stabilen, maschinenlesbaren Code.
8. Schreibende Anfrage ohne Idempotenzschlüssel.
9. Gleicher Idempotenzschlüssel mit abweichendem Inhalt stillschweigend akzeptiert.
10. Eingehender Kundenauftrag, der unmittelbar zum Beleg wird.
11. Verworfener oder überschriebener Rohdatensatz eines Eingangs.
12. Unbekannte externe Kennung durch Ähnlichkeitssuche aufgelöst.
13. Ereignisversand außerhalb der Outbox.
14. Sensible Nutzdaten im Ereignisinhalt.
15. Annahme einer globalen Reihenfolge von Ereignissen.
16. Unsignierter Webhook.
17. Fachlogik in einem Ablauf des Automatisierungswerkzeugs.
18. Ablauf, der nur in der Oberfläche des Werkzeugs existiert und nicht im Repository liegt.
19. Ablauf ohne Fehlerpfad.
20. Erneute Buchhaltungsübergabe bereits übergebener Belege ohne ausdrückliche Wiederholung.
21. Korrektur eines übergebenen Belegs durch geänderten Export statt durch Storno.
22. Serverzeit statt Erfassungszeit — oder umgekehrt — bei offline erfassten Nachweisen.
23. Stilles Verwerfen einer Felderfassung beim Abgleich.

---

## Verweise

- Übergeordnet, Topologie und Transaktionsgrenzen: `_architecture.md`
- Kontierung, Belegstatus, Schlüssel, eine Firma je Installation: `_data.md`
- Maschinelle Zugänge, Audit, Feld-Gerät: `_security.md`
- KI-Zugriff über die API, Schutzklassen: `_ai.md`
- Outbox-Überwachung, Alarme, Hintergrundaufträge: `_operations.md`
