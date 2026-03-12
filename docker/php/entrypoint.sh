#!/bin/bash

# ===================================
# AUKCIJSKA PLATFORMA - ENTRYPOINT
# ===================================
# This script runs before the container starts

set -e

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! pg_isready -h ${DB_HOST:-postgres} -p 5432 -U ${DB_USERNAME:-aukcije} > /dev/null 2>&1; do
    sleep 1
done
echo "Database is ready!"

# Wait for Redis to be ready
echo "Waiting for Redis to be ready..."
while ! redis-cli -h ${REDIS_HOST:-redis} ping > /dev/null 2>&1; do
    sleep 1
done
echo "Redis is ready!"

# Set correct permissions for storage
echo "Setting storage permissions..."
if [ -d "/var/www/html/storage" ]; then
    chown -R www-data:www-data /var/www/html/storage
    chmod -R 775 /var/www/html/storage
fi

# Set correct permissions for bootstrap/cache
if [ -d "/var/www/html/bootstrap/cache" ]; then
    chown -R www-data:www-data /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/bootstrap/cache
fi

# Run migrations if in production and not disabled
if [ "${APP_ENV}" = "production" ] && [ "${SKIP_MIGRATIONS}" != "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Clear and cache configuration (production only)
if [ "${APP_ENV}" = "production" ]; then
    echo "Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

# Start Horizon if this is the horizon container
if [ "${CONTAINER_ROLE}" = "horizon" ]; then
    echo "Starting Laravel Horizon..."
    exec php artisan horizon
fi

# Start scheduler if this is the scheduler container
if [ "${CONTAINER_ROLE}" = "scheduler" ]; then
    echo "Starting Laravel Scheduler..."
    exec php artisan schedule:work
fi

# Start Reverb if this is the reverb container
if [ "${CONTAINER_ROLE}" = "reverb" ]; then
    echo "Starting Laravel Reverb..."
    exec php artisan reverb:start --host=0.0.0.0 --port=8080
fi

# Default: just execute the command
exec "$@"
