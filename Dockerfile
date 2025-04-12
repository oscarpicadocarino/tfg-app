FROM php:8.2-apache

# Instalar mysqli y extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Activar mod_rewrite si lo necesitas más adelante
RUN a2enmod rewrite
