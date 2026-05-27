FROM php:8.1-apache
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql
# Mengaktifkan rewrite module untuk Laravel
RUN a2enmod rewrite
# Menyalin kode ke folder web
COPY . /var/www/html
# Mengatur permission storage
RUN chown -R www-data:www-data /var/www/html/storage
# Mengatur DocumentRoot ke public folder Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
