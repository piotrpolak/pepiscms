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

if (!function_exists('module_path')) {

    /**
     * Returns module base path
     *
     * @param bool|string $module_name if set to false, the current module is read and used
     * @return string
     */
    function module_path($module_name = FALSE)
    {
        $CI = get_instance();
        if (!$module_name) {
            if (isset($CI->modulerunner)) {
                $module_name = $CI->modulerunner->getRunningModuleName();
            }
        }
        return $CI->load->resolveModuleDirectory($module_name);
    }

}