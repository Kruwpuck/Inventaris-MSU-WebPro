#!/bin/sh
set -e

# Install dependencies if vendor directory is missing (handling bind mount case)
if [ ! -d "vendor" ]; then
    echo "Vendor directory not found, running composer install..."
    composer install --no-dev --no-scripts --no-autoloader
fi
composer dump-autoload --optimize

# Wait for the database to be ready
echo "Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1; do
  echo "Database is not ready yet. Retrying in 5 seconds..."
  sleep 5
done

# Jalankan HANYA migrasi baru. JANGAN PERNAH pakai migrate:fresh --
# perintah itu men-DROP seluruh tabel tiap container start dan menghapus data produksi.
echo "Running migrations..."
php artisan config:clear
php artisan migrate --force

# Fix permissions for storage and cache
echo "Fixing permissions..."
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/bootstrap/cache
chown -R :www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Link storage directory
echo "Linking storage directory..."
php artisan storage:link || true

# Warm up cache produksi (hemat CPU/RAM di VPS 1 vCPU)
echo "Caching config, routes, views..."
# Non-fatal: kalau ada view/route rusak, app tetap jalan (cuma tanpa cache).
php artisan config:cache || echo "WARN: config:cache gagal"
php artisan route:cache  || echo "WARN: route:cache gagal"
php artisan view:cache   || echo "WARN: view:cache gagal (ada blade component hilang)"

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
