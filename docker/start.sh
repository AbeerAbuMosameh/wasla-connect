#!/bin/sh
set -e

# ── 1. Write .env from environment variables ──────────────────────────────────
# printf keeps special characters in values ($ ! \ in DB passwords) literal.
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
echo "==> .env written."

# ── 2. Nginx config ────────────────────────────────────────────────────────────
echo "==> Configuring Nginx (PORT=${PORT:-80})..."
envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ── 3. Validate APP_KEY ────────────────────────────────────────────────────────
# Render's generateValue produces a plain random string, not the base64: format
# Laravel requires for encryption.
#
# IMPORTANT: after key:generate updates .env, we must also export the new key
# into the shell environment. Otherwise `php artisan optimize` (next step) reads
# APP_KEY from the stale environment variable, caches the wrong key, and every
# HTTP request fails decryption with a 500.
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "==> APP_KEY missing or invalid — generating..."
    php artisan key:generate --force
    APP_KEY=$(grep "^APP_KEY=" /var/www/html/.env | cut -d'=' -f2- | tr -d '"')
    export APP_KEY
    echo "==> APP_KEY generated and exported: ${APP_KEY:0:16}..."
else
    echo "==> APP_KEY is valid."
fi

# ── 4. Cache config, routes, views ────────────────────────────────────────────
echo "==> Clearing stale caches..."
php artisan optimize:clear

echo "==> Building cache (uses exported APP_KEY)..."
php artisan optimize

# ── 5. Fix permissions so PHP-FPM (www-data) can write to storage ─────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ── 6. Migrations ─────────────────────────────────────────────────────────────
echo "==> Running database migrations..."
php artisan migrate --force

# ── 7. Start services ─────────────────────────────────────────────────────────
echo "==> Starting PHP-FPM and Nginx..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
