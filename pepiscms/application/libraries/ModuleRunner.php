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
 * Module runner responsible for creating a new context for module controllers
 *
 * @since 0.1.3
 */
class ModuleRunner extends ContainerAware
{
    /**
     * Name of the currently running module. When module action is finished, the value becomes FALSE
     * @var mixed
     */
    private $running_module = false;

    /**
     * Instance of modules
     *
     * @var object
     */
    private static $module_instance = null;

    /**
     * Default constructor, empty
     */
    public function __construct()
    {
        $this->load->library('Logger');
        $this->load->library('ModulePathResolver');
    }

    /**
     * Returns module instance
     *
     * @return object
     */
    public static function get_instance()
    {
        return self::$module_instance;
    }

    /**
     * Returns the name of the currently running module or FALSE
     * @return mixed
     */
    public function getRunningModuleName()
    {
        return $this->running_module;
    }

    /**
     * Sets the currently running module
     * This should be private
     * @param string|boolean $running_module
     */
    public function setRunningModuleName($running_module = false)
    {
        $this->running_module = $running_module;
    }

    /**
     * Runs admin panel of the specified module. The module must be installed
     *
     * @param string $module_name
     * @param string $method
     * @return bool
     */
    public function runAdminModule($module_name, $method)
    {
        $this->load->model('Module_model');
        if (!$module_name || !self::isModuleInstalled($module_name)) {
            return false;
        }

        $module_directory = $this->load->resolveModuleDirectory($module_name);

        if (!$module_directory) {
            $error_msg = 'Unable to run module ' . $module_name . '. Module directory ' . $module_directory . ' not found.';
            Logger::error($error_msg, 'MODULE');
            show_error($error_msg);
        }

        if (!$this->securitymanager->hasAccess($module_name, $method, $module_name)) {
            throw new \PiotrPolak\PepisCMS\Security\AccessDeniedException("Access denied");
        }

        $controller_path = $this->modulepathresolver->getAdminControllerPath($module_name);
        if ($controller_path !== false) {
            include_once($controller_path);

            $class = ucfirst($module_name) . 'Admin';

            if (class_exists($class)) {
                if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($class)))) {
                    show_404("{$class}/{$method}");
                }

                // You need this kind of "recurency" in cases when you run a module from inside another module
                $previously_running_module = $this->getRunningModuleName();
                $previously_running_module_instance = self::$module_instance;
                $this->setRunningModuleName($module_name);

                self::$module_instance = new $class();
                if (!(self::$module_instance instanceof ModuleAdminController)) {
                    $error_msg = 'Unable to run module ' . $module_name . '. Class ' . $class . ' found but does not extend ModuleAdminController.';
                    Logger::error($error_msg, 'MODULE');
                    show_error($error_msg);
                }

                call_user_func(array(self::$module_instance, $method));
                $this->setRunningModuleName($previously_running_module);
                self::$module_instance = $previously_running_module_instance;

