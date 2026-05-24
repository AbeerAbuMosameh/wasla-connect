# ─────────────────────────────────────────────
# Stage 1: Install Composer dependencies
# ─────────────────────────────────────────────
FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ─────────────────────────────────────────────
# Stage 2: Production image
# ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    libpng-dev \
    libxml2-dev \
    libpq-dev \
    sqlite \
    sqlite-dev \
    gettext

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    opcache \
    bcmath \
    xml

# OPcache tuning for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
} > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copy application source
COPY . .

# Copy compiled vendor from Stage 1
COPY --from=vendor /app/vendor ./vendor

# Storage & cache directories
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx config (uses $PORT at runtime via envsubst)
COPY docker/nginx.conf /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Render injects PORT at runtime; default to 80 locally
ENV PORT=80

EXPOSE ${PORT}

CMD ["/start.sh"]
