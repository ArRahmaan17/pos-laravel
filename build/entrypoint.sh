#!/bin/bash
set -euo pipefail

if [ "$1" = "frankenphp" ]; then
    NGINX_APP_PORT="${NGINX_APP_PORT:-80}"
    RUN_DB_INIT="${RUN_DB_INIT:-false}"
    RUN_MIGRATIONS="${RUN_MIGRATIONS:-false}"
    RESET_DB_ON_BOOT="${RESET_DB_ON_BOOT:-false}"
    SEED_DB_ON_BOOT="${SEED_DB_ON_BOOT:-false}"

    sed -i "s/your_awesome_application_port/${APP_PORT}/g" /etc/frankenphp/Caddyfile

    if [ "${RUN_DB_INIT}" = "true" ]; then
        echo "Ensuring database exists..."
        frankenphp php-cli /var/www/html/database/ensure_db.php
    fi

    if [ "${APP_ENV:-local}" = "production" ]; then
        if [ ! -f bootstrap/cache/config.php ] || [ ! -f bootstrap/cache/routes-v7.php ] || [ ! -f bootstrap/cache/events.php ]; then
            echo "Building Laravel caches..."
            frankenphp php-cli artisan optimize
        fi
    fi

    if [ "${RUN_MIGRATIONS}" = "true" ]; then
        if [ "${RESET_DB_ON_BOOT}" = "true" ]; then
            echo "Resetting database and seeding..."
            frankenphp php-cli artisan migrate:fresh --seed --force
        elif [ "${SEED_DB_ON_BOOT}" = "true" ]; then
            echo "Running migrations and seeders..."
            frankenphp php-cli artisan migrate --seed --force
        else
            echo "Running migrations..."
            frankenphp php-cli artisan migrate --force
        fi
    fi

    exec frankenphp run -c /etc/frankenphp/Caddyfile --adapter caddyfile & 
    frankenphp php-cli artisan reverb:start --host=0.0.0.0 --port=8001 --no-interaction
else
    exec "$@"
fi
