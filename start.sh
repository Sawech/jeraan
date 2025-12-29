#!/bin/bash

# Start PHP-FPM in the background
php-fpm -D

# Run Laravel migrations (optional, uncomment if needed)
# php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Nginx in the foreground
nginx -g "daemon off;"