                return true;
            } else {
                $error_msg = 'Unable to run module ' . $module_name . '. Class ' . $class . ' not found.';
                Logger::error($error_msg, 'MODULE');
                show_error($error_msg);
            }
        } else {
            $error_msg = 'Unable to run module ' . $module_name . '. Controller file not found.';
            Logger::error($error_msg, 'MODULE');
            show_error($error_msg);
        }

        return false;
    }

    /**
     * Runns the frontend of the specified module, the module must be specified
     *
     * @param string $module_name
     * @param string $method
     * @return bool
     */
    public function runModule($module_name, $method)
    {
        $this->load->model('Module_model');
        if (!$module_name || !self::isModuleInstalled($module_name)) {
            return false;
        }

        $module_directory = $this->load->resolveModuleDirectory($module_name);

        if (!$module_directory) {
            $error_msg = 'Unable to run module ' . $module_name . '. Module directory ' . $module_directory . ' not found.';
            Logger::error($error_msg, 'MODULE');
            show_error($error_msg);
        }

        $controller_path = $this->modulepathresolver->getPublicControllerPath($module_name);
        if ($controller_path !== false) {
            include_once($controller_path);

            $class = ucfirst($module_name);

            if (class_exists($class)) {
                if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($class)))) {
                    show_404("{$class}/{$method}");
                }

                // You need this kind of "recurency" in cases when you run a module from inside another module
                $previously_running_module = $this->getRunningModuleName();
                $previously_running_module_instance = self::$module_instance;
                $this->setRunningModuleName($module_name);

                $this->load->library('Document');
                $this->pluginpage = $this->document; // TODO Remove in future versions, just an alias

                $this->load->moduleConfig($module_name);

                // Running now!
                self::$module_instance = new $class();
                if (!(self::$module_instance instanceof ModuleController)) {
                    $error_msg = 'Unable to run module ' . $module_name . '. Class ' . $class . ' found but does not extend ModuleController.';
                    Logger::error($error_msg, 'MODULE');
                    show_error($error_msg);
                }

                call_user_func(array(self::$module_instance, $method));
                $this->setRunningModuleName($previously_running_module);
                self::$module_instance = $previously_running_module_instance;
                return true;
            } else {
                $error_msg = 'Unable to run module ' . $module_name . '. Class ' . $class . ' not found.';
                Logger::error($error_msg, 'MODULE');
                show_error($error_msg);
            }
        } else {
            $error_msg = 'Unable to run module ' . $module_name . '. Controller file not found.';
            Logger::error($error_msg, 'MODULE');
            show_error($error_msg);
        }

        return false;
    }

    /**
     * Returns the list of all available modules
     *
     * @return array
     */
    public static function getAvailableModules()
    {
        $modules_dir = INSTALLATIONPATH . 'modules/';
        $modules = scandir($modules_dir);
        $modules_out = array();

        foreach ($modules as &$module) {
            if (is_dir($modules_dir . $module) && $module{0} != '.') {
                $modules_out[] = $module;
            }
        }

        $modules_dir = APPPATH . '../modules/';
        $modules = scandir($modules_dir);

        foreach ($modules as &$module) {
            if (is_dir($modules_dir . $module) && $module{0} != '.') {
                $modules_out[] = $module;
            }
        }

        $modules_out = array_unique($modules_out);
        sort($modules_out);

        return $modules_out;
    }

    /**
     * Returns the list of modules displayed in main menu, cached
     *
     * @return array
     */
    public static function getInstalledModulesNamesDisplayedInMenuCached()
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        $object = $CI->cachedobjectmanager->getObject('module_names_in_menu', 3600 * 24, 'modules');
        if ($object === false) {
            $CI->load->model('Module_model');
            $object = $CI->Module_model->getInstalledModulesNamesDisplayedInMenu();
            $CI->cachedobjectmanager->setObject('module_names_in_menu', $object, 'modules');
        }
        return $object;
    }

    /**
     * Returns the list of modules displayed in main menu, cached
     *
     * @return array
     */
    public static function getInstalledModulesDisplayedInMenuCached()
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        $object = $CI->cachedobjectmanager->getObject('modules_in_menu', 3600 * 24, 'modules');
        if ($object === false) {
            $CI->load->model('Module_model');
            $object = $CI->Module_model->getInstalledModulesDisplayedInMenu();
            $CI->cachedobjectmanager->setObject('modules_in_menu', $object, 'modules');
        }
        return $object;
    }

    /**
     * Returns the list of installed modules names
     *
     * @return array
     */
    public static function getInstalledModulesNamesCached()
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        $object = $CI->cachedobjectmanager->getObject('module_names_installed', 3600 * 24, 'modules');
        if ($object === false) {
            $CI->load->model('Module_model');
            $object = $CI->Module_model->getInstalledModulesNames();
            $CI->cachedobjectmanager->setObject('module_names_installed', $object, 'modules');
        }
        return $object;
    }

    /**
     * Tells whether a module is installed
     *
     * @param $name
     * @return bool
     */
    public static function isModuleInstalled($name)
    {
        $object = self::getInstalledModulesNamesCached();
        return in_array($name, $object);
    }

    /**
     * Returns the list of modules displayed in utilities, cached
     *
     * @return array
     */
    public static function getInstalledModulesNamesDisplayedInUtilitiesCached()
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        $object = $CI->cachedobjectmanager->getObject('modules_in_utilities', 3600 * 24, 'modules');
        if ($object === false) {
            $CI->load->model('Module_model');
            $object = $CI->Module_model->getInstalledModulesNamesDisplayedInUtilities();
            $CI->cachedobjectmanager->setObject('modules_in_utilities', $object, 'modules');
        }
        return $object;
    }

    /**
     * Tells whether a module is installed and displayed in in utilitied
     *
     * @param $module_name
     * @return bool
     */
    public static function isModuleDisplayedInUtilities($module_name = false)
    {
        // No module name specified
        if (!$module_name) {
            $module_name = get_instance()->modulerunner->getRunningModuleName();
        }

        // No running module specified nor detected
        if (!$module_name) {
            return false;
        }

        $module_names = self::getInstalledModulesNamesDisplayedInUtilitiesCached();
        return in_array($module_name, $module_names);
    }

    /**
     * Tells whether a module is installed and displayed in menu
     *
     * @param $module_name
     * @return bool
     */
    public static function isModuleDisplayedInMenu($module_name = false)
    {
        // No module name specified
        if (!$module_name) {
            $module_name = get_instance()->modulerunner->getRunningModuleName();
        }

        // No running module specified nor detected
        if (!$module_name) {
            return false;
        }

        $module_names = self::getInstalledModulesNamesDisplayedInMenuCached();
        return in_array($module_name, $module_names);
    }

    /**
     * Returns parent module name
     * @param bool $module_name
     * @return bool|null
     */
    public static function getParentModuleName($module_name = false)
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        // No module name specified
        if (!$module_name) {
            $module_name = $CI->modulerunner->getRunningModuleName();
        }

        // No running module specified nor detected
        if (!$module_name) {
            return false;
        }

        // Cache key
        $cache_key = 'parent_module_name_' . $module_name;

        $object = $CI->cachedobjectmanager->getObject($cache_key, 3600 * 24, 'modules');
        if ($object === false) {
            // Need to store null not false
            $object = null;
            $parent_module = $CI->Module_model->getParentInfoByName($module_name);

            if ($parent_module) {
                $object = $parent_module->name;
            }
            $CI->cachedobjectmanager->setObject($cache_key, $object, 'modules');
        }
        return $object;
    }

    /**
     * Flushes module info cache
     *
     * @return array
     */
    public static function flushCache()
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        return $CI->cachedobjectmanager->cleanup('modules');
    }
}
