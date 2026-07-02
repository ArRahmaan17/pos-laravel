#!/usr/bin/env bash
set -euo pipefail

if [ "$1" != "frankenphp" ]; then
    exec "$@"
fi

RUN_DB_INIT="${RUN_DB_INIT:-false}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-false}"
RESET_DB_ON_BOOT="${RESET_DB_ON_BOOT:-false}"
SEED_DB_ON_BOOT="${SEED_DB_ON_BOOT:-false}"
INSTALL_DEPENDENCIES="${INSTALL_DEPENDENCIES:-false}"
START_REVERB="${START_REVERB:-true}"

if [ "$INSTALL_DEPENDENCIES" = "true" ]; then
    manifest_hash="$(sha256sum composer.json | cut -d ' ' -f 1)"
    installed_hash="$(cat vendor/.composer-manifest.sha256 2>/dev/null || true)"

    if [ ! -f vendor/autoload.php ] || [ "$manifest_hash" != "$installed_hash" ]; then
        echo "Synchronizing Composer dependencies..."
        COMPOSER_CACHE_DIR="${COMPOSER_CACHE_DIR:-/tmp/composer-cache}" \
            composer update --no-interaction --prefer-dist
        printf '%s\n' "$manifest_hash" > vendor/.composer-manifest.sha256
    fi
fi

mkdir -p \
    bootstrap/cache \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

if [ "$RUN_DB_INIT" = "true" ]; then
    echo "Ensuring database exists..."
    frankenphp php-cli database/ensure_db.php
fi

if [ "${APP_ENV:-local}" = "production" ]; then
    echo "Optimizing Laravel caches..."
    frankenphp php-cli artisan optimize
else
    frankenphp php-cli artisan optimize:clear
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
    if [ "$RESET_DB_ON_BOOT" = "true" ]; then
        echo "Resetting and seeding the database..."
        frankenphp php-cli artisan migrate:fresh --seed --force
    elif [ "$SEED_DB_ON_BOOT" = "true" ]; then
        echo "Running migrations and seeders..."
        frankenphp php-cli artisan migrate --seed --force
    else
        echo "Running migrations..."
        frankenphp php-cli artisan migrate --force
    fi
fi

if [ "$START_REVERB" != "true" ]; then
    exec frankenphp run -c /etc/frankenphp/Caddyfile --adapter caddyfile
fi

frankenphp run -c /etc/frankenphp/Caddyfile --adapter caddyfile &
caddy_pid=$!
frankenphp php-cli artisan reverb:start \
    --host="${REVERB_SERVER_HOST:-0.0.0.0}" \
    --port="${REVERB_SERVER_PORT:-81}" \
    --no-interaction &
reverb_pid=$!

shutdown() {
    kill "$caddy_pid" "$reverb_pid" 2>/dev/null || true
}

trap shutdown INT TERM EXIT
wait -n "$caddy_pid" "$reverb_pid"
