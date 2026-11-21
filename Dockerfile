# =============================================================================
# DOCKERFILE PRODUKSI — PORTAL SILSILAH KLAN KASAN MUNADI (LARAVEL 12 + PHP 8.2)
# Siap deploy di Render.com (Free Tier), Railway, Fly.io, atau VPS Docker
# =============================================================================
FROM php:8.2-apache

# Install dependensi sistem & ekstensi PHP yang dibutuhkan Laravel 12 + PostgreSQL/MySQL/SQLite
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        xml \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Konfigurasi PHP (batas upload foto lebih lega) & Apache VirtualHost
COPY docker/php.ini /usr/local/etc/php/conf.d/99-klan-munadi.ini
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Salin composer terlebih dahulu untuk optimasi layer cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Salin seluruh kode aplikasi
COPY . .

# Pastikan folder storage & cache tersedia dengan izin yang benar
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             storage/app/public/gallery \
             storage/app/public/avatars \
             bootstrap/cache \
    && rm -rf public/storage \
    && composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache public/assets

# Pasang entrypoint (konversi LF agar aman saat build dari Windows)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
