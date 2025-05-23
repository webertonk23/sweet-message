# Use a imagem base mais recente do PHP com Apache
FROM php:8.2-apache-bookworm

# Evita os repositórios problemáticos do Ubuntu 20.04
RUN rm /etc/apt/sources.list.d/* && \
    apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev

# Instala extensões PHP necessárias
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    xml \
    curl \
    zip \
    opcache

# Configura o Apache e PHP
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Habilite mod_rewrite
RUN a2enmod rewrite

# Configura o DocumentRoot do Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf