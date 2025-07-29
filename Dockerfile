# Stage 1: Install PHP dependencies using Composer
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
COPY database/ database/
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Stage 2: Build frontend assets using Node.js
FROM node:20 AS frontend

WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/
RUN npm install
RUN npm run build

# Stage 3: Prepare production image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql bcmath

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/
COPY . .

# Expose correct port for Railway (port is auto-mapped)
ENV PORT=8080
EXPOSE 8080

# Create cache and view directories and fix permissions
RUN mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Set Apache config
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf


