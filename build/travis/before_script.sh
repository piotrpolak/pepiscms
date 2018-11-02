#!/bin/bash

mysql -e 'create database pepiscms;' && \
    sudo apt-get update && \
    sudo apt-get install apache2 libapache2-mod-fastcgi && \
    # enable php-fpm
    echo "" && \
    echo "Available php-fpm versions" && \
    sudo ls ~/.phpenv/versions/ && \
    echo "" && \

    sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf && \
    sudo a2enmod rewrite actions fastcgi alias && \
    echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini && \
    sudo sed -i -e "s,www-data,travis,g" /etc/apache2/envvars && \
    sudo chown -R travis:travis /var/lib/apache2/fastcgi && \
    ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm && \
    # configure apache virtual hosts
    sudo cp -f build/travis/travis-ci-apache /etc/apache2/sites-available/000-default.conf && \
    sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)/app?g" --in-place /etc/apache2/sites-available/000-default.conf && \

    mkdir app && cd app && cp -a ../vendor . && \
    cp -a ../build/travis/travis-composer.json ./composer.json && \

    cp ../pepiscms/resources/config_template/template_index.php ./index.php && \
    sed -i -e 's/TEMPLATE_VENDOR_PATH/\.\/vendor\//g' ./index.php && \
    cp ../pepiscms/resources/config_template/template_.htaccess ./.htaccess && \

    cp -a ../composer.lock . && \
    composer update piotrpolak/pepiscms --no-suggest && \

    # Replace PepisCMS core and composer.json
    rm -rf vendor/piotrpolak/pepiscms/pepiscms/ && cp -a ../pepiscms ./vendor/piotrpolak/pepiscms/pepiscms && \
    rm -rf vendor/piotrpolak/pepiscms/composer.json && cp -a ../composer.json ./vendor/piotrpolak/pepiscms/ && \
    rm -rf vendor/piotrpolak/pepiscms/behat.yml && cp -a ../behat.yml ./vendor/piotrpolak/pepiscms/ && \

    composer dump-autoload && \
    php index.php tools install && \
    php index.php tools register_admin $PEPIS_CMS_AUTH_EMAIL $PEPIS_CMS_AUTH_PASSWORD && \
    chmod 0777 -R application/cache/ application/logs/ && \

    sudo service apache2 restart && \
    curl http://localhost/ -s -f -o /dev/null || (echo "Apache test setup is down." && exit 1) && \

    cd ..