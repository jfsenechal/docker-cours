# PHP + Apache, avec l'extension pour parler à MySQL/MariaDB
FROM php:8.5-apache
RUN docker-php-ext-install pdo_mysql
WORKDIR /var/www/html
COPY src/ .
EXPOSE 80
