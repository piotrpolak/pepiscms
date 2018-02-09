# Installation

There are two ways of installing PepisCMS: attended (user) and unattended (manual).

Before to proceed to any of the installation pocedures you must first configure composer.json dependencies
                                                                      
```json
{
    "name": "piotrpolak/sample",
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:piotrpolak/pepiscms.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:piotrpolak/pepiscms-3rdparty.git"
        }
    ],
    "require": {
        "piotrpolak/pepiscms": "dev-master"
    }
}

```

## Attended (user) installation
    
1. Install composer dependencies

    ```bash
    composer install --prefer-dist
    ```
    
2. Copy `install.php` file to root directory

     ```bash
        cp ./vendor/piotrpolak/pepiscms/install.php ./
     ```
     
3. Open `install.php` in your browser [http://localhost/install.php](http://localhost/install.php)

    This will create basic framework structure. Please follow the installation guide.

    * Directory view
    ![Directory view](screens/INSTALLATION_1.png)
   
    * Copy files and create framework structure
    ![Copy files](screens/INSTALLATION_2.png)
   
    * Database connection setup
    ![Database connection](screens/INSTALLATION_3.png)
   
    * Selecting authorization driver. You can choose from *native* (users are managed locally) or *CAS*
    ![Authorization driver](screens/INSTALLATION_4.png)
   
    * Configuring administrator account
    ![Administrator account](screens/INSTALLATION_5.png)
   
    * Configuring site options
    ![Site confguration](screens/INSTALLATION_6.png)
   
    * Configuring installed modules
    ![Installed modules](screens/INSTALLATION_7.png)
   
    * Success message
    ![Success](screens/INSTALLATION_8.png)
   
    * Dashboard
    ![Dashboard](screens/INSTALLATION_9.png)
    
## Unattended installation

PepisCMS can be configured in an unattended way.

The following BASH variables can be used to control the installation parameters:

```bash
# The variable values are empty by default. The below values are taken from docker-compose.yml file
PEPIS_CMS_DATABASE_CONFIG_TYPE=native
PEPIS_CMS_DATABASE_HOSTNAME=db
PEPIS_CMS_DATABASE_USERNAME=pepiscms
PEPIS_CMS_DATABASE_PASSWORD=pepiscms
PEPIS_CMS_DATABASE_DATABASE=pepiscms
PEPIS_CMS_AUTH_DRIVER=native
PEPIS_CMS_AUTH_EMAIL=piotr@polak.ro
PEPIS_CMS_AUTH_PASSWORD=demodemo
PEPIS_CMS_SITE_EMAIL=piotr@polak.ro
PEPIS_CMS_SITE_NAME=Demonstration
```

The command line for unattended installation:

```bash
composer install --prefer-dist && \
    cp vendor/piotrpolak/pepiscms/pepiscms/resources/config_template/template_index.php ./index.php && \
    sed -i -e 's/TEMPLATE_VENDOR_PATH/\.\/vendor\//g' ./index.php && \
    cp vendor/piotrpolak/pepiscms/pepiscms/resources/config_template/template_.htaccess ./.htaccess && \
    php index.php tools install && \
    php index.php tools register_admin $PEPIS_CMS_AUTH_EMAIL $PEPIS_CMS_AUTH_PASSWORD
```