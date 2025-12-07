FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri -e 's!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}/>!g' /etc/apache2/apache2.conf && \
    a2enmod rewrite
COPY . /var/www/html/
ENV APP_DEBUG=false
EXPOSE 80