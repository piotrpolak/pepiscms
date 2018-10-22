FROM php:5.6.38-apache-jessie

ADD ./install.sh /install.sh

RUN apt-get update && apt-get install -yq git zip unzip libpng-dev && \
    a2enmod rewrite && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/* && \
    php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm -rf /tmp/composer-setup.php && \
    rm -rf /var/www/html
