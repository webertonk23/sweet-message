#!/bin/bash
set -e

# Cria o arquivo do SQLite se não existir
touch /var/www/html/database/database.sqlite

# Instala dependências e build
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Migrações
php artisan migrate --force

# Inicia Apache
exec apache2-foreground