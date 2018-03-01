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
 * Widget container
 *
 * @since 0.1.4.13
 *
 * @property PEPISCMS_Loader $load
 * @property ModuleRunner $modulerunner
 */
class WidgetRenderer extends ContainerAware
{
    protected $module_name;
    protected $method;

    /**
     * Default constructor
     *
     * @param string $module_name
     * @param string $method
     */
    public function __construct($module_name, $method)
    {
        $this->module_name = $module_name;
        $this->method = $method;

        $this->load->library('Logger');
        $this->load->library('ModuleRunner');
    }

    /**
     * Renders a widget and returns HTML
     *
     * @return string
     */
    public function render()
    {
        $controller_path = $this->modulepathresolver->getWidgetControllerPath($this->module_name);

        if ($controller_path) {
            require_once($controller_path);
            $class = ucfirst($this->module_name) . 'Widget';

            if (class_exists($class)) {
                if (!in_array(strtolower($this->method), array_map('strtolower', get_class_methods($class)))) {
                    $error_msg = 'No such widget as ' . $this->method . ' in class ' . $class . '.';
                    Logger::error($error_msg, 'WIDGET');
                    show_error($error_msg);
                }

                // You need this kind of "recurrence" in cases when you run a module from inside another module
                $previously_running_module = $this->modulerunner->getRunningModuleName();
                $this->modulerunner->setRunningModuleName($this->module_name);

                $this->load->moduleConfig($this->module_name);

                // Running now!
                $obj = new $class();
                if (!($obj instanceof Widget)) {
                    $error_msg = 'Unable to run widget ' . $this->module_name . '. Class ' . $class . ' found but does not extend Widget.';
                    Logger::error($error_msg, 'WIDGET');
                    show_error($error_msg);
                }

                $args = func_get_args();
                $contents = call_user_func_array(array($obj, $this->method), $args);

                $this->modulerunner->setRunningModuleName($previously_running_module);
                return $contents;
            } else {
                $error_msg = 'Unable to run widget ' . $this->module_name . '. Class ' . $class . ' not found.';
                Logger::error($error_msg, 'WIDGET');
                show_error($error_msg);
            }
        } else {
            $error_msg = 'Unable to run module ' . $this->module_name . '. Controller file ' . $controller_path . ' not found.';
            Logger::error($error_msg, 'MODULE');
            show_error($error_msg);
        }

        return false;
    }
}
