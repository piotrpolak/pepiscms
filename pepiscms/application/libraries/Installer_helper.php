<?php

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Installer_helper
 */
class Installer_helper extends ContainerAware
{
    const DEFAULT_INTRANET_APPLICATION_NO_FRONTEND = 0;
    const INTRANET_APPLICATION_WITH_FRONTEND_SUPPORT = 1;
    const FULL_FEATURED_WEB_CMS = 2;

    const ENV_PEPIS_CMS_AUTH_DRIVER = 'PEPIS_CMS_AUTH_DRIVER';
    const ENV_PEPIS_CMS_AUTH_EMAIL = 'PEPIS_CMS_AUTH_EMAIL';
    const ENV_PEPIS_CMS_AUTH_PASSWORD = 'PEPIS_CMS_AUTH_PASSWORD';
    const ENV_PEPIS_CMS_SITE_NAME = 'PEPIS_CMS_SITE_NAME';
    const ENV_PEPIS_CMS_SITE_EMAIL = 'PEPIS_CMS_SITE_EMAIL';
    const ENV_PEPIS_CMS_CMS_INSTANCE = 'PEPIS_CMS_CMS_INSTANCE';
    const ENV_PEPIS_CMS_DATABASE_DATABASE = 'PEPIS_CMS_DATABASE_DATABASE';
    const ENV_PEPIS_CMS_DATABASE_PASSWORD = 'PEPIS_CMS_DATABASE_PASSWORD';
    const ENV_PEPIS_CMS_DATABASE_USERNAME = 'PEPIS_CMS_DATABASE_USERNAME';
    const ENV_PEPIS_CMS_DATABASE_HOSTNAME = 'PEPIS_CMS_DATABASE_HOSTNAME';
    const ENV_PEPIS_CMS_DATABASE_CONFIG_TYPE = 'PEPIS_CMS_DATABASE_CONFIG_TYPE';
    const ENV_PEPIS_CMS_OBJECT_CACHE_OBJECT_IS_ENABLED = 'PEPIS_CMS_OBJECT_CACHE_OBJECT_IS_ENABLED';

    /**
     * @param $base_path
     * @throws Exception
     */
    public function buildFileStructure($base_path)
    {
        $mainpage_module_path = $base_path . 'modules/mainpage/';

        $files_template_path = realpath(dirname(realpath(__FILE__)) . '/../../resources/files_template/') . '/';

        $directories_to_create = array(
            $base_path,
            $base_path . 'theme/',
            $base_path . 'application/',
            $base_path . 'modules/',
            $base_path . 'application/cache/',
            $base_path . 'application/config/',
            $base_path . 'application/config/modules/',
            $base_path . 'theme/',
            $base_path . 'theme/dummy/',
            $base_path . 'uploads/',

            $mainpage_module_path,
            $mainpage_module_path . 'resources/',
            $mainpage_module_path . 'views/',
            $mainpage_module_path . 'views/public/'

        );

        foreach ($directories_to_create as $directory) {
            if (file_exists($directory)) {
                if (is_dir($directory)) {
                    continue;
                } else {
                    throw new Exception('Unable to create directory ' . $directory . '. File already exists.');
                }
            }

            if (!mkdir($directory)) {
                throw new Exception('Unable to create directory ' . $directory);
            }
        }


        copy($files_template_path . 'robots.txt', $base_path . 'robots.txt');

        if (!file_exists($base_path . 'theme/dummy/index.php')) {
            copy($files_template_path . '/theme/dummy/index.php', $base_path . 'theme/dummy/index.php');
        }
    }

