# Barquittung nach Papiervorlage

## Meta
- **State:** Ready

## Problem
Das automatisch erzeugte Beleg-PDF (`bareinnahmenbelege/pdf.blade.php`) ist eine schlichte
Tabelle und sieht nicht wie die Barquittung aus, die Sabrina Becker bisher handschriftlich
auf ihrer Papiervorlage ausgefüllt hat. Die Quittungen sollen sich einheitlich anlassen —
im Design und im Aufbau ihrer Vorlage.

## Lösungsidee
Das generierte Beleg-PDF wird exakt auf den Aufbau der Papiervorlage
(`vorlagen/Barquittung.pdf`) umgestellt: A4 mit zwei identischen Quittungshälften
(oben „Original – für Teilnehmer:in", unten „Durchschrift – für Unterlagen") und
Trennlinie mit Scherensymbol. Beide Hälften werden identisch befüllt. Das System füllt:
Beleg-Nr. (Format `2026-NNNNN`, siehe I4), Datum, Erhalten von, Betrag, in Worten
(ausgeschriebener Betrag, automatisch generiert), Für folgende Leistung/Kurs,
Kleinunternehmer-Hinweis und „Betrag dankend bar erhalten." Unterschrift und „Ort, Datum"
bleiben als leere Linien zum Handsignieren. Design der Vorlage (warmes Beige, Gold-Akzente,
dunkle Farbfläche, Schrift Lato) wird nachgebildet.

## Scope
- **In Scope:**
  - Komplette Neugestaltung des Beleg-PDFs nach der Papiervorlage (zwei Hälften,
    Trennlinie, alle Vorlagenfelder, Design).
  - Automatische Betragsausgabe „In Worten" (deutsche Ausschreibung).
  - Einbindung der Schrift Lato für das PDF.
- **Out of Scope:**
  - Rückgabebestätigungen (CashReturn) und Rechnungen behalten ihr bisheriges PDF-Layout.
  - Papiervorlage selbst wird nicht geändert.
  - E-Mail-Versand ändert sich nicht (das PDF bleibt Anhang, siehe F4-S6).

## Auswirkungen auf den Bestand
- **Specs:** Anpassung der Akzeptanzkriterien in
  `specs/E2 CMS & Verwaltung/F4 Zahlungen und Belege verwalten/S5 Bareinnahmenliste und
  Beleg-PDF.md` (Layout-Kriterien nach Vorlage) und S6 (Anhang entspricht neuem Layout);
  Meta-`State` auf `Modified`.
- **Datenstruktur:** Keine Änderung — „In Worten" wird aus dem Betrag generiert, nicht
  gespeichert.
- **Backend:** `GenerateCashReceiptPdf` erzeugt das neue Layout; Betrag-in-Worten-Funktion
  in der Application-Schicht.
- **Quellcode:** `src/resources/views/bareinnahmenbelege/pdf.blade.php` wird neu gebaut
  (Dompdf unterstützt die nötigen Mittel: Farbflächen, Rahmenradius, zwei Hälften auf
  einer A4-Seite, gestrichelte Linie); Lato-Fontdateien werden für Dompdf eingebunden;
  Farben werden im PDF-Stylesheet nachgebildet. Risiko: gering — nur die PDF-Darstellung
  ändert sich, keine Datenänderung.

## Entscheidungen
- 10.09.2026: Idee angelegt, Nummer I5 reserviert.
- 10.09.2026: Layout exakt wie die Papiervorlage (zwei Hälften, Trennlinie), System befüllt
  alle Felder bis auf Unterschrift und Ort/Datum.
- 10.09.2026: „In Worten" schreibt das System automatisch aus; Unterschrift und
  „Ort, Datum" bleiben als leere Linien zum Handsignieren.
- 10.09.2026: Belegnummer auf dem PDF im Format `2026-NNNNN` passend zur Vorlage und zu den
  handschriftlich vergebenen Nummern (Umstellung siehe I4).
- 10.09.2026: Nur der Bareinnahmenbeleg erhält das neue Layout; Rückgabebestätigungen und
  Rechnungen bleiben unverändert.

## Offene Punkte
- (leer)