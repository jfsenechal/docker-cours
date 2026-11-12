# Étape 1 — l'atelier : Composer télécharge les dépendances
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json .
RUN composer install --no-dev --no-interaction

# Étape 2 — la vitrine : l'image finale
FROM php:8.5-apache
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY src/ .