FROM php:8.3-apache
RUN apt-get update && \
    apt-get install -y zip \
        curl \
        unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite && \
    service apache2 restart
WORKDIR /var/www/larpcal
COPY composer.json /var/www/larpcal/composer.json
COPY composer.lock /var/www/larpcal/composer.lock
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer update