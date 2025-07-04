FROM php:7.4-apache

# Instala extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite para Apache
RUN a2enmod rewrite

# Instala Composer globalmente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html