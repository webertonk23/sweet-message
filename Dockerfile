# =========================
# Stage 1: Node - Build assets
# =========================
FROM node:20-slim AS node

WORKDIR /app

# Instala dependências do Node
COPY package*.json ./
RUN npm install

# Copia todos os arquivos
COPY . .

# Faz o build dos assets
RUN npm run build


# =========================
# Stage 2: PHP + Apache - App
# =========================
FROM php:8.2-apache-bookworm

# Instala extensões necessárias do PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilita mod_rewrite no Apache
RUN a2enmod rewrite

# Configura o Document Root do Apache para /public (Laravel)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos da aplicação
COPY . .

# Copia arquivos buildados do Node para o public
COPY --from=node /app/public/build ./public/build

# Instala dependências do PHP
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expõe a porta padrão do Apache
EXPOSE 8080

# Comando padrão para subir o Apache
CMD ["apache2-foreground"]
