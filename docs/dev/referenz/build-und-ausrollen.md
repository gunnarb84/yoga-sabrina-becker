> **Typ:** Referenz · **Für:** Entwickler · **Bezug:** [harness-php/_operations.md](../../../harness-php/_operations.md)
> **Stand:** 2026-09-03

# Build und Ausrollen

## Voraussetzungen

- SSH oder Dateizugriff auf den Zielserver
- PHP 8.3+, MySQL/MariaDB, Composer, Node.js (für Assets)

## Schritte

1. **Code ausrollen**
   ```bash
   git pull origin main
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

2. **Migrationen ausführen**
   ```bash
   cd src
   php artisan migrate --force
   ```

3. **Optimierungen**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Warteschlange**
   Betreiben Sie mindestens einen Queue-Worker für `QUEUE_CONNECTION=database`:
   ```bash
   php artisan queue:work --sleep=3 --tries=3
   ```

## Rückkehr zur Vorversion

Falls ein Release Probleme bereitet, führen Sie die Migrationen nicht aus, setzen Sie den Code auf den letzten stabilen Commit zurück und leeren Sie die Caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Siehe auch

- [Setup](../setup.md)
- [Konfiguration und Umgebung](konfiguration.md)
