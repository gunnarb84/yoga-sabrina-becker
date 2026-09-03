# Yoga Webseite mit CMS und Verwaltung

## Meta
- **State:** Übernommen

## Problem
Sabrina Becker bietet Yoga-Kurse, -Events und -Workshops an. Derzeit gibt es keine eigene
Webseite und keine digitale Verwaltung. Teilnehmer/innen werden über Social Media
(Instagram, WhatsApp, Facebook) informiert und angemeldet. Zahlungen werden handschriftlich
mit Barquittung und in einer Bareinnahmenliste erfasst. Das ist zeitaufwändig und
fehleranfällig: Doppelte Datenerfassung, fehlende Übersicht über freie Plätze, keine
einheitliche Kurspräsentation und manuelle Belegnummern, die durcheinander geraten können.

## Lösungsidee
Eine öffentliche Webseite für Yoga-Kurse, -Events und -Workshops, an die ein einheitliches
Backend angeschlossen ist. Das Backend ist gleichzeitig CMS (Inhalte erfassen, formatieren,
freischalten, Medien hochladen) und Verwaltungstool (Kurse, Teilnehmer, Anmeldungen,
Warteliste, Zahlungen). Teilnehmer/innen melden sich ohne eigenen Account über die Webseite
an; das System erkennt bereits bekannte E-Mail-Adressen und pflegt keine doppelten
Teilnehmerstammdaten. Kurse haben eine maximale Teilnehmerzahl; darüber hinaige Anmeldungen
landen automatisch auf einer Warteliste. Für Zahlungen werden Barzahlungen und Überweisungen
getrennt erfasst, mit automatisch fortlaufenden, unveränderlichen Belegnummern.

## Scope
- **In Scope:**
  - Öffentliche Webseite mit mehreren Rubriken (Kurse, Events, Workshops, Impressum, AGB,
    Datenschutz), basierend auf dem vorhandenen Design-Entwurf `homepage-entwurf.html`, aber
    als mehseitige Site statt One-Pager.
  - CMS-Funktionen im Backend: Kurse/Events/Workshops anlegen, Texte formatieren,
    Veröffentlichungsstatus steuern, Bilder/Dateien hochladen.
  - Kalender-/Übersicht im Backend für die Kursplanung.
  - Teilnehmerverwaltung mit Name, Adresse und optionalen Gesundheitsinformationen.
  - Anmeldung über die Webseite ohne Benutzer-Account; E-Mail-Adresse als Erkennungsmerkmal.
  - Deduplizierung von Teilnehmerstammdaten anhand der E-Mail-Adresse.
  - Kursverwaltung mit maximaler Teilnehmerzahl und automatischer Warteliste.
  - E-Mail-Benachrichtigungen an Teilnehmer/innen bei erfolgreicher Anmeldung und bei
    Wartelistenstatus.
  - Zahlungsverwaltung: Barzahlungen und Überweisungen mit Angabe der Zahlungsart bei der
    Anmeldung.
  - Fortlaufende, unveränderliche Belegnummern: Bareinnahmenbeleg für Barzahlungen,
    Rechnungsnummer für Überweisungen.
  - Ausdruck von Zahlungsbelegen/Rechnungen aus dem System.
  - Rollen/Rights-Grundgerüst für den Fall, dass später Helfer/innen mit eingeschränkten
    Rechten arbeiten.

- **Out of Scope:**
  - Online-Zahlung per Kreditkarte/PayPal/etc.
  - Abonnements, Mitgliedschaften oder wiederkehrende Lastschrift.
  - Mehrsprachigkeit der Webseite (zunächst nur Deutsch).
  - Integration mit externen Kalendersystemen oder Buchhaltungsprogrammen.
  - Automatische Social-Media-Posts.
  - Gutscheinverwaltung (eigene Idee I2, mittelfristig).

## Auswirkungen auf den Bestand
- **Specs:**
  - `specs/E1 Öffentliche Webseite/` mit F1 Seiten und Navigation, F2 Veranstaltungsdarstellung,
    F3 Online-Anmeldung
  - `specs/E2 CMS & Verwaltung/` mit F1 Veranstaltungen verwalten, F2 Teilnehmer verwalten,
    F3 Anmeldungen und Warteliste verwalten, F4 Zahlungen und Belege verwalten,
    F5 Ausgehende Nachrichten
  - `specs/guidelines/_Menüstruktur.md` für die Verwaltungsnavigation
