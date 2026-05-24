#!/bin/sh
set -e

# ── 1. Build .env from environment variables ──────────────────────────────────
# Must happen before any artisan command. Using printf so special characters
# in values (e.g. $ ! \ in DB passwords) are written literally without
# shell re-expansion.
echo "==> Writing .env from environment variables..."
{
    printf 'APP_NAME="%s"\n'         "${APP_NAME:-Wasla Connect}"
    printf 'APP_ENV="%s"\n'          "${APP_ENV:-production}"
    printf 'APP_KEY="%s"\n'          "${APP_KEY:-}"
    printf 'APP_DEBUG="%s"\n'        "${APP_DEBUG:-false}"
    printf 'APP_URL="%s"\n'          "${APP_URL:-http://localhost}"
    printf '\n'
    printf 'LOG_CHANNEL="%s"\n'      "${LOG_CHANNEL:-stderr}"
    printf 'LOG_LEVEL="%s"\n'        "${LOG_LEVEL:-error}"
    printf '\n'
    printf 'DB_CONNECTION="%s"\n'    "${DB_CONNECTION:-pgsql}"
    printf 'DB_HOST="%s"\n'          "${DB_HOST:-127.0.0.1}"
    printf 'DB_PORT="%s"\n'          "${DB_PORT:-5432}"
    printf 'DB_DATABASE="%s"\n'      "${DB_DATABASE:-wasla_connect}"
    printf 'DB_USERNAME="%s"\n'      "${DB_USERNAME:-}"
    printf 'DB_PASSWORD="%s"\n'      "${DB_PASSWORD:-}"
    printf 'DB_SSLMODE="%s"\n'       "${DB_SSLMODE:-require}"
    printf '\n'
    printf 'SESSION_DRIVER="%s"\n'   "${SESSION_DRIVER:-database}"
    printf 'SESSION_LIFETIME="%s"\n' "${SESSION_LIFETIME:-120}"
    printf '\n'
    printf 'CACHE_STORE="%s"\n'      "${CACHE_STORE:-file}"
    printf 'QUEUE_CONNECTION="%s"\n' "${QUEUE_CONNECTION:-sync}"
} > /var/www/html/.env

echo "==> .env written successfully."

# ── 2. Nginx config ────────────────────────────────────────────────────────────
echo "==> Configuring Nginx (PORT=${PORT:-80})..."
envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ── 3. Validate APP_KEY ────────────────────────────────────────────────────────
# Render's generateValue produces a plain random string, not the base64: format
# Laravel requires. Detect and replace before caching anything.
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "==> APP_KEY invalid — generating a proper key..."
    php artisan key:generate --force
else
    echo "==> APP_KEY is valid."
fi

# ── 4. Clear stale caches, then rebuild ───────────────────────────────────────
echo "==> Clearing stale caches..."
php artisan optimize:clear

echo "==> Caching config, routes, and views..."
php artisan optimize

# ── 5. Migrations ─────────────────────────────────────────────────────────────
echo "==> Running database migrations..."
php artisan migrate --force

# ── 6. Start services ─────────────────────────────────────────────────────────
echo "==> Starting PHP-FPM and Nginx..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
