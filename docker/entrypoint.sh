#!/bin/bash

# Clear Laravel caches if needed (useful for Dokploy environment changes)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Symbol link for storage
php artisan storage:link --force

# Always run migrations in production (use with caution or handle manually if preferred)
# Remove the next line if you prefer manual migration control from VPS console
# php artisan migrate --force

# Start Nginx & PHP-FPM
php-fpm -D
nginx -g 'daemon off;'
