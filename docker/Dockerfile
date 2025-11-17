# Multi-stage build for production-ready PHP-FPM image
FROM php:8.3-fpm AS base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    opcache

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP-FPM pool configuration
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Production stage
FROM base AS production

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Optimize Laravel for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

# Development stage
FROM base AS development

# Install additional development tools
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Install all dependencies including dev
RUN composer install --no-interaction

EXPOSE 9000

CMD ["php-fpm"]
