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

if (!function_exists('is_windows')) {

    /**
     * Tells whether the host OS is MS Windows
     *
     * @return bool
     */
    function is_windows()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

}