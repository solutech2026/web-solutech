FROM php:8.3-apache

# Instalar dependencias incluyendo SSL
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

# Script de inicio con SSL
RUN echo '#!/bin/bash\n\
echo "=== CONFIGURANDO PostgreSQL CON SSL ===\n\
\n\
# Crear .env con SSL\n\
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
\n\
# 🔥 Crítico: Requerir SSL\n\
DB_SSLMODE=require\n\
\n\
LOG_CHANNEL=stderr\n\
SESSION_DRIVER=database\n\
EOF\n\
\n\
# Forzar SSL también en DATABASE_URL si existe\n\
if [ ! -z "$DATABASE_URL" ]; then\n\
    # Agregar ?sslmode=require a la URL\n\
    export DATABASE_URL="${DATABASE_URL}?sslmode=require"\n\
    echo "DATABASE_URL actualizado con SSL"\n\
fi\n\
\n\
echo "=== Probando conexión con SSL ===\n\
php -r "\$pdo = new PDO(\"pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE};sslmode=require\", \"${DB_USERNAME}\", \"${DB_PASSWORD}\"); echo \"✅ Conexión exitosa con SSL\\n\"; catch (Exception \$e) { echo \"❌ Error: \" . \$e->getMessage() . \"\\n\"; }"\n\
\n\
echo "=== Cacheando configuración ===\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
echo "=== Ejecutando migraciones ===\n\
php artisan migrate --force\n\
\n\
echo "=== INICIANDO APACHE ===\n\
apache2-foreground' > /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]