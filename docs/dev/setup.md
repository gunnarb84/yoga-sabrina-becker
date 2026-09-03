> **Typ:** How-To · **Für:** Entwickler · **Bezug:** [harness-php/_operations.md](../../../harness-php/_operations.md)
> **Stand:** 2026-09-03

# Setup

Diese Anleitung beschreibt die lokale Entwicklungsumgebung.

## Voraussetzungen

- PHP 8.3+ mit den üblichen Laravel-Erweiterungen (`pdo_mysql`, `mbstring`, `openssl`, `xml`, `ctype`, `json`, `tokenizer`, `fileinfo`)
- Composer 2.x
- MySQL 8 oder MariaDB 10.6+
- Node.js 20+ und npm
- Ein Unix-artiges Betriebssystem (macOS, Linux) oder WSL

## Schritte

1. **Repository klonen**
   ```bash
   git clone <repo-url> yoga_sabrina_becker
   cd yoga_sabrina_becker
   ```

2. **PHP-Abhängigkeiten installieren**
   ```bash
   cd src
   composer install
   cd ..
   ```

3. **Umgebungsvariablen anlegen**
   ```bash
   cp src/.env.example src/.env
   ```
   Tragen Sie in `src/.env` mindestens ein:
   - `APP_KEY` (generieren Sie ihn mit `cd src && php artisan key:generate`)
   - `DB_CONNECTION=mysql`
   - `DB_DATABASE=yoga`, `DB_USERNAME`, `DB_PASSWORD`
   - `APP_URL=http://localhost:8000`
   - `APP_LOCALE=de`

4. **Datenbanken anlegen**
   Erstellen Sie die Datenbanken `yoga` (Entwicklung), `yoga_test` (Pest-Tests) und `yoga_e2e` (Playwright-Tests):
   ```sql
   CREATE DATABASE yoga CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE yoga_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE yoga_e2e CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Migrationen und Seeder ausführen**
   ```bash
   cd src
   php artisan migrate --seed
   cd ..
   ```

6. **Frontend-Assets bauen**
   Falls das Projekt Vite nutzt:
   ```bash
   npm install
   npm run build
   ```

7. **Entwicklungsserver starten**
   ```bash
   cd src
   php artisan serve --port=8000
   ```
   Die Anwendung ist unter `http://localhost:8000` erreichbar. Die Verwaltung liegt unter `/verwaltung`.

## Tests ausführen

Siehe [Tests ausführen](how-to/tests-ausfuehren.md).

## Siehe auch

- [Konfiguration und Umgebung](referenz/konfiguration.md)
- [Build und Ausrollen](referenz/build-und-ausrollen.md)
