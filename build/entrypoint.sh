#!/bin/bash
set -e

if [ "$1" = "frankenphp" ]; then
# Check if vendor folder exists, if not run composer install
    sed -i "s/your_awesome_application_port/${NGINX_APP_PORT}/" /etc/frankenphp/Caddyfile
    # Ensure database exists
    echo "Checking database status..."
    frankenphp php-cli /var/www/html/database/ensure_db.php

    # remove old cache
    echo "Clearing old Laravel cache..."
    frankenphp php-cli artisan config:clear
    frankenphp php-cli artisan event:clear
    frankenphp php-cli artisan route:clear
    frankenphp php-cli artisan view:clear
    frankenphp php-cli artisan key:generate

    # Run migrations safely
    if [ "${APP_ENV}" = "production" ]; then
        echo "Running production migrations..."
        frankenphp php-cli artisan migrate --force
    else
        echo "Running development migrations (migrate:fresh --seed)..."
        frankenphp php-cli artisan migrate:fresh --seed
    fi

    # Start FrankenPHP and Reverb server
    echo "Starting FrankenPHP & Reverb"
    exec frankenphp run -c /etc/frankenphp/Caddyfile --adapter caddyfile & frankenphp php-cli artisan reverb:start --host=0.0.0.0 --port=8001 --no-interaction
else
    exec "$@"
fi

