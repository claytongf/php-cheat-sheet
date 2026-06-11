FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd intl opcache

# Install APCu and Redis via PECL and enable them
RUN pecl install apcu redis \
    && docker-php-ext-enable apcu redis

# Enable Apache mod_rewrite for router flexibility
RUN a2enmod rewrite

# Set working directory to standard Apache document root
WORKDIR /var/www/html

# Copy codebase
COPY . /var/www/html/

# Expose HTTP port
EXPOSE 80