    /**
     * @param $data
     * @param $base_path
     * @return bool|string
     */
    public function writeConfigFiles($data, $base_path)
    {
        list($hostname, $port) = $this->explodeHostname($data['hostname']);

        $templates_base_path = realpath(dirname(realpath(__FILE__)) . '/../../resources/config_template/') . '/';

        $config_map = array(
            'TEMPLATE_CACHE_EXPIRES' => 0,
            'TEMPLATE_DEFAULT_LANGUAGE' => $data['default_language'],
            'TEMPLATE_SITE_NAME' => $data['site_name'],
            'TEMPLATE_SITE_EMAIL' => $data['site_email'],
            'TEMPLATE_CURRENT_THEME' => $data['default_theme'],
            'TEMPLATE_CMS_CUSTOMIZATION_LOGO' => '',
            'TEMPLATE_CMS_CUSTOMIZATION_SUPPORT_LINK' => '',
            'TEMPLATE_CMS_CUSTOMIZATION_SUPPORT_LINE' => '',
            'TEMPLATE_CMS_CUSTOMIZATION_ON_LOGIN_REDIRECT_URL' => '',
            'TEMPLATE_CMS_CUSTOMIZATION_LOGIN_VIEW_PATH' => '',
            'TEMPLATE_CMS_LOGIN_PAGE_DESCRIPTION' => '',
            'TEMPLATE_CMS_INTRANET' => ($data['cms_instance_type'] == self::INTRANET_APPLICATION_WITH_FRONTEND_SUPPORT ? 'TRUE' : 'FALSE'),
            'TEMPLATE_CMS_ENABLE_FRONTEND' => ($data['cms_instance_type'] != self::DEFAULT_INTRANET_APPLICATION_NO_FRONTEND ? 'TRUE' : 'FALSE'),
            'TEMPLATE_CMS_ENABLE_UTILITIES' => 'TRUE',
            'TEMPLATE_CMS_ENABLE_FILEMANAGER' => 'TRUE',
            'TEMPLATE_EMAIL_USE_SMTP' => 'FALSE',
            'TEMPLATE_EMAIL_SMTP_HOST' => '',
            'TEMPLATE_EMAIL_SMTP_USER' => '',
            'TEMPLATE_EMAIL_SMTP_PASS' => '',
            'TEMPLATE_EMAIL_SMTP_PORT' => '25',
            'TEMPLATE_DEBUG_LOG_PHP_DEPRECATED' => 'FALSE',
            'TEMPLATE_DEBUG_LOG_PHP_WARNING' => 'FALSE',
            'TEMPLATE_DEBUG_LOG_PHP_ERROR' => 'FALSE',
            'TEMPLATE_DEBUG_MAINTAINER_EMAIL_ADDRESS' => '',
            'TEMPLATE_DB_DRIVER' => 'mysqli',
            'TEMPLATE_DB_HOST' => $hostname,
            'TEMPLATE_DB_USERNAME' => $data['username'],
            'TEMPLATE_DB_PASSWORD' => $data['password'],
            'TEMPLATE_DB_DATABASE' => $data['database'],
            'TEMPLATE_DB_PORT' => ($port ? $port : 'FALSE'),
            'TEMPLATE_TIMEZONE' => date_default_timezone_get(),
            'TEMPLATE_CMS_INTRANET' => 'FALSE',
            'TEMPLATE_SECRET_KEY' => substr(md5(rand(1000, 999) . time()), 0, 10),
            'TEMPLATE_AUTHENTIFICATION_DRIVER' => $data['authentification_driver'],
            'TEMPLATE_CAS_SERVER' => $data['cas_server'],
            'TEMPLATE_CAS_PORT' => $data['cas_port'],
            'TEMPLATE_CAS_PATH' => $data['cas_path'],
            'TEMPLATE_CMS_CUSTOMIZATION_SITE_PUBLIC_URL' => '',
            'TEMPLATE_OBJECT_CACHE_OBJECT_IS_ENABLED' => $data['cache_object_is_enabled'],
        );

        $config_search = array_keys($config_map);
        $config_replace = array_values($config_map);


        foreach ($config_replace as &$item) {
            $item = str_replace("'", '\\\'', $item);
        }

        $config_path = $base_path . 'application/config/';


        // TEMPLATE_CMS_ENABLE_FRONTEND

        $config_files = array(
            'database.php' => 'database.php',
            '_pepiscms.php' => '_pepiscms.php',
            'email.php' => 'email.php',
            'debug.php' => 'debug.php',
            'auth.php' => 'auth.php',
            'config.php' => 'config.php'
        );

        if ($data['database_config_type'] == 'symfony_import') {
            unset($config_files['database.php']);
            $config_files['database_symfony.php'] = 'database.php';
        }

        $error = false;


        foreach ($config_files as $input_config_file => $output_config_file) {
            $content_config_file = file_get_contents($templates_base_path . '/template_' . $input_config_file);
            if (!$content_config_file) {
                $error = 'Unable to read template_' . $input_config_file;
                break;
            }

            $output_config_file_path = $config_path . $output_config_file;

            if (!file_put_contents($output_config_file_path, str_replace($config_search, $config_replace, $content_config_file))) {
                $error = 'Unable to write ' . $output_config_file;
                break;
            }
        }
        return $error;
    }

