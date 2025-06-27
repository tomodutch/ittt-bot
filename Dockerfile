# ----------------------
# Stage 1: PHP base with extensions
# ----------------------
FROM php:8.2-fpm-alpine AS php-base

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

# ----------------------
# Stage 2: Composer (dependencies only)
# ----------------------
FROM php:8.2-cli AS composer

WORKDIR /app

RUN apt-get update && apt-get install -y unzip git zip curl \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy entire app so artisan exists
COPY . .

# Run composer install
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# ----------------------
# Stage 3: Final app image
# ----------------------
FROM php-base AS final

WORKDIR /var/www/html

# Copy just the built vendor directory
COPY --from=composer /app/vendor ./vendor
COPY --from=composer /app/composer.* ./

# Then copy the actual app code
COPY . .

# Set up app permissions
RUN addgroup -g 1000 www \
 && adduser -u 1000 -G www -s /bin/sh -D www \
 && chown -R www:www /var/www/html \
 && chmod -R 755 /var/www/html

# Ensure PHP-FPM listens on all interfaces
RUN sed -i 's|^listen = .*|listen = 0.0.0.0:9000|' /usr/local/etc/php-fpm.d/www.conf

# Add supervisor and cron setup
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/laravel-cron /etc/periodic/1min/laravel-cron
RUN chmod +x /etc/periodic/1min/laravel-cron

USER root

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
