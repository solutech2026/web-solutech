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

# Script de entrada con sintaxis corregida
RUN echo '#!/bin/bash' > /usr/local/bin/entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== CONFIGURANDO BASE DE DATOS ==="' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Crear .env con los valores correctos' >> /usr/local/bin/entrypoint.sh && \
    echo 'cat > .env << "EOF"' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_NAME="Solutech"' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_ENV=production' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_DEBUG=true' >> /usr/local/bin/entrypoint.sh && \
    echo "APP_KEY=${APP_KEY}" >> /usr/local/bin/entrypoint.sh && \
    echo "APP_URL=${APP_URL}" >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_CONNECTION=pgsql' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_HOST=dpg-d7ld2h77f7vs73b0or4g-a.oregon-postgres.render.com' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_PORT=5432' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_DATABASE=solutech' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_USERNAME=solutech_user' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_PASSWORD=vt21B1imfNpBSuIqIrZ00iPLzf9snF0UZ' >> /usr/local/bin/entrypoint.sh && \
    echo 'DB_SSLMODE=require' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'LOG_CHANNEL=stderr' >> /usr/local/bin/entrypoint.sh && \
    echo 'SESSION_DRIVER=database' >> /usr/local/bin/entrypoint.sh && \
    echo 'CACHE_STORE=database' >> /usr/local/bin/entrypoint.sh && \
    echo 'QUEUE_CONNECTION=database' >> /usr/local/bin/entrypoint.sh && \
    echo 'EOF' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== .env creado ==="' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Limpiar cache previa' >> /usr/local/bin/entrypoint.sh && \
    echo 'rm -rf bootstrap/cache/*.php' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan config:clear' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan cache:clear' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Reconstruir cache' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan config:cache' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Ejecutar migraciones' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Ejecutando migraciones..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan migrate --force || echo "Migraciones fallaron"' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== INICIANDO APACHE ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'apache2-foreground' >> /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]