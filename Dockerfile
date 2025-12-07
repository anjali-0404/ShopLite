FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    zip unzip \
    && docker-php-ext-install pdo pdo_mysql

# Copy project files
WORKDIR /var/www/html
COPY . .

# Configure Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose the Render port
ENV PORT=8080
EXPOSE 8080

# Start Nginx + PHP-FPM
CMD service nginx start && php-fpm
