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
 * Site config model
 *
 * @since 0.1.5
 */
class Siteconfig_model extends Generic_model
{
    const MODULE_FIELD_NAME = 'module';
    const IS_BOOLEAN_FIELD_NAME = 'is_boolean';
    const NAME_FIELD_NAME = 'name';
    const UPDATED_DATETIME_FIELD_NAME = 'updated_datetime';
    const CREATED_DATETIME_FIELD_NAME = 'created_datetime';
    const VALUE_FIELD_NAME = 'value';
    const IS_SERIALIZED_FIELD_NAME = 'is_serialized';

    private $cache_ttl;
    private $cache_collection = 'siteconfig';
    private $cache_variable_name = 'all_pairs';


    public function __construct()
    {
        parent::__construct();
        $this->setTable('cms_siteconfig');
        $this->setIdFieldName('id');
//        $this->enableJournaling();

        $this->cache_ttl = 600;
        $this->cache_collection = 'siteconfig';
        $this->cache_variable_name = 'all_pairs';

        // Required by saveById method
        $this->setAcceptedPostFields(array(
                self::MODULE_FIELD_NAME,
                self::IS_BOOLEAN_FIELD_NAME,
                self::NAME_FIELD_NAME,
                self::UPDATED_DATETIME_FIELD_NAME,
                self::CREATED_DATETIME_FIELD_NAME,
                self::VALUE_FIELD_NAME,
                self::IS_SERIALIZED_FIELD_NAME
            )
        );

        // Clone database to avoid query clash
        $this->setDatabase('default');
    }

    /**
     * Saves by id, $data must be an associative array
     *
     * @param array $data
     * @return bool
     * @local
     */
    public function saveAllConfigurationVariables($data)
    {
        $data = array_merge((array)$this->getAllConfigurationVariables(), $data);
        $booleans = array(
            'cms_enable_frontend',
            'cms_intranet',
            'cms_enable_utilities',
            'cms_enable_filemanager',
            'debug_log_php_deprecated',
            'debug_log_php_warning',
            'debug_log_php_error',
            'email_use_smtp'
        );

        if (isset($data['cms_customization_logo_predefined']) && !empty($data['cms_customization_logo_predefined'])) {
            $customization_logo_path = APPPATH . '/../theme/img/customization_icons/' . $data['cms_customization_logo_predefined'];

            if (file_exists($customization_logo_path) || is_file($customization_logo_path)) {
                $customization_logo_path_new_name = 'customization_' . time() . '.png';

                $customization_logo_path_new_location = INSTALLATIONPATH . $this->config->item('theme_path') . $customization_logo_path_new_name;

                if (copy($customization_logo_path, $customization_logo_path_new_location)) {
                    $data['cms_customization_logo'] = $customization_logo_path_new_name;
                }
            }
        }

        if (isset($data['cms_customization_logo']) && $data['cms_customization_logo']) {
            $data['cms_customization_logo'] = $this->config->item('theme_path') . $data['cms_customization_logo'];
        } else {
            $data['cms_customization_logo'] = false;
        }

        foreach ($data as $key => $value) {
            $this->saveConfigByName($key, $value, in_array($key, $booleans));
        }

        return true;
    }

    /**
     * @return object
     */
    public function getAllConfigurationVariables()
    {
        @include(APPPATH . 'config/_pepiscms.php');
        @include(APPPATH . 'config/debug.php');
        @include(APPPATH . 'config/email.php');
        @include(INSTALLATIONPATH . 'application/config/_pepiscms.php');
        @include(INSTALLATIONPATH . 'application/config/debug.php');
        @include(INSTALLATIONPATH . 'application/config/email.php');

        if (isset($config['cms_customization_logo'])) {
            $config['cms_customization_logo'] = str_replace($this->config->item('theme_path'), '', $config['cms_customization_logo']);
        }

        $all = $this->getPairsForAll();

        $config = array_merge($config, $all);

        return (object)$config;
    }

    /**
     * Deletes by id, dummy
     *
     * @param mixed $id
     * @return bool
     * @local
     */
    public function deleteById($id)
    {
        return true;
    }

    /**
     * Returns a raw entry by name.
     *
     * @param $name
     * @return mixed
     */
    public function getByName($name)
    {
        return $this->db->select('*')->from($this->getTable())->where('name', $name)->get()->row();
    }

    /**
     * Returns config pairs for all all entries.
     *
     * @return array
     */
    public function getPairsForAll()
    {
        $output = array();
        $result = $this->getAll();
        foreach ($result as $line) {
            $output[$line->name] = $this->toValue($line);
        }

        return $output;
    }

    /**
     * Returns config pairs for all all entries.
     *
     * @return array
     */
    public function getPairsForModule($module_name)
    {
        $output = array();
        $result = $this->getAll(array('module' => $module_name));
        foreach ($result as $line) {
            $output[$line->name] = $this->toValue($line);
        }

        return $output;
    }

