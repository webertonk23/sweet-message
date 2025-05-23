#!/bin/sh
set -e

# Cria o arquivo do SQLite caso use SQLite (se não, remova esta linha)
touch /var/www/html/database/database.sqlite

# Cria link simbólico storage se não existir
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

# Roda migrations (sem prompt)
php artisan migrate --force

exec "$@"
