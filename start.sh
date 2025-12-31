#!/bin/bash
set -e  # Exit on error

echo "=========================================="
echo "Starting PHP-FPM..."
echo "=========================================="
# Start PHP-FPM in the background
php-fpm -D

echo "=========================================="
echo "Checking database connection..."
echo "=========================================="
php artisan db:show || echo "Database connection check failed"

echo "=========================================="
echo "Running Laravel migrations..."
echo "=========================================="
php artisan migrate --force 2>&1 || echo "Migration failed - check database connection"

echo "=========================================="
echo "Migrations completed! Clearing caches..."
echo "=========================================="

# Clear all caches first
php artisan cache:clear
php artisan config:clear  
php artisan route:clear
php artisan view:clear

echo "=========================================="
echo "Caching configuration..."
echo "=========================================="

# Then cache
php artisan config:cache
php artisan route:cache

echo "=========================================="
echo "Compiling views..."
echo "=========================================="
php artisan view:cache

echo "=========================================="
echo "Available routes:"
echo "=========================================="
# List routes for debugging
php artisan route:list | head -20

echo "=========================================="
echo "Starting Nginx..."
echo "=========================================="
# Start Nginx in the foreground
nginx -g "daemon off;"