    /**
     * Returns config value. The method is cached for extra performance.
     *
     * @param $name
     * @return mixed|null
     */
    public function getValueByNameCached($name)
    {
        $this->load->library('Cachedobjectmanager');
        $output = $this->cachedobjectmanager->getObject($this->cache_variable_name, $this->cache_ttl, $this->cache_collection);

        if (!$output) {
            $output = $this->getPairsForAll();
            $this->cachedobjectmanager->setObject($this->cache_variable_name, $output, $this->cache_collection);
        }

        if (isset($output[$name])) {
            return $output[$name];
        }

        return null;
    }

    /**
     * Saves config entry by name.
     *
     * @param $name
     * @param $value
     * @param null $module
     * @return bool
     */
    public function saveConfigByName($name, $value, $module = null)
    {
        $entry = $this->getByName($name);

        $id = false;
        $updated_datetime = utc_timestamp();
        if ($entry) {
            $id = $entry->id;
        } else {
            $created_datetime = $updated_datetime;
        }

        $is_boolean = is_bool($value);

        if ($is_boolean) {
            $value_mapped = $value ? 'true' : 'false';
        } else {
            $value_mapped = $value;
        }


        $is_serialized = false;
        if (is_array($value) || is_object($value)) {
            $is_serialized = true;
            $value_mapped = json_encode($value);
        }


        $updated_datetime = utc_timestamp();

        $data = array(
            self::MODULE_FIELD_NAME => $module,
            self::IS_BOOLEAN_FIELD_NAME => $is_boolean,
            self::NAME_FIELD_NAME => $name,
            self::UPDATED_DATETIME_FIELD_NAME => $updated_datetime,
            self::VALUE_FIELD_NAME => $value_mapped,
            self::IS_SERIALIZED_FIELD_NAME => $is_serialized,
        );

        if (isset($created_datetime)) {
            $data[self::CREATED_DATETIME_FIELD_NAME] = $created_datetime;
        }

        return $this->saveById($id, $data);
    }

    /**
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function saveById($id, $data)
    {
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup($this->cache_collection);

        return parent::saveById($id, $data);
    }

    /**
     * Returns the list of available themes
     *
     * @return array
     */
    public function getAvailableThemes()
    {
        $theme_path = $this->config->item('theme_path');
        $dir = opendir($theme_path);

        $themes = array();

        while ($file = readdir($dir)) {
            if ($file{0} == '.') {
                continue;
            }

            if (is_dir($theme_path . $file)) {
                $themes[$file] = $file;
            }
        }
        closedir($dir);

        return $themes;
    }

    /**
     * Returns a list of customization icons
     *
     * @return array
     */
    public function getPredefinedIconsNames()
    {
        $output = array();
        $customization_icons = glob(APPPATH . '/../theme/img/customization_icons/*.png');

        foreach ($customization_icons as $customization_icon) {
            $customization_icon = basename($customization_icon);
            $output[$customization_icon] = $customization_icon;
        }

        return $output;
    }

    /**
     * Returns a list of available timezone
     *
     * @return array
     */
    public function getAvailableTimezones()
    {
        $tz = array(
            'Europe/Amsterdam',
            'Europe/Andorra',
            'Europe/Athens',
            'Europe/Belfast',
            'Europe/Belgrade',
            'Europe/Berlin',
            'Europe/Bratislava',
            'Europe/Brussels',
            'Europe/Bucharest',
            'Europe/Budapest',
            'Europe/Chisinau',
            'Europe/Copenhagen',
            'Europe/Dublin',
            'Europe/Gibraltar',
            'Europe/Guernsey',
            'Europe/Helsinki',
            'Europe/Isle_of_Man',
            'Europe/Istanbul',
            'Europe/Jersey',
            'Europe/Kaliningrad',
            'Europe/Kiev',
            'Europe/Lisbon',
            'Europe/Ljubljana',
            'Europe/London',
            'Europe/Luxembourg',
            'Europe/Madrid',
            'Europe/Malta',
            'Europe/Mariehamn',
            'Europe/Minsk',
            'Europe/Monaco',
            'Europe/Moscow',
            'Europe/Nicosia',
            'Europe/Oslo',
            'Europe/Paris',
            'Europe/Podgorica',
            'Europe/Prague',
            'Europe/Riga',
            'Europe/Rome',
            'Europe/Samara',
            'Europe/San_Marino',
            'Europe/Sarajevo',
            'Europe/Simferopol',
            'Europe/Skopje',
            'Europe/Sofia',
            'Europe/Stockholm',
            'Europe/Tallinn',
            'Europe/Tirane',
            'Europe/Tiraspol',
            'Europe/Uzhgorod',
            'Europe/Vaduz',
            'Europe/Vatican',
            'Europe/Vienna',
            'Europe/Vilnius',
            'Europe/Volgograd',
            'Europe/Warsaw',
            'Europe/Zagreb',
            'Europe/Zaporozhye',
            'Europe/Zurich');

        $timezones = array();
        foreach ($tz as $k) {
            $timezones[$k] = $k;
        }

        return $timezones;
    }

