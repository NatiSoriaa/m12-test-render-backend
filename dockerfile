# Imagen base con Apache y PHP 8.2
FROM php:8.2-apache

# Instalar extensión para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo el proyecto al contenedor
COPY . /var/www/app/

# Cambiar el DocumentRoot para que Apache sirva desde la carpeta 'backend/public'
RUN sed -i 's!/var/www/html!/var/www/app/backend/public!g' /etc/apache2/sites-available/000-default.conf

# Habilitar mod_rewrite para .htaccess
RUN a2enmod rewrite

# Asignar permisos al servidor
RUN chown -R www-data:www-data /var/www/app

# Exponer el puerto HTTP
EXPOSE 80
