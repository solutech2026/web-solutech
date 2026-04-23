FROM php:8.3-cli

# Instalar Node.js, npm y extensiones necesarias (incluyendo libpq-dev para PostgreSQL)
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP para PostgreSQL (NO MySQL)
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
# LOGS PARA DEBUG - Verificar conexión a BD
# ============================================

# Crear archivo .env temporal con DATABASE_URL
RUN echo "APP_ENV=production" > .env
RUN echo "APP_KEY=base64:dxo01MmyF5p05aU4XHZByHPD1PVr/Rn5jUw8sGSY=" >> .env
RUN echo "DB_CONNECTION=pgsql" >> .env
RUN echo "DATABASE_URL=${DATABASE_URL}" >> .env

# Mostrar qué DATABASE_URL está usando
RUN echo "=== DATABASE_URL ===" && cat .env | grep DATABASE_URL

# Verificar conexión a PostgreSQL
RUN php -r "\$pdo = new PDO(getenv('DATABASE_URL')); echo '✅ Conexión exitosa a PostgreSQL\n';" || echo "❌ Error de conexión"

# Verificar qué driver está usando Laravel
RUN php artisan tinker --execute="echo 'Driver: ' . DB::connection()->getDriverName() . '\n';"

# Verificar nombre de la base de datos
RUN php artisan tinker --execute="echo 'Database: ' . DB::connection()->getDatabaseName() . '\n';"

# Verificar si hay tablas existentes
RUN php artisan tinker --execute="\$tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\''); echo 'Tablas encontradas: ' . count(\$tables) . '\n';"

# ============================================

# Ver contenido de package.json
RUN cat package.json

# Instalar dependencias de Node.js
RUN npm install

# Compilar assets
RUN npm run build

# Optimizar Laravel
RUN php artisan optimize

# Establecer permisos
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Ejecutar migraciones con verbose y mostrar logs
CMD php artisan migrate:status && \
    php artisan migrate --force --verbose && \
    php artisan serve --host=0.0.0.0 --port=8000