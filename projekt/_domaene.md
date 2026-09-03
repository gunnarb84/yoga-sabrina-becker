# Domänenwissen — Yoga Sabrina Becker

Fachliche Grundlage für Entscheidungen im Code. Technische Regeln stehen im Harness.

## Produktüberblick

Eine öffentliche Webseite für Yoga-Kurse, -Events und -Workshops mit einem gemeinsamen Backend.
Das Backend ist gleichzeitig CMS für die Webseite und Verwaltungstool für Kurse, Teilnehmer,
Anmeldungen inklusive Warteliste sowie Bar- und Überweisungszahlungen mit fortlaufenden
Belegnummern.

## Fachliche Datentypen

- **Geld**: Beträge in Euro, intern als `decimal(10, 2)` gespeichert, in PHP als `string` oder
  `Money`-Wertobjekt behandelt. Keine Rundungsverluste durch Float.
- **Dauer**: Minuten als positive Ganzzahl.
- **Maximale Teilnehmerzahl**: Positive Ganzzahl, 0 bedeutet keine Begrenzung.
- **Belegnummer**: `PREFIX-JJJJ-NNNNN`, lückenlos pro Jahr (`B`, `R`, `G`, `RB`).

## Belegrollen

| Code | Belegart | Auslöser |
|---|---|---|
| `B` | Bareinnahmenbeleg | Barzahlung |
| `R` | Rechnung | Überweisungszahlung |
| `G` | Gutschrift | Stornierung einer Überweisungszahlung |
| `RB` | Rückgabebestätigung | Stornierung einer Barzahlung |

Belege sind einmalig und unveränderlich. Eine Stornierung erzeugt einen neuen Ausgleichsbeleg.

## Buchungszeitpunkt

Eine Anmeldung gilt ab dem Zeitpunkt der Anmeldung als verbindlich, unabhängig vom
Zahlungseingang. Der Zahlungseingang wird separat als `Payment` erfasst.

## Vorgänge je Modul

### Verwaltung

- Veranstaltung anlegen, bearbeiten, veröffentlichen, abschließen, stornieren.
- Terminserie aus Vorlage generieren.
- Teilnehmer/in anlegen und bearbeiten.
- Anmeldung erstellen, auf Warteliste setzen, bestätigen, stornieren.
- Warteliste nachrücken, wenn ein Platz frei wird.
- Zahlung erfassen (Bar/Überweisung) und Beleg/Rechnung erzeugen.
- Stornierung mit Gutschrift/Rückgabebestätigung erzeugen.
- Ausgehende Nachricht (E-Mail) zu Anmeldung versenden und protokollieren.

### Webseite

- Öffentliche Veranstaltungsübersicht anzeigen.
- Veranstaltungsdetail anzeigen.
- Anmeldeformular für Teilnehmer/in ohne Anmeldung im System.
- Zahlungsinformationen für Überweisung anzeigen.

## Externe Systeme

- E-Mail-Versand über konfigurierbaren SMTP-Transport.
- Keine Social-Media-APIs; Hinweise und Links reichen.

## Schutzklassen

Gesundheitsinformationen von Teilnehmern dürfen nicht an externe KI-Dienste übertragen werden.
Sie werden ausschließlich intern gespeichert und sind für Administrator/innen einsehbar.

## Rollen

- **Administrator/in**: Vollzugriff (Sabrina Becker).
- **Helfer/in**: Eingeschränkter Zugriff, z. B. nur Teilnehmer- und Anmeldungsdaten, keine
  Zahlungsbelege.

## Sprache und Anrede

Oberfläche auf Deutsch, Anrede „Sie". Codebezeichner nach dem Glossar in `PROJEKT.md`.
