#!/bin/bash
set -e

if [ "$1" = "frankenphp" ]; then
    sed -i "s/your_awesome_application_port/${APP_PORT}/" /etc/frankenphp/Caddyfile
    # Ensure database exists
    echo "Checking database status..."
    frankenphp php-cli /var/www/html/database/ensure_db.php

    # Run migrations
    echo "Running migrations..."
    frankenphp php-cli artisan migrate:fresh --seed

    # Discover packages and cache configuration
    echo "Optimizing Laravel..."
    frankenphp php-cli artisan package:discover
    frankenphp php-cli artisan config:cache
    frankenphp php-cli artisan event:cache
    frankenphp php-cli artisan route:cache
    frankenphp php-cli artisan view:cache

    # Start FrankenPHP server
    echo "Starting FrankenPHP..."
    exec frankenphp run -c /etc/frankenphp/Caddyfile
else
    # Run arbitrary commands like reverb or queue:work
    exec "$@"
fi

