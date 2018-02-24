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
 * ContainerAware makes it possible to "inject" CodeIgniter services (models, libraries) into libraries so that you can
 * seemlesly use them inside your libraries just like if you are coding controllers or models/
 *
 * @since 1.0.0
 */
class ContainerAware
{
    /**
     * Returns service registered inside CodeIgniter container (controller)
     *
     * @param string $var
     * @return mixed
     */
    public function __get($var)
    {
        static $CI;
        isset($CI) OR $CI = CI_Controller::get_instance();
        return $CI->$var;
    }
}