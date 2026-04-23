FROM php:8.3-cli

# Instalar Node.js, npm y extensiones necesarias
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    nodejs \
    npm \
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

# Instalar dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs --no-scripts

# Copiar el resto del código
COPY . .

# Ejecutar scripts de composer
RUN composer run-script post-autoload-dump

# Instalar dependencias de Node.js
RUN npm ci --production || npm install --production

# Ver qué script de build existe
RUN cat package.json | grep build

# Compilar assets de React/Vite con verbose
RUN npm run build -- --verbose || (echo "Build failed. Checking for errors..." && exit 1)

# Optimizar Laravel
RUN php artisan optimize

# Establecer permisos
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000