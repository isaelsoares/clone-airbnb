# PHP + Apache com a extensão PDO MySQL
FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql
