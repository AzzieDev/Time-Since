FROM serversideup/php:8.3-fpm-nginx

USER root
RUN apt-get update && apt-get install -y sqlite3 default-mysql-client && apt-get clean && rm -rf /var/lib/apt/lists/*

USER www-data

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-interaction --optimize-autoloader --no-dev

# Enable Laravel autorun scripts (migrates DB automatically on startup)
ENV AUTORUN_LARAVEL_MIGRATION="true"
ENV AUTORUN_LARAVEL_STORAGE_LINK="true"
