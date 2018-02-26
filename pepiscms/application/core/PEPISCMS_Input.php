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
 * MY_Input, provides some extra features.
 */
class PEPISCMS_Input extends CI_Input
{
    /**
     * Current controller name.
     *
     * @var string
     */
    protected $controller;

    /**
     * Current method name.
     *
     * @var string
     */
    protected $method;

    /**
     * Representation of GET parameters, -1 meaning it is not initialized.
     * @var Array
     */
    protected $get_parameters = -1;

    /**
     * Returns valueof get specified parameter.
     * The params are of form /param1_name-param1_valie/param2_name-param2_valie/
     *
     * @param string $paramName
     * @return string
     */

    public function getParam($paramName)
    {
        if ($this->get_parameters == -1) {
            $CI = get_instance();
            $this->get_parameters = array();
            $total_segments = $CI->uri->total_segments();
            for ($i = 0; $i <= $total_segments; $i++) {
                $segment = $CI->uri->segment($i);
                if (strpos($segment, '-') !== false) {
                    $segment = explode('-', $segment, 2); // 2 is required here as we accept - in the valie
                    $this->get_parameters[$segment[0]] = $segment[1];
                }
            }
        }

        return (isset($this->get_parameters[$paramName]) ? $this->get_parameters[$paramName] : '');
    }

    /**
     * Returns current controller name.
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller;
    }

    /**
     * Returns current method name.
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->method;
    }

    /**
     * Sets current controller name
     * Use this method carefully, it is intended to be called by controller constructors only.
     *
     * @param string $controller
     */
    public function setControllerName($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Sets current method name
     * Use this method carefully, it is intended to be called by controller constructors only.
     *
     * @param string $method
     */
    public function setMethodName($method)
    {
        $this->method = $method;
    }
}
