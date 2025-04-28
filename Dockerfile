# Utiliser une image PHP officielle
FROM php:8.2-cli

# Installer les dépendances nécessaires pour pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev

# Installer l'extension PDO Postgres
RUN docker-php-ext-install pdo pdo_pgsql

# Copier ton projet dans le conteneur
COPY . /app

# Se placer dans ton dossier
WORKDIR /app

# Lancer le serveur PHP intégré
CMD php -S 0.0.0.0:10000 -t .
