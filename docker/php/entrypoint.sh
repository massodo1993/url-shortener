#!/bin/bash
set -e

cd /var/www/html

# 1. Зависимости
if [ ! -d "vendor" ]; then
    echo "[entrypoint] Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 2. .env
if [ ! -f ".env" ]; then
    echo "[entrypoint] Creating .env from .env.example..."
    cp .env.example .env
fi

# 3. APP_KEY
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate --force
fi

# 4. Права
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 5. Ждём БД
echo "[entrypoint] Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT}..."
until php -r "try { new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
    sleep 2
    echo "[entrypoint] ...waiting for DB"
done
echo "[entrypoint] DB is up."

# 6. Миграции (+сид только при первом старте — если таблица links пуста/отсутствует)
echo "[entrypoint] Running migrations..."
php artisan migrate --force

if [ ! -f "storage/.seeded" ]; then
    echo "[entrypoint] Seeding demo data..."
    php artisan db:seed --force && touch storage/.seeded || true
fi

# 7. storage:link
php artisan storage:link 2>/dev/null || true

echo "[entrypoint] Ready. Starting: $@"
exec "$@"

echo "[entrypoint] Publishing Filament assets..."
php artisan filament:assets
php artisan optimize:clear 2>/dev/null || true