    /**
     * @return array
     */
    public function getDefaultInstallationValues()
    {
        return array(
            'database_config_type' => getenv(self::ENV_PEPIS_CMS_DATABASE_CONFIG_TYPE) ?: 'symfony_import',
            'default_language' => 'english',
            'site_name' => getenv(self::ENV_PEPIS_CMS_SITE_NAME) ?: '',
            'site_email' => getenv(self::ENV_PEPIS_CMS_SITE_EMAIL) ?: '',
            'default_theme' => 'dummy',
            'cms_instance_type' => getenv(self::ENV_PEPIS_CMS_CMS_INSTANCE) ?: self::DEFAULT_INTRANET_APPLICATION_NO_FRONTEND,
            'hostname' => getenv(self::ENV_PEPIS_CMS_DATABASE_HOSTNAME) ?: 'localhost:3306',
            'database_config_type' => 'default',
            'username' => getenv(self::ENV_PEPIS_CMS_DATABASE_USERNAME) ?: '',
            'password' => getenv(self::ENV_PEPIS_CMS_DATABASE_PASSWORD) ?: '',
            'database' => getenv(self::ENV_PEPIS_CMS_DATABASE_DATABASE) ?: '',
            'authentification_driver' => getenv(self::ENV_PEPIS_CMS_AUTH_DRIVER) ?: 'native',
            'cas_server' => '',
            'cas_port' => '',
            'cas_path' => '',
            'admin_email' => getenv(self::ENV_PEPIS_CMS_AUTH_EMAIL) ?: '',
            'admin_password' => getenv(self::ENV_PEPIS_CMS_AUTH_PASSWORD) ?: '',
            'cache_object_is_enabled' => getenv(self::ENV_PEPIS_CMS_OBJECT_CACHE_OBJECT_IS_ENABLED) ?: 'true',
        );
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function writeDatabase($data)
    {
        $db = $this->load->database($this->getDefaultDatabaseConfig($data), true);

        $error = false;

        if (!$db) {
            $error = sprintf($this->line('installer_unable_to_establish_connection_to_database'), 'wrong configuration');
        } else {
            $db->trans_begin();
            $scripts = array('core.sql');

            $sql_basepath = APPPATH . '../resources/sql/';

            $upgradeScripts = array_filter(glob($sql_basepath . '/upgrade/*.sql'), 'is_file');
            foreach ($upgradeScripts as $upgradeScript) {
                $scripts[] = 'upgrade/' . basename($upgradeScript);
            }

            $queries = array();
            foreach ($scripts as $script) {
                $queries = array_merge($queries, self::getQueries($sql_basepath . $script));
            }

            foreach ($queries as $query) {
                $db->query($query);
            }

            if ($db->trans_status() === false) {
                $db->trans_rollback();

                show_error('Database error', 500, 'A Database Error Occurred');
            } else {
                $db->trans_commit();
            }
        }
        return $error;
    }

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function registerAdmin($email, $password)
    {
        $email = strtolower($email);
        $this->load->database();
        if (!$this->User_model->emailExists($email)) {
            return $this->User_model->register($email, $email, false, $password, array(), true, false);
        }
        return false;
    }

    /**
     * @param $data
     * @return array
     */
    private function getDefaultDatabaseConfig($data)
    {
        list($hostname, $port) = $this->explodeHostname($data['hostname']);

        $database_config = array(
            'dsn' => '',
            'hostname' => $hostname,
            'username' => $data['username'],
            'password' => $data['password'],
            'database' => $data['database'],
            'dbdriver' => 'mysqli',
            'port' => $port,
            'dbprefix' => '',
            'pconnect' => false,
            'db_debug' => true, // MUST BE FALSE FOR ROLLBACK
            'cache_on' => false,
            'cachedir' => '',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '',
            'encrypt' => false,
            'compress' => false,
            'stricton' => false,
            'failover' => array(),
            'save_queries' => true
        );
        return $database_config;
    }

    /**
     * Returns queries from the specified file
     *
     * @param $filePath
     * @return array
     */
    private static function getQueries($filePath)
    {
        $contents = file_get_contents($filePath);

        $contents = str_replace("\n", ' ', $contents);
        $contents = str_replace("\r", '', $contents);

        $queries = array();

        $qq = explode(';', $contents);

        foreach ($qq as $query) {
            $query = trim($query);
            if (strlen($query) > 0) {
                $queries[] = $query;
            }
        }

        return $queries;
    }

    public function explodeHostname($hostname)
    {
        $port = 3306;
        $hostname = explode(':', $hostname);
        if (count($hostname) > 1) {
            $port = $hostname[1];
        }
        $hostname = $hostname[0];

        return array($hostname, $port);
    }
}
