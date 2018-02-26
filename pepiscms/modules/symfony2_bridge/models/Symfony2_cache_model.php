<?php

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Symfony2_cache_model
 */
class Symfony2_cache_model extends CI_Model
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->load->moduleLibrary('symfony2_bridge', 'Symfony2_bridge');
    }

    /**
     * Returns cache directory
     *
     * @param string $env prod or dev
     * @return bool|string
     */
    public function getCacheDir($env = 'prod')
    {
        $allowed_env = array('prod', 'dev');
        if (!in_array($env, $allowed_env)) {
            return false;
        }

        $cache_dir = $this->symfony2_bridge->getKernel()->getCacheDir();
        $base_name = basename($cache_dir);
        if (!in_array($base_name, $allowed_env)) { // Security
            return false;
        }

        $dir_name = dirname($cache_dir);
        if (!$dir_name || $dir_name == '/' || $dir_name == '.') { // Security
            return false;
        }

        return $dir_name . '/' . $env . '/';
    }

    /**
     * Clears cache
     *
     * @param string $env
     * @return bool
     */
    public function removeCache($env = 'prod')
    {
        $cache_dir = $this->getCacheDir($env);
        if (!$cache_dir) {
            return false;
        }

        if (file_exists($cache_dir) && is_dir($cache_dir)) {
            $cmd = 'rm -rf ' . escapeshellarg($cache_dir);
            system($cmd);
            return true;
        }

        return false;
    }
}
