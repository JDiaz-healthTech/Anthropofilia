# ==============================================
# Dockerfile para Anthropofilia
# Apache sirve desde /var/www/html/public
# ==============================================

FROM php:8.0-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# 3. Cambiar DocumentRoot de Apache a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Permitir .htaccess (AllowOverride All)
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# 5. Directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar archivos del proyecto
COPY . /var/www/html/

# 7. Crear carpetas necesarias y permisos
RUN mkdir -p storage/cache storage/logs public/uploads \
    && chown -R www-data:www-data /var/www/html

# 8. Exponer puerto
EXPOSE 80