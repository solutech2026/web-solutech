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

# Copiar archivos de dependencias primero (para optimizar caché)
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instalar dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Instalar dependencias de Node.js
RUN npm ci

# Copiar el resto del código
COPY . .

# Compilar assets de React/Vite
RUN npm run build

# Limpiar y optimizar Laravel
RUN php artisan config:clear
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan package:discover --ansi

# Establecer permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer el puerto
EXPOSE 8000

# Comando para iniciar la aplicación
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000