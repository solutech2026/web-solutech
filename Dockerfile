FROM php:8.3-apache

# Instalar dependencias incluyendo Node.js y npm
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpq-dev \
    nodejs \
    npm \
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

# Instalar dependencias PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Configurar variable de entorno para asegurar HTTPS en Vite
ENV APP_ENV=production
ENV APP_URL=https://web-solutech.onrender.com

# Instalar dependencias Node.js y compilar Vite
RUN npm install && NODE_ENV=production npm run build

# Limpiar cache de configuración inicial
RUN php artisan config:clear

EXPOSE 80

# Script de inicio con seeders
RUN echo '#!/bin/bash' > /usr/local/bin/entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== INICIANDO APLICACION ==="' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Crear .env' >> /usr/local/bin/entrypoint.sh && \
    echo 'cat > .env << "EOF"' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_NAME=Solutech' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_ENV=production' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_DEBUG=false' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_KEY=base64:oi1nf/JUinLZtIzCfs5U4fb+okBmr6Uf/Q27VCvleqU=' >> /usr/local/bin/entrypoint.sh && \
    echo 'APP_URL=https://web-solutech.onrender.com' >> /usr/local/bin/entrypoint.sh && \
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
    echo 'CACHE_DRIVER=database' >> /usr/local/bin/entrypoint.sh && \
    echo 'EOF' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== .env creado ==="' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== LIMPIANDO CACHE ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan config:clear' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan config:cache' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan view:cache' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== EJECUTANDO MIGRACIONES ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan migrate --force' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== EJECUTANDO SEEDERS ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'if php artisan db:seed --force; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "✅ Seeders ejecutados correctamente"' >> /usr/local/bin/entrypoint.sh && \
    echo 'else' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "⚠️ Error en seeders, continuando..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== VERIFICANDO DATOS ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan tinker --execute="echo \"Usuarios: \" . App\\\\Models\\\\User::count();" 2>/dev/null || echo "No se pudo verificar"' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== INICIANDO APACHE ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'apache2-foreground' >> /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]