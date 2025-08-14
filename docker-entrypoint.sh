#!/bin/sh
set -e

chmod -R 777 /var/www/storage /var/www/bootstrap/cache

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link || true

exec "$@"
