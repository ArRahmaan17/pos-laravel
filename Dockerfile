# ==============================================================================
# Stage 1: Node — Build Vite / Tailwind CSS Assets
# ==============================================================================
FROM node:22-alpine AS node-builder

WORKDIR /build

# Install JS dependencies
COPY package.json package-lock.json ./
RUN npm ci --prefer-offline

# Copy source files needed by Vite
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Build production assets
RUN npm run build

# ==============================================================================
# Stage 2: Composer — Install PHP Dependencies
# ==============================================================================
FROM composer:2 AS composer-builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

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
COPY --chown=www-data:www-data . .

# ── Bring in vendor from composer stage ──────────────────────────────────────
COPY --from=composer-builder --chown=www-data:www-data /app/vendor ./vendor

# ── Bring in compiled assets from node stage ─────────────────────────────────
COPY --from=node-builder --chown=www-data:www-data /build/public/build ./public/build

# ── CaddyFile (FrankenPHP embedded Caddy config) ─────────────────────────────
COPY --chown=www-data:www-data CaddyFile /etc/frankenphp/Caddyfile

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
COPY --chown=root:root docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp"]
