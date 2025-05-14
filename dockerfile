FROM php:8.2-apache

# Actualización e instalación de dependencias del sistema
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql gd

# Copiar todo el proyecto al contenedor
COPY . /var/www/app/

# Cambiar el DocumentRoot para que Apache sirva desde la carpeta 'backend/public'
RUN sed -i 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

# Habilitar mod_rewrite para .htaccess
RUN a2enmod rewrite

# Asignar permisos al servidor
RUN chown -R www-data:www-data /var/www/app

# Exponer el puerto HTTP
EXPOSE 80

# Copiar el archivo de configuración setup_database.php
COPY setup_database.php /var/www/app/setup_database.php

# Ejecutar el script de configuración de la base de datos cuando el contenedor se inicie
CMD php /var/www/app/setup_database.php && apache2-foreground
