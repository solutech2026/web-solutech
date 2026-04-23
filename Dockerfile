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

# Compilar assets
RUN npm run build

# Optimizar Laravel (sin base de datos)
RUN php artisan optimize

# Establecer permisos
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# ============================================
# TODO ESTO SE EJECUTA AL INICIAR EL CONTENEDOR
# ============================================

# Crear script de inicio
RUN echo '#!/bin/bash\n\
# Crear .env con variables de Railway\n\
echo "APP_ENV=production" > .env\n\
echo "APP_DEBUG=false" >> .env\n\
echo "APP_KEY=base64:dxo01MmyF5p05aU4XHZByHPD1PVr/Rn5jUw8sGSY=" >> .env\n\
echo "APP_URL=${RAILWAY_PUBLIC_DOMAIN}" >> .env\n\
echo "DB_CONNECTION=pgsql" >> .env\n\
echo "DB_HOST=postgres.railway.internal" >> .env\n\
echo "DB_PORT=5432" >> .env\n\
echo "DB_DATABASE=railway" >> .env\n\
echo "DB_USERNAME=postgres" >> .env\n\
echo "DB_PASSWORD=dhxHXcZXJgRxQYbgrNoiyXqbnlKMPBvu" >> .env\n\
echo "DB_SSLMODE=require" >> .env\n\
\n\
# Limpiar y cachear configuración\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
# Ejecutar migraciones\n\
php artisan migrate --force\n\
\n\
# Iniciar servidor\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]