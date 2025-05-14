# Imagen base con Apache + PHP
FROM php:8.2-apache

# Copiar TODO el proyecto
COPY . /var/www/app/

# Cambiar el DocumentRoot para que Apache sirva desde /var/www/app/public
RUN sed -i 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

# Habilitar mod_rewrite (si usás .htaccess)
RUN a2enmod rewrite

# Dar permisos
RUN chown -R www-data:www-data /var/www/app

# Exponer el puerto
EXPOSE 80
