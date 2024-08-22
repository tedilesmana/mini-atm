#!/bin/bash

# Wait for MySQL to be ready
until nc -z -v -w30 db 3306
do
  echo "Waiting for database connection..."
  sleep 5
done

echo "Database is up - executing command"

npm install

composer install

npm run build

# Run migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm
