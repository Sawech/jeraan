#!/bin/bash
set -e

echo "Starting PHP-FPM..."
php-fpm -D

echo "Waiting for database..."
sleep 10

echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Nginx..."
nginx -g "daemon off;"