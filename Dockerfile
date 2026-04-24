FROM php:8.3-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    curl git unzip libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite && \
    sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Instalar dependencias
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Crear .env
RUN echo "APP_NAME=Solutech" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_DEBUG=true" >> .env && \
    echo "APP_KEY=${APP_KEY}" >> .env && \
    echo "APP_URL=${APP_URL}" >> .env && \
    echo "" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=dpg-d7ld2h77f7vs73b0or4g-a.oregon-postgres.render.com" >> .env && \
    echo "DB_PORT=5432" >> .env && \
    echo "DB_DATABASE=solutech" >> .env && \
    echo "DB_USERNAME=solutech_user" >> .env && \
    echo "DB_PASSWORD=${DB_PASSWORD}" >> .env && \
    echo "DB_SSLMODE=require" >> .env && \
    echo "" >> .env && \
    echo "LOG_CHANNEL=stderr" >> .env && \
    echo "SESSION_DRIVER=database" >> .env && \
    echo "CACHE_DRIVER=database" >> .env

# Cachear configuración
RUN php artisan config:cache

EXPOSE 80

# Script que crea las tablas necesarias
RUN echo '#!/bin/bash\n\
echo "=== INICIANDO APLICACION ===\n\
\n\
echo "=== CORRIENDO MIGRACIONES ==="\n\
php artisan migrate --force\n\
\n\
echo "=== CREANDO TABLA DE CACHE ==="\n\
php artisan cache:table\n\
php artisan migrate --force\n\
\n\
echo "=== CREANDO TABLA DE SESIONES ==="\n\
php artisan session:table\n\
php artisan migrate --force\n\
\n\
echo "=== VERIFICANDO TABLAS ==="\n\
php artisan migrate:status\n\
\n\
echo "=== INICIANDO APACHE ===\n\
apache2-foreground\n\
' > /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]