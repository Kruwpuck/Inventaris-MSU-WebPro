#!/bin/sh
set -e

# Install dependencies if vendor directory is missing (handling bind mount case)
if [ ! -d "vendor" ]; then
    echo "Vendor directory not found, running composer install..."
    composer install --no-scripts --no-autoloader
    composer dump-autoload
fi

# Wait for the database to be ready
echo "Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1; do
  echo "Database is not ready yet. Retrying in 5 seconds..."
  sleep 5
done

# Run migrations
# Run migrations and seed
echo "Running migrations fresh with seed..."
php artisan migrate:fresh --seed --force

# Run seeders (already included in migrate:fresh --seed)
# echo "Running seeders..."
# php artisan db:seed --force

# Link storage directory
echo "Linking storage directory..."
php artisan storage:link

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
