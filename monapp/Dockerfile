# 1. Point de départ : PHP 8.3 avec le serveur Apache déjà configuré
FROM php:8.3-apache
# 2. Installer l'extension MySQL pour PHP
RUN docker-php-ext-install pdo_mysql
# 3. Se placer dans le dossier web d'Apache
WORKDIR /var/www/html
# 4. Copier notre code dans l'image
COPY src/ .
# 5. Documenter le port utilisé par Apache
EXPOSE 80
# (Pas besoin de CMD : l'image php:8.3-apache démarre déjà Apache)