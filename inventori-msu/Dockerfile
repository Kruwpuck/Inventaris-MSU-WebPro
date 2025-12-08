# PHP-FPM 8.2/8.3/8.4, pilih yang cocok dengan proyekmu (di contoh ini 8.2)
FROM php:8.2-fpm

# Ekstensi yang umum untuk Laravel + MySQL
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql gd zip

# ✅ Tambahan permanen untuk limit upload (Livewire butuh ini)
RUN echo "upload_max_filesize=10M\npost_max_size=12M\nmemory_limit=256M" \
    > /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# (Opsional) percepat composer dengan cache layer saat pertama build
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader || true

# Copy sisa kode saat runtime lewat bind mount (docker-compose)
# Jadi composer autoload akan dijalankan saat container up

CMD ["php-fpm"]
