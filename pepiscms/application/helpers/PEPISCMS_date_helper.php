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

if (!function_exists('utc_timestamp')) {

    /**
     * Generates timestamp, equivalent of MySQL UTC_TIMESTAMP function
     *
     * @return string
     */
    function utc_timestamp()
    {
        return gmdate('Y-m-d H:i:s');
    }

}
