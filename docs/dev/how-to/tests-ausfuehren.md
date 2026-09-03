> **Typ:** How-To · **Für:** Entwickler · **Bezug:** [harness-php/_test-strategy.md](../../../harness-php/_test-strategy.md), [harness-php/_test-api.md](../../../harness-php/_test-api.md), [harness-php/_test-ui.md](../../../harness-php/_test-ui.md)
> **Stand:** 2026-09-03

# Tests ausführen

## Statische Richtlinienprüfung

```bash
python3 harness-php/analyse_all.py --fail-on-violations
```

Dies prüft PHPStan, Pint und Deptrac.

## Pest-Tests

Stellen Sie sicher, dass die Datenbank `yoga_test` existiert. In `src/phpunit.xml` ist `DB_DATABASE=yoga_test` konfiguriert.

```bash
php vendor/bin/pest --configuration=src/phpunit.xml
```

Das Kommando muss vom Projekt-Wurzelverzeichnis ausgeführt werden, damit die Pfade in `phpunit.xml` korrekt aufgelöst werden.

## Playwright-Tests

Stellen Sie sicher, dass die Datenbank `yoga_e2e` existiert und npm-Abhängigkeiten installiert sind:

```bash
cd tests-e2e
npm install
npx playwright install chromium
npm run e2e
```

## Reihenfolge vor einem Commit

1. `python3 harness-php/analyse_all.py --fail-on-violations`
2. `php vendor/bin/pest --configuration=src/phpunit.xml`
3. `cd tests-e2e && npm run e2e`

## Siehe auch

- [Setup](../setup.md)
- [Einen neuen Vorgang anlegen](neuer-vorgang.md)
