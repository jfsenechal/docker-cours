FROM roundcube/roundcubemail:latest

# Installation des dépendances système pour Argon2 et compilation de l'extension PHP
RUN apt-get update && apt-get install -y \
    libargon2-dev \
    libsodium-dev \
    && docker-php-ext-install sodium \
    && rm -rf /var/lib/apt/lists/*
