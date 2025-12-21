# Usamos PHP 8.0 con Apache (coincide con tu SQL)
FROM php:8.0-apache

# 1. Instalar dependencias del sistema necesarias
# libpng, libjpeg, libwebp, libfreetype son OBLIGATORIAS para tu upload_image.php
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli zip

# 2. Habilitar mod_rewrite para tus URLs amigables
RUN a2enmod rewrite

# 3. Configurar directorio de trabajo
WORKDIR /var/www/html

# 4. Copiar los archivos del proyecto
COPY . /var/www/html/

# 5. Crear carpeta de uploads si no existe y dar permisos
# Tu script sube a /uploads/YYYY/mm/, así que damos permisos recursivos
RUN mkdir -p uploads && chown -R www-data:www-data /var/www/html

# 6. Exponer puerto (informativo)
EXPOSE 80