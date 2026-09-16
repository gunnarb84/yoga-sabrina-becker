# Verwaltungs-Dashboard

## Meta
- **State:** Ready

## Problem
Beim Öffnen der Verwaltung landet die Anwenderin (Sabrina Becker) auf einem
Platzhalter ohne Überblick. Um zu wissen, was ansteht — heutige Termine, unbezahlte
Rechnungen, fehlgeschlagene E-Mails, Wartelisteneinträge —, muss sie in mehrere
Listen navigieren. Das Dashboard soll als Startseite eine sofortige Orientierung
bieten und direkte Wege zu den relevanten Listen und Masken eröffnen.

## Lösungsidee
Das Dashboard wird zur Startseite der Verwaltung und zeigt vier Blöcke:

1. **Nächste Termine** — Termine vom heutigen Tag bis zum Ende des laufenden Monats,
   je Zeile mit Veranstaltung, Datum und bestätigten Anmeldungen (freien Plätzen).
   Klick auf einen Termin öffnet die Anmeldeliste der Veranstaltung.
2. **Offene Aufgaben** — Zähler je Zeile: unbezahlte Rechnungen, fehlgeschlagene
   ausgehende E-Mails, Wartelisteneinträge; Kontaktanfragen erscheinen als Link
   ohne Zähler (siehe Entscheidungen). Klick öffnet die jeweilige Liste bzw. das
   Nachrichtendetail.
3. **Kennzahlen** — Anmeldungen im laufenden Monat, Bareinnahmen im laufenden
   Monat, aktueller Kassenbestand; rein informativ.
4. **Schnellzugriffe** — Links zu den häufigsten Masken: Bareinnahme erfassen,
   neue Aktivität, Massenerfassung.

Das Dashboard ist reine Anzeige und Navigation: Es speichert nichts selbst,
alle Aktionen bleiben in den Listen und Masken.

## Scope
- **In Scope:** Die vier Blöcke oben; Startseite der Verwaltung (bisheriger
  Platzhalter wird ersetzt); neue Lese-Abfragen für zukünftige Termine mit
  Belegung und für die Kennzahlen-Aggregation.
- **Out of Scope:** Inline-Erfassungen im Dashboard (Bareinnahme, Zahlungen),
  konfigurierbare Blöcke, Statistiken rückwirkend über mehrere Monate,
  Kontaktanfragen-Zähler (Modulgrenze, siehe Entscheidungen).

## Auswirkungen auf den Bestand
- **Specs:** Neues Feature „F6 Dashboard" in `specs/E2 CMS & Verwaltung/`
  (hinten angehängt, F1–F5 existieren), mit Stories je Block.
- **Datenstruktur:** Keine Migrationen, keine neuen Entitäten — alle Felder
  existieren (`verwaltung_termine`/Sessions, `verwaltung_rechnungen`,
  `verwaltung_ausgehende_nachrichten`, Wartelistenstatus der Anmeldungen,
  Bareinnahmenbelege/Barentnahmen).
- **Backend:** Neue Lese-Abfragen: eine für zukünftige Termine mit
  Anmeldungsbelegung über alle Aktivitäten hinweg (bisher existiert die
  Terminansicht nur je Veranstaltung), eine für die Kennzahlen-Aggregation
  (Anmeldungen/Bareinnahmen im laufenden Monat, Kassenbestand). Zähler für
  Rechnungen (Status), fehlgeschlagene Nachrichten (`OutboundMessagesQuery`
  mit Statusfilter) und Warteliste aus bestehenden Abfragen ableitbar bzw.
  über schlanke Zähl-Abfragen.
- **Quellcode:** `modules/Verwaltung/Ui/Dashboard.php` und
  `modules/Verwaltung/Ui/dashboard.blade.php` (bestehender Platzhalter);
  neue Abfragen unter `modules/Verwaltung/Application/…`; Risiken gering —
  ausschließlich Lese-Abfragen, keine Datenänderung.

## Entscheidungen
- 16.09.2026: Idee angelegt, Nummer I7 reserviert.
- 16.09.2026: Interaktionsgrad — nur Sprünge (Navigation), keine
  Speicher-Aktionen im Dashboard; hält das Dashboard übersichtlich und die
  Logik in den bestehenden Listen/Masken.
- 16.09.2026: Terminvorschau bis zum Ende des laufenden Monats.
- 16.09.2026: Kennzahlen auf den laufenden Monat bezogen; Kassenbestand
  ohne Zeitbezug (immer aktuell).
- 16.09.2026: Kontaktanfragen im Offene-Aufgaben-Block als Link ohne
  Live-Zähler — die Kontaktanfragen gehören zum Webseite-Modul, die
  Modulgrenze sieht keinen Zugriff der Verwaltung auf
  `Webseite.Application` vor, und es soll kein erster Kreuzmodul-Zugriff
  (Tabelle `webseite_kontaktanfragen`) etabliert werden. Nachrüstung über
  eine saubere Schnittstelle möglich.

## Offene Punkte
- (keine)