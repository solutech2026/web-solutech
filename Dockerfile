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
    sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && touch /var/www/html/storage/logs/laravel.log \
    && chmod 775 /var/www/html/storage/logs/laravel.log

# Instalar dependencias
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

EXPOSE 80

# Script de inicio - USANDO VARIABLES DIRECTAS DE RENDER
RUN echo '#!/bin/bash\n\
echo "=== INICIANDO CONFIGURACIÓN ===\n\
\n\
# 🔥 Mostrar qué variables existen\n\
echo "Variables de entorno disponibles:"\n\
env | grep -E "PG|DATABASE" || echo "No hay variables PG"\n\
\n\
# 🔥 Usar DATABASE_URL si existe (más confiable)\n\
if [ ! -z "$DATABASE_URL" ]; then\n\
    echo "✅ Usando DATABASE_URL"\n\
    DB_URL="$DATABASE_URL"\n\
else\n\
    echo "✅ Construyendo URL manualmente"\n\
    DB_URL="pgsql://${PGUSER}:${PGPASSWORD}@${PGHOST}:${PGPORT}/${PGDATABASE}"\n\
fi\n\
\n\
# Crear .env con la URL de base de datos\n\
cat > .env << EOF\n\
APP_NAME="Solutech"\n\
APP_ENV=production\n\
APP_DEBUG=true\n\
APP_KEY=${APP_KEY}\n\
APP_URL=${APP_URL}\n\
\n\
# 🔥 CRÍTICO: Usar DATABASE_URL\n\
DATABASE_URL=${DB_URL}\n\
DB_CONNECTION=pgsql\n\
\n\
LOG_CHANNEL=stderr\n\
SESSION_DRIVER=database\n\
CACHE_STORE=database\n\
QUEUE_CONNECTION=database\n\
EOF\n\
\n\
echo "=== .env creado ==="\n\
cat .env\n\
\n\
# 🔥 Probar conexión ANTES de iniciar\n\
echo "=== Probando conexión a PostgreSQL ===\n\
php -r "\$db = getenv('DATABASE_URL'); echo \\"URL: \\" . \$db . \\"\\n\\"; try { new PDO(\$db); echo \\"✅ CONEXIÓN EXITOSA\\n\\"; } catch (Exception \$e) { echo \\"❌ ERROR: \\" . \$e->getMessage() . \\"\\n\\"; }"\n\
\n\
echo "=== Cacheando configuración ===\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
echo "=== Ejecutando migraciones ===\n\
php artisan migrate --force\n\
\n\
echo "=== INICIANDO APACHE ===\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]