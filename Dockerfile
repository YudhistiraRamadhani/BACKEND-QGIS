FROM php:8.1-apache

# 1. Install dependensi sistem untuk PostgreSQL & Unzip
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Aktifkan rewrite module untuk Laravel
RUN a2enmod rewrite

# 4. Ubah DocumentRoot ke folder 'public'
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 5. Salin kode proyek
COPY . /var/www/html

# 6. Install package Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Set permission dasar
RUN chown -R www-data:www-data /var/www/html

# 8. Salin dan set entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 9. Gunakan entrypoint
ENTRYPOINT ["entrypoint.sh"]
