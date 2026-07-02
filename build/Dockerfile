# syntax=docker/dockerfile:1.7

FROM node:22-alpine AS frontend-development

WORKDIR /var/www/html

COPY build/frontend-entrypoint.sh /usr/local/bin/frontend-entrypoint.sh

RUN chmod +x /usr/local/bin/frontend-entrypoint.sh \
    && mkdir -p /var/www/html/node_modules /home/node/.npm \
    && chown -R node:node /var/www/html /home/node/.npm

USER node

EXPOSE 5173

ENTRYPOINT ["/usr/local/bin/frontend-entrypoint.sh"]


FROM dunglas/frankenphp:php8.4-alpine AS php-base

RUN apk add --no-cache \
        bash \
        curl \
        git \
        unzip \
        zip \
    && install-php-extensions \
        bcmath \
        exif \
        gd \
        intl \
        pcntl \
        pdo_mysql \
        zip

WORKDIR /var/www/html


FROM php-base AS development

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY build/php/opcache.dev.ini /usr/local/etc/php/conf.d/opcache.ini
COPY application/example.CaddyFile /etc/frankenphp/Caddyfile
COPY build/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs

EXPOSE 8000 81

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp"]


FROM composer:2 AS composer-builder

WORKDIR /app

COPY application/composer.json ./

RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer update \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --optimize-autoloader \
        --prefer-dist \
        --ignore-platform-reqs


FROM node:22-alpine AS frontend-builder

WORKDIR /build

COPY application/package.json application/.npmrc ./

RUN --mount=type=cache,target=/root/.npm \
    npm install --prefer-offline

COPY application/ ./

RUN npm run build


FROM php-base AS app

COPY build/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY --chown=www-data:www-data application/ ./
COPY --from=composer-builder --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend-builder --chown=www-data:www-data /build/public/build ./public/build
COPY application/example.CaddyFile /etc/frankenphp/Caddyfile
COPY build/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && rm -f bootstrap/cache/*.php \
    && mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data bootstrap/cache storage \
    && chmod -R 775 bootstrap/cache storage

EXPOSE 8000 81

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp"]
