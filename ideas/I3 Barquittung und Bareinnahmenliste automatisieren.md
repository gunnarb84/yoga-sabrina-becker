# Barquittung und Bareinnahmenliste automatisieren

## Meta
- **State:** Draft

## Problem
Sabrina Becker schreibt für jede Barzahlung von Hand eine Quittung (bisher
handschriftliche Barquittung + manuelle Bareinnahmenliste). Die Erfassung in der
Verwaltung erzeugt zwar automatisch Payment und Bareinnahmenbeleg, aber:

- Die Belege sind in der Oberfläche **nicht sichtbar** — es gibt keine
  Bareinnahmenliste (die Spec F4-S1 fordert sie; das ist eine bestehende Lücke).
- Die Erfassung erfolgt nur einzeln je Anmeldung (Termin für Termin anklicken) —
  bei einem Kurs mit vielen Teilnehmern in bar ist das je Termin viel Handarbeit.
- Die Teilnehmenden erhalten keine Barquittung (kein PDF, kein E-Mail-Versand);
  die Belege existieren nur in der Datenbank.
- Rechnungen bei Überweisung sind bereits vollständig automatisiert und brauchen
  keine Änderung.

## Lösungsidee
Die Bareinnahmenabwicklung wird auf drei Bausteinen automatisiert:

1. **Bareinnahmenliste** (Nachtrag zur Spec-Lücke): Liste aller Bareinnahmenbelege
   mit Datum, Belegnummer, Empfänger/in und Betrag, mit PDF-Download des Belegs
   (Muster wie die bestehende Rechnungsliste).
2. **Massenerfassung je Veranstaltung**: Eine Maske zeigt alle offenen
   Anmeldungen mit Zahlungsart „Bar" einer Veranstaltung; je Person ein
   „bezahlt"-Haken mit Zahlungsdatum. Einmal speichern erzeugt für jede
   angehakte Anmeldung Payment und Bareinnahmenbeleg en bloc (je Beleg die
   bestehende lückenlose Nummernvergabe). Die Einzelerfassung bleibt daneben
   möglich.
3. **Barquittung als PDF mit E-Mail-Versand**: Bei jeder erfassten Barzahlung
   (einzel oder en bloc) entsteht die Barquittung als PDF wie bei Rechnungen;
   sie wird automatisch per E-Mail an die Teilnehmer/in versandt (protokolliert
   als ausgehende Nachricht) und ist in der Bareinnahmenliste jederzeit
   herunterladbar.

Der Beleg entsteht weiterhin erst beim erfassten Geldeingang — nie schon bei der
Online-Anmeldung: Der Bareinnahmenbeleg ist lückenlos nummeriert und
unveränderlich und dokumentiert einen tatsächlichen Geldfluss. Der Fall „vorab
bezahlt" ist abgedeckt, weil die Erfassung jederzeit möglich ist.

Kostenlose Veranstaltungen (Zahlungsart „Kostenlos") bleiben unverändert: keine
Zahlung, kein Beleg, die Anmeldung gilt direkt als bezahlt.

## Scope
- **In Scope:**
  - Bareinnahmenliste in der Verwaltung (Liste aller Bareinnahmenbelege) mit
    PDF-Download je Beleg.
  - Barquittung als PDF (Vorlage wie Rechnung, Belegrolle B) mit Download.
  - Automatischer E-Mail-Versand der Barquittung an die Teilnehmer/in bei der
    Erfassung der Barzahlung (als ausgehende Nachricht protokolliert).
  - Massenerfassung je Veranstaltung: alle offenen „Bar"-Anmeldungen auf einer
    Maske, „bezahlt"-Haken je Person, gemeinsames Speichern.
  - Einzelerfassung „Zahlung erfassen" bleibt bestehen (mit E-Mail-Versand).
- **Out of Scope:**
  - Änderungen am Überweisungsablauf (Rechnung bei Anmeldung, manuelles
    Markieren des Zahlungseingangs — bleibt wie F4-S2/S4).
  - Automatischer Abgleich von Zahlungseingängen mit der Bank.
  - Erzeugung von Barquittungen bereits bei der Online-Anmeldung (fachlich
    abgelehnt: Beleg dokumentiert Geldeingang, nicht die Buchung).
  - Teilbeträge/Ratenzahlungen je Anmeldung.
  - Stornierungen (Gutschrift/Rückgabebestätigung) — abgedeckt durch F4-S3,
    bleibt wie bestehend.

## Auswirkungen auf den Bestand
- **Specs:** Feature `E2/F4 Zahlungen und Belege verwalten` wird erweitert:
  neue Stories für Bareinnahmenliste mit PDF, Barquittungs-PDF mit
  E-Mail-Versand und Massenerfassung; bestehende S1 (Bareinnahmenliste) wird
  damit tatsächlich erfüllt, S2–S4 bleiben unverändert gültig.
- **Datenstruktur:** Keine neuen Tabellen nötig — `payments`, `cash_receipts`
  und `outbound_messages` reichen; Felder wie `E-Mail-Status je Beleg` laufen
  über `outbound_messages` (Bezug über Zahlung/Anmeldung).
- **Backend:** Neue Abfragen (Bareinnahmenbelege als Projektion,
  offene „Bar"-Anmeldungen je Veranstaltung), neuer Schreibvorgang
  (Massenerfassung), Erweiterung von `RecordPayment` um PDF- und
  E-Mail-Versand; neuer PDF-Vorgang für Bareinnahmenbelege analog
  `GenerateInvoicePdf`.
- **Quellcode:** Berührungspunkte: `modules/Verwaltung/Application/Payment/*`,
  `modules/Verwaltung/Application/CashReceipt` (PDF analog
  `Application/Invoice/GenerateInvoicePdf`), neue UI-Komponenten unter
  `modules/Verwaltung/Ui` (Bareinnahmenliste, Massenerfassung), Routen und
  Navigation; Risiken gering, da bestehende Vorgänge nur erweitert werden und
  die Nummernvergabe (`NextNumber`) unverändert bleibt.

## Entscheidungen
- 10.09.2026: Idee angelegt, Nummer I3 reserviert.
- 10.09.2026: Belegzeitpunkt geklärt — Barquittung entsteht beim erfassten
  Geldeingang (Erfassung), nicht bei der Online-Anmeldung; Beleg ist Nachweis
  des tatsächlichen Geldflusses.
- 10.09.2026: Massenerfassung je Veranstaltung beschlossen („bezahlt"-Haken je
  Person, gemeinsames Speichern); Einzelerfassung bleibt bestehen.
- 10.09.2026: Barquittung als PDF beschlossen, Download in der Bareinnahmenliste
  **und** automatischer E-Mail-Versand an die Teilnehmer/in.
- 10.09.2026: Bar wird üblicherweise bei den Terminen bezahlt; Vorabzahlung
  kommt vor — abgedeckt durch jederzeit mögliche Erfassung.
- 10.09.2026: Überweisungen bleiben unverändert (Rechnung bei Anmeldung +
  manuelles Markieren des Eingangs reichen).
- 10.09.2026: Kostenlose Veranstaltungen: keine Zahlung, kein Beleg, Anmeldung
  direkt „bezahlt" (bestehendes Verhalten „Kostenlos" bleibt, wird per Test
  verifiziert).

## Offene Punkte
- (keine — Stakeholder-Fassung zur Bestätigung vorgelegt)