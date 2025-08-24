FROM php:8.2-apache

# Instala la extensión mysqli para conectar con MySQL
RUN docker-php-ext-install mysqli

# Habilita mod_rewrite (si usas .htaccess)
RUN a2enmod rewrite

# Copia el contenido del directorio html al servidor web
COPY html/ /var/www/html/

# Copia el contenido del directorio php al servidor web
COPY php/ /var/www/html/php/

# Copia los archivos JS para que estén disponibles
COPY js/ /var/www/html/js/

# Copia los archivos CSS para que estén disponibles
COPY css/ /var/www/html/css/

# Establece permisos (opcional)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
