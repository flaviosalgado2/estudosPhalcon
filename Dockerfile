# Use a base PHP 8.5.8 image
FROM php:8.4.23-fpm

# adotado Node version LTS
ARG NODE_VERSION=24
ARG PHALCON_VERSION=5.17.0

# Install system dependencies
RUN apt-get update && apt-get install -y \
    procps \
    git \
    curl \
    zip \
    unzip \
    nano \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libzip-dev \
    libfreetype6-dev \
    libpq-dev \
    libpcre2-dev \
    build-essential \
    autoconf \
    pkg-config \
    tzdata && \
    ln -fs /usr/share/zoneinfo/America/Belem /etc/localtime && \
    dpkg-reconfigure --frontend noninteractive tzdata

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring zip exif pcntl gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Adicionar Composer global bin ao PATH
ENV PATH="$PATH:/root/.composer/vendor/bin"

# Install Phalcon extension from source
RUN cd /tmp \
    && git clone --depth 1 --branch v${PHALCON_VERSION} https://github.com/phalcon/cphalcon.git \
    && cd cphalcon/build \
    && ./install \
    && docker-php-ext-enable phalcon \
    && rm -rf /tmp/cphalcon

# Install Node.js, npm, and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && npm install -g yarn

# Set working directory
WORKDIR /var/www

# Expose ports
EXPOSE 8000 9003

# Start PHP-FPM
CMD ["php-fpm"]