# Utiliser une image PHP officielle
FROM php:8.2-cli

# Installer extensions si besoin (ex: Postgres)
RUN docker-php-ext-install pdo pdo_pgsql

# Copier ton projet dans le conteneur
COPY . /app

# Se placer dans ton dossier
WORKDIR /app

# Lancer un serveur PHP intégré
CMD php -S 0.0.0.0:10000 -t .
