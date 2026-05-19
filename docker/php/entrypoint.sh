#!/usr/bin/env sh
set -eu

cd /var/www/html


if [ ! -f .env ]; then
  cp .env.example .env
fi

# Docker service hostnames
sed -i 's/^DB_HOST=.*/DB_HOST=mysql/' .env || true

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

mkdir -p storage/app/livewire-tmp
mkdir -p storage/app/public
mkdir -p bootstrap/cache

php artisan storage:link --force || true

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache


# Wait for DB by retrying migrations
i=0
until php artisan migrate --force >/dev/null 2>&1; do
  i=$((i + 1))
  if [ "$i" -ge 60 ]; then
    echo "Database is not ready after 120s, aborting."
    exit 1
  fi
  echo "Waiting for MySQL..."
  sleep 2
done

exec "$@"
