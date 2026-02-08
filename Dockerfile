FROM php:8.2-fpm

# نصب فقط چیزهای لازم
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        zip \
        unzip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache
