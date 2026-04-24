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

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Instalar dependencias
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

EXPOSE 80

# Script de inicio SIMPLE
RUN echo '#!/bin/bash\n\
echo "=== INICIANDO APLICACIÓN ===\n\
\n\
# Crear .env con los datos de Render\n\
cat > .env << EOF\n\
APP_NAME="Solutech"\n\
APP_ENV=production\n\
APP_DEBUG=true\n\
APP_KEY=${APP_KEY}\n\
APP_URL=${APP_URL}\n\
\n\
DB_CONNECTION=pgsql\n\
DB_HOST=${DB_HOST}\n\
DB_PORT=${DB_PORT}\n\
DB_DATABASE=${DB_DATABASE}\n\
DB_USERNAME=${DB_USERNAME}\n\
DB_PASSWORD=${DB_PASSWORD}\n\
DB_SSLMODE=require\n\
\n\
LOG_CHANNEL=stderr\n\
SESSION_DRIVER=database\n\
EOF\n\
\n\
# Cachear configuración\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
# Ejecutar migraciones\n\
php artisan migrate --force\n\
\n\
# Iniciar Apache\n\
apache2-foreground\n\
' > /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]