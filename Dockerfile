FROM php:8.2-fpm

# Install Nginx and PHP extensions
RUN apt-get update && apt-get install -y nginx \
    && docker-php-ext-install pdo pdo_mysql

# Remove default nginx site
RUN rm -f /etc/nginx/sites-enabled/default

# Copy your PHP application
WORKDIR /var/www/html
COPY . .

# Copy custom Nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Render uses port 8080
ENV PORT=8080
EXPOSE 8080

CMD service nginx start && php-fpm
