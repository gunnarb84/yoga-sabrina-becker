# Barentnahmen erfassen

## Meta
- **State:** Ready

## Problem
Sabrina Becker entnimmt der Barkasse gelegentlich Geld (z. B. private Entnahme,
Barauslage für Wechselgeld, Bezahlungen in bar). Die Bareinnahmenliste zeigt heute nur
die Einnahmen — die Differenz zwischen Bareinnahmen und tatsächlichem Bargeldbestand
ist nicht nachvollziehbar.

## Lösungsidee
Barentnahmen werden in der Verwaltung erfasst und erscheinen in einer gemischten,
chronologischen Bareinnahmenliste mit laufendem **Bestand** je Zeile (Kassenbuch-Prinzip).
Eine Barentnahme bekommt **keine Belegnummer und kein PDF**; optional kann eine
**Fremdbelegnummer** erfasst werden (z. B. Nummer der Kassenquittung eines Barkaufs).
Felder der Erfassung: Datum, Betrag, Zweck (Pflicht), Fremdbelegnummer (optional).

## Scope
- **In Scope:** Erfassung von Barentnahmen, gemischte Bareinnahmenliste mit laufendem Bestand.
- **Out of Scope:** Eigene Belegrolle/Nummernkreis und PDF für Barentnahmen; Kassenbuch
  nach Steuervorgaben.

## Auswirkungen auf den Bestand
- **Specs:** Neue Story in `specs/E2 CMS & Verwaltung/F4 Zahlungen und Belege verwalten/`
  (Barentnahme erfassen); Story der Bareinnahmenliste (F4-S5) wird geändert (gemischte
  Liste mit Bestandsspalte).
- **Datenstruktur:** Neue Tabelle `verwaltung_barentnahmen` (UUID v7 binary(16)) mit
  `datum` (date), `betrag` (decimal 10,2), `zweck` (string), `fremdbelegnummer`
  (string, nullable).
- **Backend:** Neuer Vorgang `RecordCashWithdrawal` im Modul Verwaltung (Ergebnis mit
  stabilen Fehlercodes: amount_invalid, purpose_required u. a.); Erweiterung der
  Bareinnahmenlisten-Abfrage um eine gemischte, chronologische Bewegungsliste mit
  laufendem Bestand: Bareinnahmenbelege (B) und Bar-Rückzahlungen (RB) als Einnahmen
  bzw. Ausgaben plus Barentnahmen als Ausgaben. Keine Belegnummern-Berührung
  (Nummernkreise B/R/G/RB bleiben unverändert).
- **Quellcode:** `modules/Verwaltung/Application/CashWithdrawal/` (neu, Vorgang),
  `modules/Verwaltung/Persistence/` (Modell, Migration, Abfrageerweiterung),
  `modules/Verwaltung/Ui/CashReceipt/` (Liste mit Bestandsspalte, Button und Erfassungs-
  maske als Livewire-Komponente). Keine Berührung mit `NextNumber`/Belegerzeugung.

## Machbarkeitsprüfung
- **Specs:** Keine Widersprüche — F4 deckt bisher nur Einnahmen-/Belegvorgänge ab; die
  Bareinnahmenliste (F4-S5) ändert ihr Anzeigeformat.
- **Datenstruktur:** Belegmodell bleibt unangetastet; Barentnahmen sind eigene Entität
  ohne Belegrolle (Entscheidung oben).
- **Backend:** Bestand setzt sich aus bestehenden Daten (B- und RB-Belege) plus neuer
  Entität zusammen; Bar-Rückzahlungen (RB) existieren bereits (Stornierung Barzahlung).
- **Quellcode:** Bareinnahmenliste (`CashReceipts`, `CashReceiptController`,
  `cash-receipts.blade.php`) ist der Berührungspunkt; Risiken: laufender Bestand braucht
  eine deterministische Reihenfolge (Datum, dann Entstehungszeit) und die Abfrage muss
  drei Bewegungsarten vereinen.

## Entscheidungen
- 10.09.2026: Idee angelegt, Nummer I6 reserviert.
- 10.09.2026: Eine gemischte, chronologische Liste mit Bestandsspalte (Kassenbuch-Prinzip),
  keine getrennten Listen.
- 10.09.2026: Keine Belegnummer und kein PDF für Barentnahmen; optional ein Feld
  Fremdbelegnummer (z. B. Kassenquittung eines Barkaufs).
- 10.09.2026: Felder der Erfassung: Datum, Betrag, Zweck (Pflicht), Fremdbelegnummer (optional).
- 10.09.2026: Bestand = Bareinnahmen − Bar-Rückzahlungen (Rückgabebestätigungen, RB) −
  Barentnahmen; RB-Belege sind real abgeflossenes Bargeld und gehören in die Rechnung.
- 10.09.2026: Erfassung über Button „Barentnahme erfassen" auf der Bareinnahmen-Seite.
- 10.09.2026: Negativer Bestand wird zugelassen (Kasse aus der Zeit vor dem System ist
  vermutlich unvollständig erfasst; keine Sperre, die Nachpflege ausbremst).

## Offene Punkte
- (leer — Stakeholder hat die finale Fassung am 10.09.2026 bestätigt)
