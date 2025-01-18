#!/bin/bash

# Copy .env.example to .env if .env does not exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Set permissions for storage and bootstrap/cache directories
chown -R www-data:www-data /var/www/trader/storage /var/www/trader/bootstrap/cache
chmod -R 775 /var/www/trader/storage /var/www/trader/bootstrap/cache

# Wait for MySQL to be ready
while ! mysqladmin ping -h"$DB_HOST" --silent; do
    echo "Waiting for MySQL to be ready..."
    sleep 2
done

# Create the database if it doesn't exist
echo "Creating database if it doesn't exist..."
mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE;"

# Run Laravel commands
echo "Running Laravel commands..."
php artisan key:generate
php artisan migrate

# Execute the original command
exec "$@"