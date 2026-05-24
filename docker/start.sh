#!/bin/sh
set -e

echo "==> Substituting PORT=${PORT} into Nginx config..."
envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo "==> Caching Laravel config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Starting PHP-FPM and Nginx via Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
