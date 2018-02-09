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
 * @link                http://www.pocd lak.ro/
 */

/**
 * Enhanced controller providing some extra features
 *
 */
abstract class EnhancedController extends CI_Controller
{
    /**
     * An array containing controller's context.
     *
     * @var array
     */
    protected $response_attributes = array();

    /**
     * Protects the view from being rendered for twice
     *
     * @var bool
     */
    protected $already_displayed = false;

    /**
     * EnhancedController constructor.
     */
    public function __construct()
    {
        if (!get_instance()) // Very important for modules!!
        {
            parent::__construct();
        }
        $this->output->enable_profiler($this->config->item('enable_profiler'));
    }

    /**
     * Returns current controller name.
     *
     * @return string
     */
    protected function getControllerName()
    {
        return $this->input->getControllerName();
    }

    /**
     * Returns current method name.
     *
     * @return string
     */
    protected function getMethodName()
    {
        return $this->input->getMethodName();
    }

    /**
     * Sets the current controller name.
     *
     * @param string $controller
     * @return mixed
     */
    protected function setControllerName($controller)
    {
        return $this->input->setControllerName($controller);
    }

    /**
     * Sets the current method name.
     *
     * @param string $method
     * @return mixed
     */
    protected function setMethodName($method)
    {
        return $this->input->setMethodName($method);
    }

    /**
     * Assigns a value to a variable.
     *
     * @param string $attributeName
     * @param mixed $attributeValue
     */
    public function assign($attributeName, $attributeValue)
    {
        $this->response_attributes[$attributeName] = $attributeValue;
    }

    /**
     * Returns value of the assigned parameter.
     *
     * @param string $attributeName
     * @return mixed
     */
    public function getAttribute($attributeName)
    {
        if (!isset($this->response_attributes[$attributeName])) {
            return NULL;
        }
        return $this->response_attributes[$attributeName];
    }

    /**
     * Returns associative array representing response attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->response_attributes;
    }

    /**
     * Sets associative array representing response attributes.
     *
     * @param $response_attributes
     */
    public function setAttributes($response_attributes)
    {
        $this->response_attributes = $response_attributes;
    }

    /**
     * Loads and displays view
     *
     * @param string|bool $view
     * @param bool $display_header
     * @param bool $display_footer
     * @return bool
     */
    public function display($view = false, $display_header = true, $display_footer = true)
    {
        if ($this->already_displayed)
            return FALSE;

        if (!$view) {
            $view = $this->uri->segment(1) . '/' . $this->uri->segment(2) . (strlen($this->uri->segment(3)) > 0 ? '_' . $this->uri->segment(3) : '');
        }

        $this->load->view($view, $this->response_attributes);

        // Reseting
        $this->response_attributes = array();
        $this->already_displayed = true;

        return TRUE;
    }
}
