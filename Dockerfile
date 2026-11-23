# PHP + Apache com a extensão PDO MySQL e .htaccess habilitado
FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql \
 && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
