FROM php:8.2-fpm

# Gunakan mirror Indonesia (Kartolo) untuk mempercepat apt-get
RUN sed -i 's/deb.debian.org/kartolo.sby.datautama.net.id/g' /etc/apt/sources.list.d/debian.sources

# Ekstensi yang umum untuk Laravel + MySQL. opcache penting di VPS 1 vCPU.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql gd zip opcache \
    && apt-get clean \
    && find /var/lib/apt/lists -type f -delete

# Limit upload (Livewire butuh ini). memory_limit diturunkan 256M -> 128M
# supaya 6 worker muat di mem_limit 320m pada VPS 1GB.
RUN printf "upload_max_filesize=20M\npost_max_size=25M\nmemory_limit=128M\n" \
    > /usr/local/etc/php/conf.d/uploads.ini

# opcache produksi
RUN printf "opcache.enable=1\nopcache.memory_consumption=64\nopcache.max_accelerated_files=10000\nopcache.validate_timestamps=0\nopcache.interned_strings_buffer=8\n" \
    > /usr/local/etc/php/conf.d/opcache.ini

# PHP-FPM ondemand: tidak spawn worker idle, hemat RAM.
RUN printf "[www]\npm = ondemand\npm.max_children = 6\npm.process_idle_timeout = 10s\npm.max_requests = 500\n" \
    > /usr/local/etc/php-fpm.d/zz-tuning.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader || true

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
