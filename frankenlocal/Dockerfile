FROM dunglas/frankenphp:1-php8.5
RUN  install-php-extensions pdo_mysql
WORKDIR /app/public
COPY src/ .
EXPOSE 80
