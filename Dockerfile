# ============================================
# Stage 1: Build Frontend Assets
# ============================================
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json .npmrc ./
RUN npm ci --ignore-scripts

COPY resources/ resources/
COPY vite.config.js ./

RUN npm run build

# ============================================
# Stage 2: Install Composer Dependencies
# ============================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

# ============================================
# Stage 3: Production Image
# ============================================
FROM php:8.4-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        mbstring \
        gd \
        opcache \
        bcmath \
        pcntl \
    && rm -rf /var/cache/apk/*

# PHP production config
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Custom PHP config
COPY docker/php/custom.ini "$PHP_INI_DIR/conf.d/custom.ini"

# PHP-FPM config
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# OPcache config
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# Nginx config
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copy application files
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

COPY app/ app/
COPY bootstrap/ bootstrap/
COPY config/ config/
COPY database/ database/
COPY lang/ lang/
COPY public/ public/
COPY resources/ resources/
COPY routes/ routes/
COPY storage/ storage/
COPY artisan ./
COPY composer.json composer.lock ./

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Create required directories
RUN mkdir -p storage/app/public storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
