> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [harness-php/_integration.md](../../../harness-php/_integration.md)
> **Stand:** 2026-09-08

# Schnittstellen

## Interne Web-Routen

- `/` – Startseite (`webseite.home`)
- `/veranstaltungen` – Veranstaltungsübersicht (`webseite.activities.index`)
- `/veranstaltungen/{slug}` – Veranstaltungsdetail (`webseite.activities.show`)
- `/veranstaltungen/{slug}/anmelden` – Anmeldeseite (`webseite.activities.register`)
- `/kontakt` – Kontaktseite mit Anfrageformular (`contact`)
- `/verwaltung/login` – Backend-Login (`verwaltung.login`)
- `/verwaltung/...` – Verwaltungsbereich, darunter `/verwaltung/kontaktanfragen`
  (`verwaltung.contact-inquiries`) und `/verwaltung/kontaktanfragen/{id}`
  (`verwaltung.contact-inquiry.detail`)

## Integrations-API

Zurzeit wird keine öffentliche Integrations-API bereitgestellt. E-Mail-Versand an Teilnehmer/innen erfolgt über Laravel-Mail mit `outbound_messages` als Sendeprotokoll.

## Siehe auch

- [Datenmodell](datenmodell.md)
- [Module und Schichten](module.md)
