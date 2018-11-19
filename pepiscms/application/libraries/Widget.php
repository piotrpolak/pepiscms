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
    public function __construct()
    {
        // For compatibility reasons - PHP 7.1 throws the below message

        /**
         * An uncaught Exception was encountered
         * Type: Error
         *
         * Message: Cannot call constructor
         *
         * Filename: /var/www/html/modules/events/events_widget.php
         */
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
     * @return Widget
     */
    public function assign($attributeName, $attributeValue)
    {
        $this->response_attributes[$attributeName] = $attributeValue;
        return $this;
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
