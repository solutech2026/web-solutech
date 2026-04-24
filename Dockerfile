FROM php:8.3-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite
RUN service apache2 stop

WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Instalar dependencias PHP (ignorando platform reqs para Railway)
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Instalar assets frontend
RUN npm install && npm run build || true

# Limpiar y cachear configuración
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan view:clear \
    && php artisan route:clear

# Crear script de entrada personalizado
RUN echo '#!/bin/bash\n\
\necho "Waiting for database to be ready..."\n\
sleep 5\n\
\necho "Running migrations..."\n\
php artisan migrate --force\n\
\necho "Caching config..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\necho "Starting Apache..."\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]