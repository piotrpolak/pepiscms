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
 * Widget provides support for widget elements.
 * Widgets are visual components that are rendered separately from the main template.
 * Allows better encapsulation of component logic. Widgets can be reused among different projects.
 *
 * @since 0.1.4.13
 *
 * @property PEPISCMS_Loader $load
 * @property ModuleRunner $modulerunner
 */
class Widget extends ContainerAware
{
    /**
     * Widget constructor.
     *
     * @param array|null $params
     */
    public function __construct($params = null)
    {
    }

    /**
     * Array containing context
     *
     * @var array
     */
    protected $response_attributes = array();

    /**
     * Assigns a value to a variable
     *
     * @param string $attributeName
     * @param object $attributeValue
     */
    public function assign($attributeName, $attributeValue)
    {
        $this->response_attributes[$attributeName] = $attributeValue;
    }

    /**
     * Returns value of the assigned parameter
     *
     * @param string $attributeName
     * @return mixed
     */
    public function getAttribute($attributeName)
    {
        return $this->response_attributes[$attributeName];
    }

    /**
     * Returns associative array representing response attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->response_attributes;
    }

    /**
     * Sets associative array representing response attributes
     *
     * @param array $response_attributes
     */
    public function setAttributes($response_attributes)
    {
        $this->response_attributes = $response_attributes;
    }

    /**
     * Loads and displays view
     *
     * @param string $view
     * @return string
     */
    public function display($view)
    {
        $module_directory = $this->load->resolveModuleDirectory($this->modulerunner->getRunningModuleName());

        $contents = $this->load->theme($module_directory . '/views/widget/' . $view . '.php', $this->response_attributes, true);

        // Resetting
        $this->response_attributes = array();

        return $contents;
    }

    /**
     * Creates a widget
     *
     * @param string $module_name
     * @param string $method
     * @return WidgetRenderer
     */
    public function create($module_name, $method)
    {
        return new WidgetRenderer($module_name, $method);
    }
}

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
