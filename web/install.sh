#!/bin/bash

# Uncomment if you need to pull any custom composer packages
#while [ ! -f /root/.ssh/id_rsa ] ;
#do
#    echo "Waiting for id_rsa before initializing composer git..."
#    sleep 1
#done

IS_FIRST_INSTALL=TRUE

if [ -e ./vendor/composer/ ] ;
then
    IS_FIRST_INSTALL=FALSE
fi

if [ $IS_FIRST_INSTALL == "TRUE" ];
then
    if [ ! -d /root/.ssh/ ];
    then
        mkdir -p /root/.ssh/
    fi

    ssh-keyscan -H github.com >> /root/.ssh/known_hosts || (echo "Unable to add github key" && exit 1)
fi

# Just to make classmap scan work
if [ ! -e pepiscms ] ;
then
    ln -s /var/www/html/vendor/piotrpolak/pepiscms/pepiscms pepiscms || exit 9
fi

# Must be executed after linking PepisCMS
if [ $IS_FIRST_INSTALL == "TRUE" ];
then
    echo
    echo "Installing composer dependencies. This might take a while."
    composer install --prefer-dist || exit 2
fi

if [ ! -e ./index.php ] ;
then
    cp vendor/piotrpolak/pepiscms/pepiscms/resources/config_template/template_index.php ./index.php || exit 3
    sed -i -e 's/TEMPLATE_VENDOR_PATH/\.\/vendor\//g' ./index.php || exit 4
fi

if [ ! -e ./.htaccess ] ;
then
    cp vendor/piotrpolak/pepiscms/pepiscms/resources/config_template/template_.htaccess ./.htaccess || exit 3
fi

if [ $IS_FIRST_INSTALL == "TRUE" ];
then
    php index.php tools install
    php index.php tools register_admin $PEPIS_CMS_AUTH_EMAIL $PEPIS_CMS_AUTH_PASSWORD

    chmod 0777 -R application/cache/ application/logs/
fi

apache2-foreground