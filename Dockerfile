FROM php:8.3-apache
# Install necessary packages
RUN apt-get update && \
    apt-get install -y zip \
        curl \
        unzip \
        locales \
        imagemagick && \
    docker-php-ext-install mysqli pdo pdo_mysql gettext
# Set up locales
RUN echo "sv_SE.UTF-8 UTF-8" >> /etc/locale.gen && \
    echo "nb_NO.UTF-8 UTF-8" >> /etc/locale.gen && \
    echo "da_DK.UTF-8 UTF-8" >> /etc/locale.gen && \
    echo "fi_FI.UTF-8 UTF-8" >> /etc/locale.gen && \
    echo "en_GB.UTF-8 UTF-8" >> /etc/locale.gen && \
    echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen
RUN locale-gen
# Set up vhost
COPY conf/larpcal.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default
RUN a2enmod rewrite
# Set up codebase
WORKDIR /var/www/
COPY composer.json /var/www/composer.json
COPY composer.lock /var/www/composer.lock
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer update
COPY ./src /var/www/src
COPY ./app /var/www/html
RUN chmod a+rwx -R /var/www/html/images
RUN service apache2 restart
# Run language format conversions
#WORKDIR /var/www/translations
#RUN ./translate.sh