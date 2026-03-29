# ==============================================================================
# Stage 1: Node — Build Vite / Tailwind CSS Assets
# ==============================================================================
FROM node:25-alpine AS node-builder

WORKDIR /build

# Install JS dependencies
COPY application/package.json application/package-lock.json ./
RUN npm ci --prefer-offline

# Copy source files needed by Vite
COPY application/vite.config.js ./
COPY application/resources/ ./resources/
COPY application/public/ ./public/

# Build production assets
RUN npm run build

# ==============================================================================
# Stage 2: Composer — Install PHP Dependencies
# ==============================================================================
FROM composer:2 AS composer-builder

# ==============================================================================
# Stage 3: FrankenPHP — Production Image
# ==============================================================================
FROM dunglas/frankenphp:php8.4-alpine AS app

LABEL maintainer="pos-laravel"
LABEL description="POS Laravel — FrankenPHP + Caddy"

# ── System dependencies ──────────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    zlib-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev

# ── PHP Extensions ────────────────────────────────────────────────────────────
RUN install-php-extensions \
    pdo \
    pdo_mysql \
    redis \
    gd \
    intl \
    zip \
    bcmath \
    pcntl \
    opcache \
    mbstring \
    exif \
    fileinfo

# ── OPcache tuning ────────────────────────────────────────────────────────────
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# ── Working directory ─────────────────────────────────────────────────────────
WORKDIR /var/www/html
# ── Copy application source ───────────────────────────────────────────────────
COPY --chown=www-data:www-data application/ .

# ── Remove stale cache files and vendor ──────────────────────────────────────────────────
RUN rm -f bootstrap/cache/*.php

# ── Bring in vendor from composer stage ──────────────────────────────────────
COPY --from=composer-builder --chown=www-data:www-data /usr/bin/composer /usr/bin/composer

# ── Bring in compiled assets from node stage ─────────────────────────────────
COPY --from=node-builder --chown=www-data:www-data /build/public/build ./public/build

# ── CaddyFile (FrankenPHP embedded Caddy config) ─────────────────────────────
COPY --chown=www-data:www-data application/example.CaddyFile /etc/frankenphp/Caddyfile

# ── Storage & cache directories ───────────────────────────────────────────────
RUN mkdir -p \
        storage/logs/frankendata \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY --chown=root:root build/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp"]
