#!/bin/bash

# Uncomment if you need to pull any custom composer packages
#while [ ! -f /root/.ssh/id_rsa ] ;
#do
#    echo "Waiting for id_rsa before initializing composer git..."
#    sleep 1
#done

if [[ -z ${PEPIS_CMS_IS_UNATTENDED_INSTALL} ]]
then
    PEPIS_CMS_IS_UNATTENDED_INSTALL=TRUE
else
    PEPIS_CMS_IS_UNATTENDED_INSTALL=`echo $PEPIS_CMS_IS_UNATTENDED_INSTALL | tr a-z A-Z`
fi

IS_FIRST_INSTALL=TRUE

if [[ -e ./vendor/composer/ ]] ;
then
    IS_FIRST_INSTALL=FALSE
fi

if [[ ${IS_FIRST_INSTALL} == "TRUE" ]];
then
    if [[ ! -d /root/.ssh/ ]];
    then
        mkdir -p /root/.ssh/
    fi

    ssh-keyscan -H github.com >> /root/.ssh/known_hosts || (echo "Unable to add github key" && exit 1)
fi

# Just to make classmap scan work
if [[ ! -e pepiscms ]] ;
then
    ln -s /var/www/html/vendor/piotrpolak/pepiscms/pepiscms pepiscms || exit 9
fi

# Must be executed after linking PepisCMS
if [[ ${IS_FIRST_INSTALL} == "TRUE" ]];
then
    cp untouchable/composer.json .
    echo
    echo "Installing composer dependencies. This might take a while."
    composer install --prefer-dist --no-dev || exit 2
fi

if [[ ${IS_FIRST_INSTALL} == "TRUE" ]];
then

    if [[ ${PEPIS_CMS_IS_UNATTENDED_INSTALL} == "TRUE" ]];
    then
        if [[ ! -e ./index.php ]] ;
        then
            cp vendor/piotrpolak/pepiscms/pepiscms/resources/config_template/template_index.php ./index.php || exit 3
            sed -i -e 's/TEMPLATE_VENDOR_PATH/\.\/vendor\//g' ./index.php || exit 4
        fi

        if [[ ! -e ./.htaccess ]] ;
        then
            cp vendor/piotrpolak/pepiscms/pepiscms/resources/config_template/template_.htaccess ./.htaccess || exit 3
        fi

        CI_ENV=development php index.php tools install
        CI_ENV=development php index.php tools register_admin $PEPIS_CMS_AUTH_EMAIL $PEPIS_CMS_AUTH_PASSWORD
    else
        cp vendor/piotrpolak/pepiscms/install.php ./install.php || exit 3
        chmod -R 0777 ./ # Normally that would be 765 but there is an owner/group mismatch using the current Docker setup
    fi

    chmod 0777 -R application/cache/ application/logs/ modules/
fi

apache2-foreground