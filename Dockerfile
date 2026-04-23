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

# ============================================
# CONFIGURACIÓN FORZADA DE POSTGRESQL
# ============================================

# Crear .env con la configuración CORRECTA
RUN echo "APP_ENV=production" > .env
RUN echo "APP_DEBUG=false" >> .env
RUN echo "APP_KEY=base64:dxo01MmyF5p05aU4XHZByHPD1PVr/Rn5jUw8sGSY=" >> .env
RUN echo "APP_URL=https://web-solutech-production.up.railway.app" >> .env

# Configuración MANUAL de PostgreSQL (usando valores fijos)
RUN echo "DB_CONNECTION=pgsql" >> .env
RUN echo "DB_HOST=postgres.railway.internal" >> .env
RUN echo "DB_PORT=5432" >> .env
RUN echo "DB_DATABASE=railway" >> .env
RUN echo "DB_USERNAME=postgres" >> .env
RUN echo "DB_PASSWORD=dhxHXcZXJgRxQYbgrNoiyXqbnlKMPBvu" >> .env
RUN echo "DB_SSLMODE=require" >> .env

# Mostrar configuración para debug
RUN echo "=== CONFIGURACIÓN FINAL ===" && cat .env

# Probar conexión manualmente
RUN php -r "\$pdo = new PDO('pgsql:host=postgres.railway.internal;port=5432;dbname=railway', 'postgres', 'dhxHXcZXJgRxQYbgrNoiyXqbnlKMPBvu', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); echo '✅ CONEXIÓN EXITOSA A POSTGRESQL\n'; \$stmt = \$pdo->query('SELECT current_database()'); echo 'Base de datos: ' . \$stmt->fetchColumn() . '\n';"

# Limpiar caché de Laravel
RUN php artisan config:clear
RUN php artisan config:cache

# Ejecutar migraciones
RUN php artisan migrate --force --verbose

# ============================================

# Instalar dependencias de Node.js
RUN npm install

# Compilar assets
RUN npm run build

# Optimizar Laravel
RUN php artisan optimize

# Establecer permisos
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000