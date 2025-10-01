# ----------------------------------------
# Stage 1: Build frontend assets with Vite
# ----------------------------------------
    FROM node:20-alpine AS vite-builder

    WORKDIR /app
    
    # Copy only package.json and lock first (for caching)
    COPY package*.json ./
    
    RUN npm install
    
    # Copy resources and config files for vite
    COPY resources ./resources
    COPY vite.config.* ./
    COPY tailwind.config.* ./
    COPY postcss.config.* ./
    
    RUN npm run build
    
    
    # ----------------------------------------
    # Stage 2: Setup Laravel App
    # ----------------------------------------
    FROM php:8.2-apache AS app
    
    # Install required system packages and PHP extensions (no mysql)
    RUN apt-get update && apt-get install -y \
        unzip git curl libpq-dev libonig-dev libzip-dev zip \
        && docker-php-ext-install pdo pdo_pgsql mbstring zip \
        && a2enmod rewrite
    
    WORKDIR /var/www/html

    # Set Apache DocumentRoot to Laravel's public folder
    RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

    
    # Copy composer
    COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
    
    # Copy Laravel files
    COPY . .
    
    # Install PHP dependencies
    RUN composer install --no-dev --optimize-autoloader
    
    # Copy built assets from vite-builder
    COPY --from=vite-builder /app/public/build ./public/build
    
    # Permissions for storage and cache
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    
    EXPOSE 80
    CMD ["apache2-foreground"]
    