# Base image: PHP 8.3 FPM Alpine for a lightweight production image
FROM php:8.3-fpm-alpine

# Set environment variables
ENV APP_HOME /var/www/html
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_MEMORY_CONSUMPTION=128
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES=10000
ENV PHP_OPCACHE_REVALIDATE_FREQ=0

# Install system dependencies and tools
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    git \
    nodejs \
    npm \
    netcat-openbsd

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Use the installer to add extensions (much faster as it uses pre-compiled binaries where possible)
RUN install-php-extensions gd bcmath zip pdo_mysql intl opcache redis

# Get composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup working directory
WORKDIR $APP_HOME

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-grpc

# Install JS dependencies and build assets
RUN npm install && npm run build

# Ensure storage directories exist
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/app/public bootstrap/cache

# Configure Nginx logs and permissions
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log \
    && chown -R www-data:www-data $APP_HOME /var/lib/nginx /var/log/nginx

# Configure Nginx to run as www-data
RUN sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf

# Copy custom Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Add entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
