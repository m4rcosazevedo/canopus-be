FROM php:8.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# 1. Instalar dependências do sistema + Bibliotecas do Chromium
# Adicionei as libs essenciais para o Chrome rodar "headless"
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    # Dependências do Chromium (essenciais para 2026)
    libnss3 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libdrm2 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    libgbm1 \
    libasound2 \
    libpangocairo-1.0-0 \
    libxshmfence1 \
    # Node.js para o Puppeteer \
    chromium \
    nodejs \
    npm

# 1.2 Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# 2. Instalar extensões PHP (mantive as suas e adicionei intl se precisar)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 3. Limpar cache para manter a imagem leve
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
# 4. Instalar Puppeteer globalmente (Economiza memória no build)
# O Puppeteer baixará o Chromium automaticamente
RUN npm install -g puppeteer --unsafe-perm=true

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /var/www

USER $user
