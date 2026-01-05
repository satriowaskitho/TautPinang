# Stage 1: Node.js build for frontend assets
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Stage 2: Composer dependencies
FROM serversideup/php:8.3-cli AS composer-builder

USER root
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

# Stage 3: Production image
FROM serversideup/php:8.3-fpm-nginx

ENV PHP_OPCACHE_ENABLE=1

USER root
WORKDIR /var/www/html

# Copy application files from composer-builder
COPY --chown=www-data:www-data --from=composer-builder /app /var/www/html

# Copy built Vite assets from node-builder
COPY --chown=www-data:www-data --from=node-builder /app/public/build /var/www/html/public/build

# Create ALL necessary storage directories
RUN mkdir -p storage/app/public/logos \
    storage/app/public/livewire-tmp \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Remove existing symlink if present and create new one
RUN cd /var/www/html/public \
    rm -rf storage && \
    ln -s ../storage/app/public storage && \
    cd ..

# Set proper permissions (CRITICAL!)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/storage -type f -exec chmod 664 {} \; \
    && chmod -R 775 /var/www/html/bootstrap/cache

USER www-data

EXPOSE 8080
