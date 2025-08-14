FROM php:8.2-fpm

# 1. Install NodeJS (wajib untuk Vite build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y \
        nodejs \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libpq-dev \
        zip \
        unzip \
        git \
        sqlite3 \
        libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

WORKDIR /var/www

# 2. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copy source code
COPY . /var/www

# 4. Build PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader && composer dump-autoload -o

# 5. Build NPM assets (Vite)
RUN npm install && npm run build

# 6. Entrypoint (opsional)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 7. DB file, permissions
RUN mkdir -p /app/database && cp /var/www/database/database.sqlite /app/database/database.sqlite || true
RUN chown -R www-data:www-data /var/www && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
