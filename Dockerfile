FROM php:8.3-cli

# Instalar Node.js 20 y extensiones necesarias
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mysqli

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de dependencias primero
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instalar dependencias de PHP - LÍNEA CORREGIDA
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Instalar dependencias de Node.js
RUN npm ci --production || npm install --production

# Copiar el resto del código
COPY . .

# Compilar assets
RUN npm run build

# Optimizar Laravel
RUN php artisan optimize

# Establecer permisos
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000