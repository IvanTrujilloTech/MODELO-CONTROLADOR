FROM php:8.1-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Set DocumentRoot to /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/000-default.conf

# Install libzip-dev for zip extension
RUN apt-get update && apt-get install -y libzip-dev

# Install PDO MySQL and zip extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install git and unzip
RUN apt-get update && apt-get install -y git unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader