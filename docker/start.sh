#!/bin/sh
set -e

echo "==> Configuring Nginx (PORT=${PORT})..."
envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Render's generateValue produces a plain random string, not a valid Laravel
# base64: key. Detect and replace it before anything else runs.
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "==> APP_KEY missing or invalid format — generating a proper key..."
    php artisan key:generate --force
else
    echo "==> APP_KEY is valid."
fi

echo "==> Clearing any stale caches from previous deploys..."
php artisan optimize:clear

echo "==> Caching config, routes, and views..."
php artisan optimize

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Starting PHP-FPM and Nginx..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
