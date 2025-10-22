#!/bin/bash

# Ensure storage directories exist and have correct permissions
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Run migrations
php artisan migrate --force

# Clear any existing caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Cache configuration
php artisan config:cache
php artisan route:cache

# Symlink storage logs to stdout for Render visibility
tail -f storage/logs/laravel.log &

# Start Apache
apache2-foreground
