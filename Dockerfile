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

# Copiar archivos
COPY . .

# 🔥 CORREGIR PERMISOS - Este es el problema principal
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && touch /var/www/html/storage/logs/laravel.log \
    && chmod 775 /var/www/html/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage/logs

# Instalar dependencias
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

EXPOSE 80

# Script de inicio con configuración de base de datos correcta
RUN echo '#!/bin/bash\n\
echo "=== CONFIGURANDO ENTORNO ===\n\
\n\
# Crear .env desde variables de entorno\n\
cat > .env << EOF\n\
APP_NAME="Solutech"\n\
APP_ENV=production\n\
APP_DEBUG=false\n\
APP_KEY=${APP_KEY}\n\
APP_URL=${APP_URL}\n\
\n\
# 🔥 Usar las variables de PostgreSQL que Render inyecta\n\
DB_CONNECTION=pgsql\n\
DB_HOST=${PGHOST}\n\
DB_PORT=${PGPORT}\n\
DB_DATABASE=${PGDATABASE}\n\
DB_USERNAME=${PGUSER}\n\
DB_PASSWORD=${PGPASSWORD}\n\
\n\
LOG_CHANNEL=stderr\n\
SESSION_DRIVER=database\n\
CACHE_STORE=database\n\
QUEUE_CONNECTION=database\n\
EOF\n\
\n\
echo "=== .env creado ==="\n\
echo "DB_HOST: ${PGHOST}"\n\
echo "DB_PORT: ${PGPORT}"\n\
echo "DB_DATABASE: ${PGDATABASE}"\n\
echo "DB_USERNAME: ${PGUSER}"\n\
\n\
# 🔥 Arreglar permisos nuevamente (por si acaso)\n\
chown -R www-data:www-data /var/www/html/storage\n\
chmod -R 775 /var/www/html/storage\n\
\n\
# Limpiar y cachear configuración\n\
php artisan config:clear\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Ejecutar migraciones\n\
php artisan migrate --force\n\
\n\
echo "=== INICIANDO APACHE ===\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]