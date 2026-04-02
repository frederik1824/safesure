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

# Run migrations but don't exit if they fail (prevents container crash)
php artisan migrate --force || echo "Migrations failed, check DB connection"

# Fix database permissions (Critical for SQLite writing)
chown -R www-data:www-data /var/www/html/database

# Clear caches for a fresh start
php artisan cache:clear

# Start PHP-FPM and Nginx
echo "Starting PHP-FPM..."
php-fpm -D

# Wait for FPM to start
echo "Waiting for PHP-FPM to be ready..."
while ! nc -z 127.0.0.1 9000; do
  sleep 1
done

echo "Starting Nginx..."
nginx -g 'daemon off;'
