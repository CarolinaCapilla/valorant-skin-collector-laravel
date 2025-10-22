#!/bin/bash

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache

# Start Apache
apache2-foreground
