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

EXPOSE 80

# Script de inicio que crea .env al arrancar
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== CREANDO .env ===\n\
\n\
# Crear archivo .env desde cero\n\
cat > .env << EOF\n\
APP_NAME=Solutech\n\
APP_ENV=production\n\
APP_DEBUG=true\n\
php artisan key:generate --force\n\
APP_URL=https://web-solutech.onrender.com\n\
\n\
DB_CONNECTION=pgsql\n\
DB_HOST=dpg-d7ld2h77f7vs73b0or4g-a.oregon-postgres.render.com\n\
DB_PORT=5432\n\
DB_DATABASE=solutech\n\
DB_USERNAME=solutech_user\n\
DB_PASSWORD=vt21B1imfNpBSuIqIrZ00iPLzf9snF0UZ\n\
DB_SSLMODE=require\n\
\n\
LOG_CHANNEL=stderr\n\
SESSION_DRIVER=database\n\
CACHE_DRIVER=database\n\
EOF\n\
\n\
# Mostrar que APP_KEY está presente\n\
echo "=== VERIFICANDO .env ===\n\
cat .env | grep APP_KEY\n\
\n\
# Forzar que Laravel use el .env\n\
rm -rf bootstrap/cache/*.php\n\
\n\
# Limpiar y recachear configuración\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
# Verificar que Laravel lee la APP_KEY\n\
echo "=== VERIFICANDO APP_KEY ===\n\
php artisan tinker --execute="echo \\"APP_KEY: \\" . env(\\"APP_KEY\\");"\n\
\n\
# Ejecutar migraciones\n\
echo "=== EJECUTANDO MIGRACIONES ===\n\
php artisan migrate --force\n\
\n\
echo "=== INICIANDO APACHE ===\n\
apache2-foreground\n\
' > /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]