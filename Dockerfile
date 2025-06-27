# Stage 1: Build dependencies with PHP 8.2 CLI and Composer
FROM php:8.2-cli AS composer

WORKDIR /app

# Install system dependencies for composer
RUN apt-get update && apt-get install -y unzip git zip curl

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize

# Stage 2: Production PHP container with PHP 8.2 FPM + cron + supervisord
FROM php:8.2-fpm-alpine

# Install OS packages needed
RUN apk add --no-cache \
    bash \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    icu-dev \
    postgresql-dev \
    zip \
    unzip \
    shadow \
    supervisor \
    cron \
    tzdata

ENV TZ=Asia/Tokyo

# Install PHP extensions
RUN docker-php-ext-install \
    bcmath \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    pcntl \
    opcache

# Copy application from build stage
COPY --from=composer /app /var/www/html

WORKDIR /var/www/html

# Set permissions (change www-data user/group if needed)
RUN addgroup -g 1000 www \
 && adduser -u 1000 -G www -s /bin/sh -D www \
 && chown -R www:www /var/www/html \
 && chmod -R 755 /var/www/html

# Copy supervisord config and cron job
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/laravel-cron /etc/periodic/1min/laravel-cron
RUN chmod +x /etc/periodic/1min/laravel-cron

USER www

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
