> **Typ:** How-To · **Für:** Anwender · **Bezug:** [specs/E2 CMS & Verwaltung/F4 Zahlungen und Belege verwalten](../../specs/E2%20CMS%20%26%20Verwaltung/F4%20Zahlungen%20und%20Belege%20verwalten)
> **Stand:** 2026-09-10

# Barzahlungen erfassen und Quittungen ausstellen

Dieses How-To richtet sich an die Verwaltung (Sabrina Becker bzw. Helfer/innen) und
beschreibt, wie Sie Barzahlungen erfassen — auch für Kundinnen und Kunden ohne
vorherige Anmeldung über die Webseite —, die dazugehörige Barquittung als PDF
ausgeben lassen, sie per E-Mail versenden und mit der Massenerfassung viele
Terminzahlungen in einem Durchgang abwickeln.

Bei jeder erfassten Barzahlung passiert dies automatisch:

- Der Bareinnahmenbeleg wird mit fortlaufender Belegnummer (z. B. `2026-00001`)
  erzeugt und als **Barquittung** als PDF bereitgestellt. Das PDF folgt dem Aufbau
  der Papiervorlage: ein Blatt mit zwei identischen Quittungshälften
  („Original – für Teilnehmer:in" oben, „Durchschrift – für Unterlagen" unten),
  getrennt durch eine Trennlinie zum Auseinanderschneiden.
- Die Barquittung wird per E-Mail an die E-Mail-Adresse der Teilnehmerin/des
  Teilnehmers gesendet — sofern eine vorhanden ist. Der Versand ist unter
  **Nachrichten** nachvollziehbar.
- Der Zahlungsstatus der Anmeldung wechselt auf **bezahlt**.

Kostenlose Anmeldungen erhalten keine Barquittung und keine E-Mail.

## Eine Barzahlung einzeln erfassen

1. Öffnen Sie im Menü **Aktivitäten** die Veranstaltung und wählen Sie **Anmeldungen**.
2. Wählen Sie bei der Anmeldung der Teilnehmerin/des Teilnehmers **Zahlung erfassen**.
   Der vorgeschlagene Betrag ist der Preis der Veranstaltung.
3. Prüfen Sie **Zahlungsdatum** und **Empfänger/in**, passen Sie sie bei Bedarf an, und
   wählen Sie **Zahlung speichern**.

Die Anmeldung erhält den Zahlungsstatus **bezahlt**, der Beleg entsteht, und die
Barquittung wird per E-Mail versendet.

## Eine Bareinnahme ohne vorherige Anmeldung erfassen

Für Kundinnen und Kunden, die nicht über die Webseite angemeldet waren (Laufkundschaft):

1. Wählen Sie im oberen Menü **Bareinnahme erfassen**.
2. Wählen Sie die **Veranstaltung**, tragen Sie **Vorname** und **Nachname** ein und
   erfassen Sie den **Betrag** frei. Die E-Mail-Adresse ist optional — ohne sie
   entsteht nur der Beleg mit PDF-Download, es wird keine E-Mail versendet.
3. **Zahlungsdatum** ist mit dem heutigen Datum vorbelegt; passen Sie es bei Bedarf an.
4. Wählen Sie **Bareinnahme speichern**. Die Maske legt Teilnehmer/in, Anmeldung und
   Zahlung in einem Schritt an und zeigt die Belegnummer mit einem Link zum Download
   der Barquittung als PDF.

Es findet keine Kapazitäts- oder Wartelistenprüfung statt, weil die Person anwesend ist
und bereits gezahlt hat. Gibt es die Person mit exakt gleichem Vor- und Nachnamen schon,
wird ihr bestehender Datensatz verwendet. Die Anmeldung ist sofort **bestätigt**,
**bezahlt** und trägt als **Herkunft** den Wert **Verwaltung** (Anmeldungen aus dem
Online-Anmeldeprozess der Webseite tragen **Webseite** — die Spalte **Herkunft** zeigen
die Anmeldungslisten an).

