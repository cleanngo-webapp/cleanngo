#!/bin/bash

# Create storage symlink if it doesn't exist
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "Creating storage symlink..."
    php artisan storage:link || ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
fi

# Ensure proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start Apache
exec apache2-foreground
