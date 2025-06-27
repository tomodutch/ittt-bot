# Stage 1: Dependencies Build with Composer
FROM php:8.2-cli AS build

WORKDIR /app

RUN apt-get update && apt-get install -y unzip git zip curl \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy only files needed to install dependencies (cached unless deps change)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
 && composer dump-autoload --optimize

# Stage 2: Final production container
FROM php:8.2-fpm-alpine

# Install system packages and PHP extensions
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
    dcron \
    tzdata

ENV TZ=Asia/Tokyo

RUN docker-php-ext-install \
    bcmath \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    pcntl \
    opcache

WORKDIR /var/www/html

# Copy vendor directory and optimized autoloader from build stage
COPY --from=build /app/vendor /var/www/html/vendor
COPY --from=build /app/composer.* /var/www/html/

# Copy application code separately to allow app changes without reinstalling vendor
COPY . /var/www/html

# Set permissions
RUN addgroup -g 1000 www \
 && adduser -u 1000 -G www -s /bin/sh -D www \
 && chown -R www:www /var/www/html \
 && chmod -R 755 /var/www/html

# Copy supervisor and cron job config
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/laravel-cron /etc/periodic/1min/laravel-cron
RUN chmod +x /etc/periodic/1min/laravel-cron

USER www

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
