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
 * MY_Config adds a default config path and possibility to load module config.
 */
class PEPISCMS_Config extends CI_Config
{
    const HOST_PATTERN = '/^((\[[0-9a-f:]+\])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-\.]+)(:\d+)?$/i';
    const HOST_SERVER_ATTRIBUTE = 'HTTP_HOST';

    /**
     * Overwritten constructor that parses and sets base_url and adn adds an extra config path.
     */
    public function __construct()
    {
        /**
         * IMPORTANT
         *
         * The following code comes from CI 3.0.2 as CI 3.0.3 have changed the base_url is computed for empty config value
         */
        $this->config =& get_config();

        // Set the base_url automatically if none was provided
        if (empty($this->config['base_url'])) {
            $protocol = (is_https() ? 'https' : 'http');

            // The regular expression is only a basic validation for a valid "Host" header.
            // It's not exhaustive, only checks for valid characters.
            if (isset($_SERVER[self::HOST_SERVER_ATTRIBUTE]) && preg_match(self::HOST_PATTERN, $_SERVER[self::HOST_SERVER_ATTRIBUTE])) {
                $base_url = $protocol . '://' . $_SERVER[self::HOST_SERVER_ATTRIBUTE]
                    . substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
            } else {
                $base_url = $protocol . '://localhost/';
            }

            $this->set_item('base_url', $base_url);
        }
        /** END */

        parent::__construct();
        $this->_config_paths = array_unique(array_merge($this->_config_paths, array(INSTALLATIONPATH . 'application/')));
    }

    // --------------------------------------------------------------------

    /**
     * Loads module config file.
     *
     * @param string $module_name
     * @return bool
     */
    public function loadModuleConfig($module_name)
    {
        $file = INSTALLATIONPATH . 'application/config/modules/' . $module_name . '.php';

        if (in_array($file, $this->is_loaded, true)) {
            return true;
        }

        $this->is_loaded[] = $file;
        if (file_exists($file)) {
            include($file);
        } else {
            return false;
        }

        if (!isset($config) or !is_array($config)) {
            show_error('Your ' . $file . ' file does not appear to contain a valid configuration array.');
        }

        $this->config = array_merge($this->config, $config);

        unset($config);

        log_message('debug', 'Config file loaded: ' . $file);
        return true;
    }

    /**
     * Returns config value from file, skipping Siteconfig_model.
     */
    public function raw_item($item, $index = '')
    {
        return parent::item($item, $index);
    }

    /**
     * @inheritdoc
     */
    public function item($item, $index = '')
    {
        $this->_overwrite_value_from_database($item);
        return parent::item($item, $index);
    }

    /**
     * @inheritdoc
     */
    public function slash_item($item)
    {
        $this->_overwrite_value_from_database($item);
        return parent::slash_item($item);
    }

    /**
     * @param $item
     */
    private function _overwrite_value_from_database($item)
    {
        if (class_exists('CI_Controller')) {
            if (!isset(CI_Controller::get_instance()->db)) {
                return;
            }

            if (!isset(CI_Controller::get_instance()->Siteconfig_model)) {
                CI_Controller::get_instance()->load->model('Siteconfig_model');
            }

            $value = CI_Controller::get_instance()->Siteconfig_model->getValueByNameCached($item);
            if ($value != null) {
                $this->set_item($item, $value);
            }
        }
    }


}
