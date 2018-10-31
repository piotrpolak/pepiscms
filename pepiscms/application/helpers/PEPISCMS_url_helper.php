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

if (!function_exists('admin_url')) {

    /**
     * Returns base URL for admin.
     *
     * @param bool $is_absolute
     * @return string
     */
    function admin_url($is_absolute = true)
    {
        if ($is_absolute) {
            return base_url() . 'admin/';
        } else {
            return 'admin/';
        }
    }
}

if (!function_exists('module_url')) {

    /**
     * Returns relative admin module base URL for the specified module. If no module is specified, the currently running module is used.
     *
     * @param string|bool $module_name if set to false, the current module is read and used
     * @return string
     */
    function module_url($module_name = false)
    {
        if (!$module_name) {
            $CI = get_instance();
            if (isset($CI->modulerunner)) {
                $module_name = $CI->modulerunner->getRunningModuleName();
            }
        }
        return admin_url() . 'module/run/' . $module_name . '/';
    }
}

if (!function_exists('module_resources_url')) {

    /**
     * Returns relative module public resources URL for the specified module. If no module is specified, the currently running module is used.
     *
     * @param string|bool $module_name if set to false, the current module is read and used
     * @return string
     */
    function module_resources_url($module_name = false)
    {
        $CI = get_instance();
        if (!$module_name) {
            if (isset($CI->modulerunner)) {
                $module_name = $CI->modulerunner->getRunningModuleName();
            }
        }

        $resouces_path = $CI->load->resolveModuleDirectory($module_name, false) . '/resources/';

        $path = false;
        if (file_exists($resouces_path)) {
            $path = $CI->load->resolveModuleDirectory($module_name, true) . 'resources/';
        }

        return $path;
    }
}

if (!function_exists('site_theme_url')) {

    /**
     * Returns relative theme path. If no theme is specified then the system configured theme is used.
     *
     * @param string|bool $theme_name if set to false, the current theme is read and used
     * @return string
     */
    function site_theme_url($theme_name = false)
    {
        if (!$theme_name) {
            $theme_name = get_instance()->config->item('current_theme');
        }
        return get_instance()->config->item('theme_path') . $theme_name . '/';
    }
}

if (!function_exists('module_icon_url')) {

    /**
     * Returns module icon URL for admin
     *
     * @param string|bool $module_name if set to false, the current module is read and used
     * @return string
     */
    function module_icon_url($module_name = false)
    {
        $CI = get_instance();
        if (!$module_name) {
            if (isset($CI->modulerunner)) {
                $module_name = $CI->modulerunner->getRunningModuleName();
            }
        }

        if (file_exists($CI->load->resolveModuleDirectory($module_name, false) . 'resources/icon_32.png')) {
            $icon_path = module_resources_url($module_name) . 'icon_32.png';
        } else {
            $icon_path = 'pepiscms/theme/img/module/module_32.png';
        }

        return $icon_path;
    }
}

if (!function_exists('module_icon_small_url')) {

    /**
     * Returns admin module icon URL. If no module is specified, the currently running module is used.
     *
     * @param string|bool $module_name if set to false, the current module is read and used
     * @return string
     */
    function module_icon_small_url($module_name = false)
    {
        $CI = get_instance();
        if (!$module_name) {
            if (isset($CI->modulerunner)) {
                $module_name = $CI->modulerunner->getRunningModuleName();
            }
        }

        if (file_exists($CI->load->resolveModuleDirectory($module_name, false) . '/resources/icon_16.png')) {
            $icon_path = module_resources_url($module_name) . 'icon_16.png';
        } else {
            $icon_path = 'pepiscms/theme/module_16.png';
        }

        return $icon_path;
    }
}

if (!function_exists('current_url')) {

    /**
     * Returns absolute URL for the current request.
     *
     * Returns the full URL (including segments) of the page where this
     * function is placed
     *
     * @return string
     */
    function current_url()
    {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $pageURL .= 's';
        }
        $pageURL .= '://';
        if ($_SERVER['SERVER_PORT'] != '80') {
            $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
}
