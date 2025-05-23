#!/bin/bash
set -e


# Cria o link simbólico para storage se ainda não existir
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi
php artisan migrate --force

# Ajusta permissões para o storage e cache, só para garantir
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"
