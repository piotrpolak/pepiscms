<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * MY_Config adds a default config path and possibility to load module config
 */
class PEPISCMS_Config extends CI_Config
{
    /**
     * Overwritten constructor adding an extra config path
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
            if (isset($_SERVER['HTTP_HOST']) && preg_match('/^((\[[0-9a-f:]+\])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-\.]+)(:\d+)?$/i', $_SERVER['HTTP_HOST'])) {
                $base_url = $protocol . '://' . $_SERVER['HTTP_HOST']
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
     * Load Config File
     *
     * @param string $module_name
     * @return bool
     */
    public function loadModuleConfig($module_name)
    {
        $file = INSTALLATIONPATH . 'application/config/modules/' . $module_name . '.php';

        if (in_array($file, $this->is_loaded, TRUE)) {
            return TRUE;
        }

        $this->is_loaded[] = $file;
        if (file_exists($file)) {
            include($file);
        } else {
            return FALSE;
        }

        if (!isset($config) OR !is_array($config)) {
            show_error('Your ' . $file . ' file does not appear to contain a valid configuration array.');
        }

        $this->config = array_merge($this->config, $config);


        unset($config);

        log_message('debug', 'Config file loaded: ' . $file);
        return TRUE;
    }
}