    /**
     * Returns a list of available admin languages
     *
     * @return array
     */
    public function getAvailableAdminLanguages()
    {
        $lng = $this->config->item('enabled_languages');
        $languages = array();
        foreach ($lng as $k) {
            $languages[$k] = ucfirst($k);
        }

        return $languages;
    }

    /**
     * Returns default admin language
     *
     * @return string
     */
    public function getDefaultAdminLanguage()
    {
        $obj = $this->getByName('language');
        if ($obj) {
            return $obj->value;
        }

        include(INSTALLATIONPATH . 'application/config/_pepiscms.php');

        return $config['language'];
    }

    /**
     * Does configuration test and tells whenever it has errors
     *
     * @return bool
     */
    public function hasConfigurationAnyErrors()
    {
        $errors = $this->makeConfigurationTestsAngGetErrors();
        foreach ($errors as $error) {
            if ($error) {
                return true;
            }
        }
        return false;
    }

    /**
     * Does configuration tests and returns an array of errors
     *
     * @return array
     */
    public function makeConfigurationTestsAngGetErrors()
    {
        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }

        $logs_path = INSTALLATIONPATH . 'application/logs/';

        $configuration_tests = array();
        $configuration_tests['error_php_reporting_enabled'] = ENVIRONMENT !== 'production' && PEPISCMS_PRODUCTION_RELEASE;
        $configuration_tests['error_development_release'] = !PEPISCMS_PRODUCTION_RELEASE;
        $configuration_tests['error_wrong_uri_protocol'] = $this->config->item('uri_protocol') != 'QUERY_STRING';
        $configuration_tests['error_cache_not_writeable'] = false;
        $configuration_tests['error_logs_not_writeable'] = false;

        // Extended cache test
        if (!is_writeable($cache_path)) {
            $configuration_tests['error_cache_not_writeable'] = true;
        } else {
            $tmp_cache_path = $cache_path . '/_test_' . rand(9999, 10000) . '/';
            if (!mkdir($tmp_cache_path)) {
                $configuration_tests['error_cache_not_writeable'] = true;
            } else {
                if (!rmdir($tmp_cache_path)) {
                    $configuration_tests['error_cache_not_writeable'] = true;
                }
            }
        }

        // Extended logs test
        if (!is_writeable($logs_path)) {
            $configuration_tests['error_logs_not_writeable'] = true;
        } else {
            $tmp_logs_path = $logs_path . '/_test_' . rand(9999, 10000) . '.php';
            if (!touch($tmp_logs_path)) {
                $configuration_tests['error_logs_not_writeable'] = true;
            } else {
                if (!unlink($tmp_logs_path)) {
                    $configuration_tests['error_logs_not_writeable'] = true;
                }
            }
        }


        $configuration_tests['error_wrong_file_owner'] = false;
        if (function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid(fileowner(INSTALLATIONPATH));
            $whoaim = exec('whoami');
            $configuration_tests['error_wrong_file_owner'] = ($whoaim != $owner['name'] && $whoaim != $owner['uid']);
        }

        return $configuration_tests;
    }

    /**
     * Returns an array containing the names of failed tests
     *
     * @return array
     */
    public function makeConfigurationTestsAngGetFailedTests()
    {
        $tests = $this->makeConfigurationTestsAngGetErrors();
        $failed_tests = array();

        foreach ($tests as $test_name => $is_error) {
            if ($is_error) {
                $failed_tests[] = $test_name;
            }
        }

        return $failed_tests;
    }

    /**
     * @param $obj
     * @return mixed
     */
    private function toValue($obj)
    {
        if ($obj->is_serialized) {
            return json_decode($obj->value);
        } elseif ($obj->is_boolean) {
            if (strtolower($obj->value) === 'true') {
                return true;
            }

            return false;
        } else {
            return $obj->value;
        }
    }

    //    /**
//     * @param $data
//     * @param $booleans
//     * @param $config_files
//     * @return string
//     * @deprecated
//     */
//    private function writeToDisk($data, $booleans, $config_files)
//    {
//        $config_files = array('_pepiscms.php', 'debug.php', 'email.php');
//
//        $error = false;
//        $config_search = $config_replace = array();
//        foreach ($data as $key => $value) {
//            if (in_array($key, $booleans)) {
//                $value = $value > 0 ? 'TRUE' : 'FALSE';
//            }
//
//            $config_search[] = 'TEMPLATE_' . strtoupper($key);
//            $config_replace[] = $value;
//        }
//
//        foreach ($config_files as $config_file) {
//            $content_config = file_get_contents(APPPATH . '../resources/config_template/template_' . $config_file);
//
//            if (!$content_config) {
//                $error = 'Unable to read template_' . $config_file;
//            }
//
//            if (!isset($error)) {
//                $config_path = INSTALLATIONPATH . 'application/config/' . $config_file;
//                if (!file_put_contents($config_path, str_replace($config_search, $config_replace, $content_config))) {
//                    $error = 'Unable to write ' . $config_file;
//                }
//                \PiotrPolak\PepisCMS\Modulerunner\OpCacheUtil::safeInvalidate($config_path);
//            }
//        }
//        return $error;
//    }
}
