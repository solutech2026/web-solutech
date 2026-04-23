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

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000