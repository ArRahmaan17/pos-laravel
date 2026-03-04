#!/bin/bash
set -e

if [ "$1" = "frankenphp" ]; then
    # Cache configuration
    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache

    # Start FrankenPHP server
    exec frankenphp run --config /etc/frankenphp/Caddyfile
else
    # Run arbitrary commands like reverb or queue:work
    exec "$@"
fi
