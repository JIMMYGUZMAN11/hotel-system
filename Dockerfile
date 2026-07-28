FROM php:8.2-apache

# Extensiones necesarias (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite (por si se usa en el futuro)
RUN a2enmod rewrite

# Copiar el proyecto al document root de Apache
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html

# Render inyecta la variable PORT; Apache por defecto usa 80.
# Este script ajusta el puerto de escucha al que Render asigne.
RUN echo '#!/bin/bash\nsed -i "s/80/${PORT:-80}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf\napache2-foreground' > /usr/local/bin/start-apache.sh \
    && chmod +x /usr/local/bin/start-apache.sh

EXPOSE 80
CMD ["/usr/local/bin/start-apache.sh"]
