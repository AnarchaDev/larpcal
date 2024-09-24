FROM php:8.3-apache
RUN apt-get update && \
    apt-get install -y zip \
        curl \
        unzip \
        imagemagick && \
    docker-php-ext-install mysqli pdo pdo_mysql
COPY conf/larpcal.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default
RUN a2enmod rewrite
RUN service apache2 restart
WORKDIR /var/www/
COPY composer.json /var/www/composer.json
COPY composer.lock /var/www/composer.lock
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer update
COPY ./src /var/www/src
COPY ./app /var/www/html
RUN chmod a+rwx -R /var/www/html/images