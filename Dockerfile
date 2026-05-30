FROM php:8.1-fpm

# Install sistem dependensi
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    libpq-dev

# Install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql

# Copy konfigurasi Nginx
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# Copy aplikasi
COPY . /var/www
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# MENJALANKAN MIGRASI SAAT BUILD (Solusi pengganti Pre-Deploy Command)
# Pastikan database sudah terhubung melalui ENV variables saat proses build ini
RUN php artisan migrate --force

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Jalankan Nginx dan PHP-FPM
CMD service nginx start && php-fpm
