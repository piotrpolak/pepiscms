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
 * Widget provides support for widget elements.
 * Widgets are visual components that are rendered separately from the main template.
 * Allows better encapsulation of component logic. Widgets can be reused among different projects.
 *
 * @since 0.1.4.13
 */
class Widget
{
    /**
     * Widget constructor.
     *
     * @param array|null $params
     */
    public function __construct($params = NULL)
    {
    }

    /**
     * Array containing context
     *
     * @var array
     */
    protected $response_attributes = array();

    /**
     * This is magic
     *
     * @param string $var
     * @return mixed
     */
    function __get($var)
    {
        static $ci;
        isset($ci) OR $ci = get_instance();
        return $ci->$var;
    }

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

        $contents = $this->load->theme($module_directory . '/views/widget/' . $view . '.php', $this->response_attributes, TRUE);

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
 */
class WidgetRenderer
{

    protected $module_name, $method;

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
    }

    /**
     * Renders a widget and returns HTML
     *
     * @return string
     */
    public function render()
    {
        $file_suffix = '_widget';
        $class_suffix = 'Widget';
        $CI = get_instance();
        $CI->load->library('Logger');
        $CI->load->library('ModuleRunner');


        $module_directory = $CI->load->resolveModuleDirectory($this->module_name);
        $controller_file = $this->module_name . $file_suffix . '.php';

        if (file_exists($module_directory . '/' . $controller_file)) {
            // Including controller class definition
            include_once($module_directory . '/' . $controller_file);
            $class = ucfirst($this->module_name) . $class_suffix; // Building class name

            if (class_exists($class)) {
                if (!in_array(strtolower($this->method), array_map('strtolower', get_class_methods($class)))) {
                    $error_msg = 'No such widget as ' . $this->method . ' in class ' . $class . '.';
                    Logger::error($error_msg, 'WIDGET');
                    show_error($error_msg);
                }

                // You need this kind of "recurency" in cases when you run a module from inside another module
                $previously_running_module = $CI->modulerunner->getRunningModuleName();
                $CI->modulerunner->setRunningModuleName($this->module_name);

                $CI->load->moduleConfig($this->module_name);

                // Running now!
                $obj = new $class();
                if (!($obj instanceof Widget)) {
                    $error_msg = 'Unable to run widget ' . $this->module_name . '. Class ' . $class . ' found but does not extend Widget.';
                    Logger::error($error_msg, 'WIDGET');
                    show_error($error_msg);
                }

                $args = func_get_args();
                $contents = call_user_func_array(array($obj, $this->method), $args);

                $CI->modulerunner->setRunningModuleName($previously_running_module);
                return $contents;
            } else {
                $error_msg = 'Unable to run widget ' . $this->module_name . '. Class ' . $class . ' not found.';
                Logger::error($error_msg, 'WIDGET');
                show_error($error_msg);
            }
        } else {
            $error_msg = 'Unable to run module ' . $this->module_name . '. Controller file ' . $controller_file . ' not found.';
            Logger::error($error_msg, 'MODULE');
            show_error($error_msg);
        }

        return FALSE;
    }

}
