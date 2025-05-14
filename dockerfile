# Imagen base con Apache + PHP 8.2
FROM php:8.2-apache

# Copiar solo la carpeta 'public' al directorio raíz del servidor web
COPY public/ /var/www/html/

# (Opcional) Si usás archivos fuera de /public para lógica PHP
COPY config/ /var/www/html/config/
COPY controllers/ /var/www/html/controllers/
COPY models/ /var/www/html/models/
COPY setup_database.php /var/www/html/

# Habilitar mod_rewrite (si usás .htaccess)
RUN a2enmod rewrite

# Dar permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto expuesto
EXPOSE 80
