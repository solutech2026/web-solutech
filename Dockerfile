FROM php:8.3-cli

# Instalar Node.js, npm y extensiones necesarias
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP para PostgreSQL
RUN docker-php-ext-install pdo_pgsql pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar archivos primero
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instalar dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs --no-scripts

# Copiar el resto del código
COPY . .

# Ejecutar scripts de composer
RUN composer run-script post-autoload-dump

# Instalar dependencias de Node.js
RUN npm install

# Compilar assets FORZADAMENTE
RUN npm run build || npx vite build

# Verificar que los assets se crearon
RUN ls -la public/build/ || echo "ERROR: Assets no compilados"

# Optimizar Laravel
RUN php artisan optimize

# Establecer permisos correctos
RUN chmod -R 775 storage bootstrap/cache public/build
RUN chmod -R 755 public/build

EXPOSE 8000

# Script de inicio CORREGIDO
RUN echo '#!/bin/bash\n\
# Variables de Railway\n\
echo "=== Configuración de Railway ==="\n\
echo "DATABASE_URL: ${DATABASE_URL}"\n\
echo "RAILWAY_PUBLIC_DOMAIN: ${RAILWAY_PUBLIC_DOMAIN}"\n\
\n\
# Crear .env solo con lo necesario\n\
cat > .env << EOF\n\
APP_ENV=production\n\
APP_DEBUG=false\n\
APP_KEY=base64:dxo01MmyF5p05aU4XHZByHPD1PVr/Rn5jUw8sGSY=\n\
APP_URL=https://${RAILWAY_PUBLIC_DOMAIN}\n\
ASSET_URL=https://${RAILWAY_PUBLIC_DOMAIN}\n\
DB_CONNECTION=pgsql\n\
DATABASE_URL=${DATABASE_URL}\n\
EOF\n\
\n\
# Verificar .env\n\
echo "=== Archivo .env ==="\n\
cat .env\n\
\n\
# Limpiar caché\n\
php artisan config:clear\n\
php artisan view:clear\n\
php artisan cache:clear\n\
\n\
# Cachear configuración\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Enlace simbólico para storage\n\
php artisan storage:link || true\n\
\n\
# Verificar que los assets existen\n\
echo "=== Verificando assets ==="\n\
ls -la public/build/ || echo "⚠️ Assets no encontrados"\n\
\n\
# Ejecutar migraciones\n\
php artisan migrate --force\n\
\n\
# Iniciar servidor\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]