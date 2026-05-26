FROM php:8.1-fpm-alpine

# Install dependensi sistem dan driver PostgreSQL
RUN apk add --no-cache \
    nginx \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql

# Mengatur folder kerja di dalam container
WORKDIR /app

# Menyalin seluruh file project ke dalam container
COPY . .

# Menginstall Composer secara global dan menjalankan instalasi package
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Menyusun konfigurasi default web server agar membaca folder public Laravel
RUN mkdir -p /run/nginx
RUN echo 'server { \
    listen 80; \
    root /app/public; \
    index index.php index.html; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        try_files $uri =404; \
        fastcgi_split_path_info ^(.+\.php)(/.+)$; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        fastcgi_param PATH_INFO $fastcgi_path_info; \
    } \
}' > /etc/nginx/http.d/default.conf

# Membuka port internal
EXPOSE 80

# Perintah utama untuk menjalankan PHP-FPM dan Nginx secara bersamaan saat server menyala
CMD php artisan migrate --force && php-fpm -D && nginx -g "daemon off;"