## Handschriftliche Belege nachpflegen

Bereits handschriftlich vergebene Belegnummern können Sie später im System nachpflegen,
damit alle Bareinnahmen an einem Ort liegen:

1. Schalten Sie in der Maske **Bareinnahme erfassen** das Häkchen
   **Handschriftlichen Beleg nachpflegen** ein.
2. Tragen Sie die **Belegnummer** der handschriftlichen Quittung im Format
   `JJJJ-NNNNN` (z. B. `2026-00012`) und das **Ausstellungsdatum** des Originals ein.
3. Wählen Sie **Bareinnahme speichern**. Der Beleg entsteht mit der vorgegebenen
   Nummer statt mit der nächsten freien Nummer.

Wichtig: Pflegen Sie zuerst alle handschriftlichen Belege nach, bevor Sie die erste
neue Barzahlung erfassen. Der Nummernkreis zählt automatisch hinter der höchsten
vorgegebenen Nummer weiter, sodass keine Nummer doppelt vergeben wird.

## Barzahlungen massenweise erfassen

Für den Zahlungseinzug bei den Terminen einer Veranstaltung:

1. Öffnen Sie im Menü **Aktivitäten** die Veranstaltung und wählen Sie **Anmeldungen**.
2. Wählen Sie **Barzahlungen massenweise erfassen**. Die Liste zeigt alle offenen
   Bar-Anmeldungen der Veranstaltung — Anmeldungen mit Rechnung (Überweisung) und
   bereits erfasste Zahlungen fehlen.
3. Setzen Sie bei allen Anmeldungen, die Sie jetzt bezahlt haben, das Häkchen unter
   **Bezahlt**. **Zahlungsdatum** ist mit dem aktuellen Zeitpunkt vorausgefüllt und
   lässt sich je Zeile ändern.
4. Wählen Sie **Ausgewählte Zahlungen speichern**. Die Meldung bestätigt die Anzahl der
   erfassten Zahlungen; die erfassten Anmeldungen verschwinden aus der Liste und sind in
   der Anmeldungsliste als **bezahlt** markiert.

Ohne Auswahl erhalten Sie den Hinweis, mindestens eine Anmeldung zu wählen; es wird
dann nichts erfasst.

## Die Bareinnahmenliste verwenden

1. Wählen Sie im oberen Menü **Bareinnahmen**. Die Liste zeigt alle Barquittungen mit
   Belegnummer, Datum, Empfänger/in und Betrag — die neueste zuerst.
2. Um die Liste einzugrenzen, geben Sie unter **Belegnummer** einen Teil der Nummer oder
   unter **Empfänger/in** einen Namen ein. Die Liste aktualisiert sich automatisch.
3. Wählen Sie **PDF**, um die Barquittung herunterzuladen und auszudrucken.

## Eine nicht versendete Barquittung erneut senden

Scheitert der E-Mail-Versand, wird die Zahlung trotzdem erfasst und die Barquittung
erstellt; die Nachricht erhält den Status **fehlgeschlagen**. Öffnen Sie unter
**Nachrichten** das **Detail** der Nachricht und wählen Sie **Erneut senden** (siehe
[Kontaktanfragen bearbeiten](kontaktanfragen-bearbeiten.md) für den Nachrichtenbereich).

## Ergebnis

Jede Bareinnahme — auch von Laufkundschaft — hat eine fortlaufende Barquittung als PDF
im Aufbau der Papiervorlage, Teilnehmer/innen mit E-Mail-Adresse erhalten die Quittung
automatisch per E-Mail, und die manuelle Bareinnahmenliste entfällt.

## Siehe auch

- [Zu einer Veranstaltung anmelden](zu-einer-veranstaltung-anmelden.md)
- [Meldungen der Anwendung](../referenz/meldungen.md)