- **Datenstruktur:** Neue Entitäten: Course, Event, Workshop, Participant, Registration,
  WaitingList, Payment, CashReceipt, Invoice. Verbindung zu bestehenden Dateien noch keine
  vorhanden; Domänenwissen in `projekt/_domaene.md` muss ergänzt werden.
- **Backend:** Neue Module oder Modulgruppen: Webseite (CMS) und Verwaltung. Vorgänge wie
  „Anmeldung erstellen", „Zahlung erfassen", „Belegnummer vergeben", „Kurs anlegen",
  „Stornierung mit Gutschrift/Rückgabebeleg", „Warteliste nachrücken", „E-Mail versenden".
- **Quellcode:** Berührungspunkte unterhalb `modules/` (Backend) und `resources/views/`
  (Frontend); Risiko bei fortlaufenden Belegnummern in Mehrbenutzer-Szenarien, obwohl zunächst
  Ein-Personen-Betrieb.

## Entscheidungen
- 02.09.2026: PROJEKT.md angelegt; Idee I1 reserviert (manuell, da noch kein Git-Repository).
- 02.09.2026: Webseite wird mehseitige Site, kein One-Pager.
- 02.09.2026: CMS und Verwaltung sollen in einem Backend liegen, nicht getrennt.
- 02.09.2026: Rollen/Rechte-Grundgerüst wird vorgesehen, obwohl aktuell nur eine Person das
  System nutzt.
- 02.09.2026: Teilnehmer erkennen über E-Mail-Adresse, kein Benutzer-Account nötig.
- 02.09.2026: Stornierungen werden ausschließlich über das Backend vorgenommen; Teilnehmer
  können nicht selbst stornieren. Nach einer Stornierung rückt der erste Wartelistenplatz
  automatisch nach und wird per E-Mail informiert.
- 02.09.2026: Belegnummern werden pro Jahr vergeben mit Präfix: `B-YYYY-NNNNN` für
  Bareinnahmenbelege, `R-YYYY-NNNNN` für Rechnungen.
- 02.09.2026: Kurse, Events und Workshops werden als einheitliche Entität „Veranstaltung"
  (Event/Activity) mit Typ-Unterscheidung modelliert; Kurse haben mehrere Termine, Events und
  Workshops in der Regel einen.
- 02.09.2026: Rechnungen für Überweisungen werden automatisch bei Anmeldung erstellt und per
  E-Mail versendet; Barzahlungen werden im Backend mit Bareinnahmenbeleg erfasst.
- 02.09.2026: Anmeldungen gelten unabhängig vom Zahlungseingang als verbindlich.
- 02.09.2026: Kurse können aus Vorlagen mit automatisch berechneten Terminen erzeugt werden
  (Wochentag, Uhrzeit, Anzahl Termine).
- 02.09.2026: Die Webseite zeigt nur zukünftige, veröffentlichte Veranstaltungen;
  ausgebuchte Kurse bleiben sichtbar mit Hinweis „Warteliste".
- 02.09.2026: Gesundheitsinformationen sind optional; sie werden nur bei aktiver Zustimmung
  erfasst, nicht in allgemeinen Listen angezeigt und nicht per E-Mail versendet.
- 02.09.2026: E-Mails werden direkt per SMTP aus dem System versendet; als Fallback gibt es
  eine Übersicht „Ausgehende Nachrichten" zum erneuten manuellen Senden.
- 02.09.2026: Kostenlose Veranstaltungen (Preis 0) werden unterstützt; Zahlungsauswahl und
  Belegerzeugung entfallen dann.
- 02.09.2026: Stornierungen/Rückzahlungen werden im System erfasst und erhalten einen eigenen
  lückenlosen, unveränderlichen Nummernkreis: `G-YYYY-NNNNN` für Gutschriften
  (Überweisungen), `RB-YYYY-NNNNN` für Rückgabebestätigungen (Barzahlungen).
- 02.09.2026: Teilnehmer/innen können ihre Daten ohne Account nicht selbst ändern;
  Bearbeitung geschieht im Backend. Eine Bearbeitung per Magic-Link kommt ins Backlog.
- 02.09.2026: Gesundheitsinformationen werden bei wiederholter Anmeldung vorausgefüllt, aber
  vom Teilnehmer bearbeitbar.
- 02.09.2026: Gutscheine bleiben für I1 außerhalb des Scopes und werden später als eigene
  Idee behandelt.

## Offene Punkte
- (keine — State Ready)
