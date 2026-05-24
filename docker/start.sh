#!/bin/sh
set -e

# ── 1. Write .env from environment variables ──────────────────────────────────
# Laravel's artisan commands require a .env file to be present even when all
# values are already injected as real environment variables by Render.
echo "==> Writing .env from environment variables..."
cat > /var/www/html/.env <<EOF
APP_NAME="${APP_NAME:-Wasla Connect}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-wasla_connect}
DB_USERNAME=${DB_USERNAME:-}
DB_PASSWORD=${DB_PASSWORD:-}
DB_SSLMODE=${DB_SSLMODE:-require}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
EOF

# ── 2. Nginx config ────────────────────────────────────────────────────────────
echo "==> Configuring Nginx (PORT=${PORT:-80})..."
envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ── 3. Validate APP_KEY ────────────────────────────────────────────────────────
# Render's generateValue produces a plain random string, not the base64: format
# Laravel requires. Detect and replace it so encryption never fails.
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "==> APP_KEY missing or invalid format — generating a proper key..."
    php artisan key:generate --force
    # Refresh .env with the newly generated key
    NEW_KEY=$(grep "^APP_KEY=" /var/www/html/.env | cut -d'=' -f2-)
    echo "==> Generated key: ${NEW_KEY}"
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
