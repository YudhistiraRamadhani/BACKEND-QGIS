FROM php:8.1-apache

# 1. Install dependensi sistem untuk PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Install Composer secara resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Mengaktifkan rewrite module untuk Laravel
RUN a2enmod rewrite

# 4. Mengatur DocumentRoot ke folder 'public'
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 5. Menyalin file project
COPY . /var/www/html

# 6. Install package Laravel (Langkah yang tadi hilang)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Set permission yang benar agar Laravel bisa menulis ke storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Pastikan Apache mendengarkan port 80 (Railway butuh ini)
RUN sed -i 's/Listen 80/Listen 80/' /etc/apache2/ports.conf
