#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ -n "${DB_HOST:-}" ] && [ -n "${DB_DATABASE:-}" ] && [ -n "${DB_USERNAME:-}" ] && [ -n "${DB_PASSWORD:-}" ]; then
    echo "Running Laravel database migrations..."
    php artisan migrate --force
else
    echo "Database environment is incomplete; skipping migrations for this boot."
fi

exec apache2-foreground
