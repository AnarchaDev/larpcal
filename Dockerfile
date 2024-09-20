FROM php:8.3-apache
RUN mkdir -p /var/www/larpcal
RUN mkdir -p /var/www/larpcal/app
RUN mkdir -p /var/www/larpcal/src
RUN chown -R www-data:www-data /var/www
RUN apt-get update && \
    apt-get install -y zip \
        curl \
        unzip && \
    docker-php-ext-install mysqli pdo pdo_mysql
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default
RUN a2enmod rewrite && \
    service apache2 restart
WORKDIR /var/www/larpcal
COPY composer.json /var/www/larpcal/composer.json
COPY composer.lock /var/www/larpcal/composer.lock
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer update
ENTRYPOINT ["php", "./app/index.php"]