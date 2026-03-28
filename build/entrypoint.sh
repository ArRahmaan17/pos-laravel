#!/bin/bash
set -e

if [ "$1" = "frankenphp" ]; then
    sed -i "s/your_awesome_application_port/${APP_PORT}/" /etc/frankenphp/Caddyfile
    # Ensure database exists
    echo "Checking database status..."
    frankenphp php-cli /var/www/html/database/ensure_db.php

    # remove old cache
    echo "Remove old optimizing laravel..."
    frankenphp php-cli artisan config:clear
    frankenphp php-cli artisan event:clear
    frankenphp php-cli artisan route:clear
    frankenphp php-cli artisan view:clear

    # Run migrations
    echo "Running migrations..."
    frankenphp php-cli artisan migrate:fresh --seed

    # Discover packages and cache configuration
    echo "Optimizing laravel..."
    frankenphp php-cli artisan package:discover
    frankenphp php-cli artisan config:cache
    frankenphp php-cli artisan event:cache
    frankenphp php-cli artisan route:cache
    frankenphp php-cli artisan view:cache

    # Start FrankenPHP and Reverb server
    echo "Starting FrankenPHP & Reverb"
    exec frankenphp run -c /etc/frankenphp/Caddyfile --adapter caddyfile & frankenphp php-cli artisan reverb:start --host=0.0.0.0 --port=8001 --no-interaction
else
    exec "$@"
fi

