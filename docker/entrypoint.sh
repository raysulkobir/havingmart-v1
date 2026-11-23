#!/bin/bash
set -e

cd /var/www/html

# Install dependencies into the (named-volume) vendor dir on first boot.
# No composer.lock present => Composer resolves versions valid for PHP 7.4.
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] vendor/ empty - installing PHP 7.4 dependencies (first boot, may take a few minutes)..."
    composer install --prefer-dist --no-progress --no-interaction
fi

# Make sure Laravel's writable paths exist and are writable by Apache.
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache \
         storage/logs \
         bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

exec "$@"
