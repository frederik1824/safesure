# Base image: PHP 8.3 FPM Alpine for a lightweight production image
FROM php:8.3-fpm-alpine

# Set environment variables
ENV APP_HOME /var/www/html
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_MEMORY_CONSUMPTION=128
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES=10000
ENV PHP_OPCACHE_REVALIDATE_FREQ=0

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    bash \
    curl \
    git \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd bcmath zip pdo_mysql intl opcache

# Get composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup working directory
WORKDIR $APP_HOME

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data $APP_HOME/storage $APP_HOME/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install JS dependencies and build assets
RUN npm install && npm run build

# Copy custom Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Add entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
