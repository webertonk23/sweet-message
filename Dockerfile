FROM php:8.2-apache-bookworm

# Instala dependências e extensões PHP
RUN apt-get update && apt-get install -y \
    libzip-dev unzip libonig-dev libxml2-dev libsqlite3-dev \
    && docker-php-ext-install pdo_mysql mbstring xml curl zip opcache pdo_sqlite

# Configura Apache
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && a2enmod rewrite

# Configura DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Script de inicialização
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]