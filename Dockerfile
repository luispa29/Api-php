# Usa la imagen oficial de PHP 8.2 FPM (FastCGI Process Manager)
FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema necesarias
RUN apk update && apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    nginx \
    build-base \
    freetds-dev \
    unixodbc-dev \
    libxml2-dev \
    icu-dev \
    libtool \
    autoconf \
    g++
# Instala las extensiones de PHP necesarias
# Primero pdo_mysql si lo necesitas
RUN docker-php-ext-install pdo pdo_mysql

# Instala y habilita los drivers de SQL Server
# Desglosamos los comandos para mejor depuración y claridad
RUN pecl install sqlsrv \
    && pecl install pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de tu aplicación al contenedor
COPY . /var/www/html

# Instala las dependencias de Composer
# Asegúrate de que composer.json y composer.lock existan antes de esto
RUN composer install --no-dev --optimize-autoloader

# Configura los permisos de la aplicación
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copia la configuración de Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copia la configuración de PHP-FPM
COPY docker/php-fpm/php.ini /usr/local/etc/php/conf.d/php.ini

# Exponer el puerto 9000 para PHP-FPM y 80 para Nginx (Nginx redireccionará a PHP-FPM)
EXPOSE 9000
EXPOSE 80

# Comando para iniciar PHP-FPM y Nginx
CMD ["sh", "-c", "php-fpm && nginx -g 'daemon off;'"]
