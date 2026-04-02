#!/bin/bash

# Clear Laravel caches and then cache them for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Symbol link for storage
php artisan storage:link --force

# Verification of credentials (safety check)
if [ ! -f "/var/www/html/$(basename ${FIREBASE_CREDENTIALS:-none})" ] && [ ! -f "${FIREBASE_CREDENTIALS:-none}" ]; then
    echo "Warning: Firebase JSON file NOT found at current FIREBASE_CREDENTIALS path. Sync will fail."
fi

# Run migrations (Safe for development, remove for strict manual control if needed)
php artisan migrate --force

# Start PHP-FPM and Nginx
echo "Starting PHP-FPM and Nginx..."
php-fpm -D
nginx -g 'daemon off;'
