#!/bin/bash
set -e

PROJECT_ROOT="/var/www/html"
APP_ROOT="${PROJECT_ROOT}/src"
ENV_FILE="${APP_ROOT}/.env"

cd "${PROJECT_ROOT}"

# .env aus Umgebungsvariablen erzeugen, falls noch nicht vorhanden
if [ ! -f "${ENV_FILE}" ]; then
    cat > "${ENV_FILE}" <<EOF
APP_NAME="${APP_NAME:-Yoga Sabrina Becker}"
APP_ENV="${APP_ENV:-local}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-true}"
APP_URL="${APP_URL:-http://localhost:8080}"
APP_LOCALE="${APP_LOCALE:-de}"
APP_FALLBACK_LOCALE="${APP_FALLBACK_LOCALE:-en}"
APP_FAKER_LOCALE="${APP_FAKER_LOCALE:-de_DE}"
APP_MAINTENANCE_DRIVER="file"
BCRYPT_ROUNDS="${BCRYPT_ROUNDS:-12}"
LOG_CHANNEL="${LOG_CHANNEL:-stack}"
LOG_LEVEL="${LOG_LEVEL:-debug}"
DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-yoga}"
DB_USERNAME="${DB_USERNAME:-yoga}"
DB_PASSWORD="${DB_PASSWORD:-yoga}"
SESSION_DRIVER="${SESSION_DRIVER:-database}"
SESSION_LIFETIME="${SESSION_LIFETIME:-120}"
BROADCAST_CONNECTION="log"
FILESYSTEM_DISK="local"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"
CACHE_STORE="${CACHE_STORE:-database}"
MAIL_MAILER="${MAIL_MAILER:-log}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-info@yoga-sabrina-becker.example}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Yoga Sabrina Becker}"
VITE_APP_NAME="\${APP_NAME}"
EOF
fi

# App-Key generieren, falls noch nicht vorhanden
if ! grep -qE '^APP_KEY=base64:' "${ENV_FILE}" 2>/dev/null; then
    cd "${APP_ROOT}" && php artisan key:generate --ansi
fi

# Auf Datenbank warten
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-yoga}', '${DB_USERNAME:-yoga}', '${DB_PASSWORD:-yoga}');" 2>/dev/null; do
    echo "Warte auf Datenbank ${DB_HOST:-db}..."
    sleep 2
done

# Migrationen anwenden
cd "${APP_ROOT}" && php artisan migrate --force --ansi

# Standard-Admin-Benutzer anlegen (via DatabaseSeeder)
if [ "${DOCKER_SEED_ADMIN:-true}" = "true" ]; then
    cd "${APP_ROOT}" && php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder --force --ansi || true
fi

# Demo-Daten seeden, falls gewünscht
if [ "${DOCKER_SEED_DEMO:-false}" = "true" ]; then
    cd "${APP_ROOT}" && php artisan db:seed --class=Database\\Seeders\\DemoSeeder --force --ansi || true
fi

# Berechtigungen sicherstellen
chown -R www-data:www-data "${APP_ROOT}/storage" "${APP_ROOT}/bootstrap/cache"
chmod -R 775 "${APP_ROOT}/storage" "${APP_ROOT}/bootstrap/cache"

echo "Yoga Sabrina Becker ist bereit unter ${APP_URL:-http://localhost:8080}"
echo "Login: ${DOCKER_ADMIN_EMAIL:-sabrina@example.com} / ${DOCKER_ADMIN_PASSWORD:-yoga2026}"
exec "$@"
