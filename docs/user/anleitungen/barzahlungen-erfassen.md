> **Typ:** How-To · **Für:** Anwender · **Bezug:** [specs/E2 CMS & Verwaltung/F4 Zahlungen und Belege verwalten](../../specs/E2%20CMS%20%26%20Verwaltung/F4%20Zahlungen%20und%20Belege%20verwalten)
> **Stand:** 2026-09-10

# Barzahlungen erfassen und Quittungen ausstellen

Dieses How-To richtet sich an die Verwaltung (Sabrina Becker bzw. Helfer/innen) und
beschreibt, wie Sie Barzahlungen erfassen, die dazugehörige Barquittung als PDF
ausgeben lassen, sie per E-Mail versenden und mit der Massenerfassung viele
Terminzahlungen in einem Durchgang abwickeln.

Bei jeder erfassten Barzahlung passiert dies automatisch:

- Der Bareinnahmenbeleg wird mit fortlaufender Belegnummer (z. B. `B-2026-00001`)
  erzeugt und als **Barquittung** als PDF bereitgestellt.
- Die Barquittung wird per E-Mail an die E-Mail-Adresse der Teilnehmerin/des
  Teilnehmers gesendet. Der Versand ist unter **Nachrichten** nachvollziehbar.
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

Jede Bareinnahme hat eine fortlaufende Barquittung als PDF, die Teilnehmer/innen
erhalten die Quittung automatisch per E-Mail, und die manuelle Bareinnahmenliste
entfällt.

## Siehe auch

- [Zu einer Veranstaltung anmelden](zu-einer-veranstaltung-anmelden.md)
- [Meldungen der Anwendung](../referenz/meldungen.md)