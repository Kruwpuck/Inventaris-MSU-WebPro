# ========= STAGE 1: Composer deps =========
FROM composer:2 AS vendor
WORKDIR /app
# Salin composer files saja untuk cache layer
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist

# ========= STAGE 2: Build assets (Vite) =========
FROM node:20 AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
# Salin resource untuk build
COPY resources ./resources
COPY vite.config.* ./
# Build assets produksi (hasil ke /app/dist)
RUN npm run build || echo "skip build if not configured"

# ========= STAGE 3: App (php-fpm) =========
FROM php:8.3-fpm-alpine

# Install ekstensi PHP yang umum untuk Laravel
RUN apk add --no-cache bash icu-dev oniguruma-dev libzip-dev zlib-dev curl git \
 && docker-php-ext-install intl pdo pdo_mysql mbstring zip opcache

WORKDIR /var/www

# Salin source code
COPY . .

# Salin vendor dari stage composer
COPY --from=vendor /app/vendor ./vendor

# (Opsional) Salin hasil build assets ke public jika kamu build Vite produksi
# Sesuaikan sesuai konfigurasi Vite kamu (default ke public/build)
# Jika pakai laravel-vite default, hapus baris ini karena Vite serve dev-mode
# COPY --from=assets /app/dist ./public/build

# Permission (di Windows biasanya aman, tapi ini untuk Linux server)
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Expose port fpm
EXPOSE 9000
CMD ["php-fpm"]
