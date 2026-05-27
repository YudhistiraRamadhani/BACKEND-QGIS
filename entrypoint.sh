#!/bin/sh
# Menunggu database siap dan menjalankan migrasi
echo "Menjalankan migrasi..."
php artisan migrate --force

# Menjalankan server
echo "Menyalakan PHP-FPM dan Nginx..."
php-fpm -D && nginx -g "daemon off;"
