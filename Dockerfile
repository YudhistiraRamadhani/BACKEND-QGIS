FROM php:8.1-fpm

# 1. Install sistem dependensi
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql

# 3. Copy konfigurasi Nginx
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# 4. Optimasi Instalasi Composer (Caching Layer)
WORKDIR /var/www
COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 5. Copy seluruh aplikasi dan selesaikan install
COPY . .
RUN composer dump-autoload --optimize

# 6. Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 7. Jalankan Migrasi & Service
CMD php artisan migrate --force && service nginx start && php-fpm
