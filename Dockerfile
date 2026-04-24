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

# Configurar permisos correctamente
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Crear log file con permisos correctos
RUN touch /var/www/html/storage/logs/laravel.log && \
    chmod 775 /var/www/html/storage/logs/laravel.log && \
    chown www-data:www-data /var/www/html/storage/logs/laravel.log

# Instalar dependencias PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

EXPOSE 80

# Script de entrada con depuración completa
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== CONFIGURANDO BASE DE DATOS ===\n\
\n\
# Ver qué variables de entorno existen\n\
echo "Variables DB disponibles:"\n\
env | grep -E "DB_|PG" || echo "No se encontraron variables DB"\n\
\n\
# Crear .env con los valores correctos\n\
cat > .env << EOF\n\
APP_NAME="Solutech"\n\
APP_ENV=production\n\
APP_DEBUG=true\n\
APP_KEY=${APP_KEY}\n\
APP_URL=${APP_URL}\n\
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
CACHE_STORE=database\n\
QUEUE_CONNECTION=database\n\
EOF\n\
\n\
echo "Contenido del .env:"\n\
cat .env\n\
\n\
# Limpiar cache previa\n\
rm -rf bootstrap/cache/*.php\n\
php artisan config:clear\n\
php artisan cache:clear\n\
\n\
# Reconstruir cache\n\
php artisan config:cache\n\
\n\
# Verificar configuración que está usando Laravel\n\
echo "Configuración que usará Laravel:"\n\
php artisan tinker --execute="echo json_encode(config('\\''database.connections.pgsql'\\''), JSON_PRETTY_PRINT);"\n\
\n\
# Ejecutar migraciones\n\
echo "Ejecutando migraciones..."\n\
php artisan migrate --force || echo "⚠️ Migraciones fallaron, revisar logs"\n\
\n\
echo "=== INICIANDO APACHE ===\n\
apache2-foreground\n\
' > /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]