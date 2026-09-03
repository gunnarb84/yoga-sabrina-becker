> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [PROJEKT.md](../../../PROJEKT.md)
> **Stand:** 2026-09-03

# Projektstruktur

```
yoga_sabrina_becker/
├── docs/                       # Dokumentation
│   ├── user/                   # Endkunden-Doku
│   └── dev/                    # Entwicklerdoku
├── harness-php/                # Projektneutraler Harness (geschützt)
├── ideas/                      # Produktideen
├── modules/                    # Fachmodule
│   ├── Verwaltung/             # CMS und Verwaltung
│   └── Webseite/               # Öffentliche Webseite
├── platform/                   # Plattformbausteine
│   ├── Identity/               # UUID-Generierung und -Casts
│   ├── NumberSequence/         # fortlaufende Nummern
│   └── Shared/                 # gemeinsame Wertobjekte und Hilfen
├── projekt/                    # Projektspezifische Ergänzungen
│   └── _domaene.md             # Domänenwissen
├── specs/                      # Anforderungen und Akzeptanzkriterien
├── src/                        # Laravel-Anwendung
│   ├── app/                    # App-Models, Provider, globale HTTP-Schicht
│   ├── bootstrap/
│   ├── config/
│   ├── database/               # Migrationen, Factories, Seeders
│   ├── public/                 # Einstiegspunkt und statische Assets
│   ├── resources/              # Views, CSS, JS
│   ├── routes/                 # Routen (Web + Verwaltung)
│   └── tests/                  # Pest-Tests (Feature + Unit)
├── tests/                      # Weitere Tests, falls erforderlich
├── tests-e2e/                  # Playwright-Oberflächentests
├── vendor/                     # Composer-Abhängigkeiten
└── PROJEKT.md                  # Projektprofil
```

## Siehe auch

- [Setup](../setup.md)
- [Module und Schichten](module.md)
