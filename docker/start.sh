#!/bin/bash
set -e

echo "Starting PHP-FPM..."
php-fpm -D

echo "Waiting for database..."
sleep 10

echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

echo "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:cache

echo "Starting Nginx..."
nginx -g "daemon off;"