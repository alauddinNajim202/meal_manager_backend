FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy custom Apache virtual host
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy existing application directory contents
COPY . /var/www/html

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# PHP configuration for file uploads
RUN echo "upload_max_filesize = 25M\npost_max_size = 30M\nmemory_limit = 256M" > /usr/local/etc/php/conf.d/uploads.ini

# Set permissions for Laravel and public uploads
RUN mkdir -p /var/www/html/public/uploads /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
