> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [harness-php/_operations.md](../../../harness-php/_operations.md)
> **Stand:** 2026-09-03

# Konfiguration und Umgebung

Die Anwendung wird über `src/.env` konfiguriert. Mindestens erforderlich sind:

| Variable | Bedeutung |
|---|---|
| `APP_KEY` | Laravel-App-Key (mindestens 32 Zeichen) |
| `APP_URL` | Öffentliche URL der Installation |
| `APP_LOCALE` | `de` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST`, `DB_PORT` | Datenbank-Host |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Datenbankzugang |
| `MAIL_MAILER` | `log` für lokale Tests, sonst SMTP |
| `QUEUE_CONNECTION` | `database` |
| `CACHE_STORE` | `database` |
| `SESSION_DRIVER` | `database` |

## Test-Datenbanken

- `yoga_test` für Pest-Tests (`phpunit.xml`).
- `yoga_e2e` für Playwright-Tests (`tests-e2e/playwright.config.ts`).

## Siehe auch

- [Setup](../setup.md)
- [Build und Ausrollen](build-und-ausrollen.md)
