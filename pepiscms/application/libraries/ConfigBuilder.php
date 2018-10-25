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
 * Utility class for building config files
 * It is used to serialize associative arrays in a nice way by writing PHP code
 *
 * @since 0.1.4
 */
class ConfigBuilder
{
    /**
     * Parses template file, replaces template variables and writes config
     *
     * @param string $template_path
     * @param string $config_path
     * @param array $config_variables
     * @return bool
     */
    public function build($template_path, $config_path, $config_variables)
    {
        $contents = file_get_contents($template_path);
        if (!$contents) {
            return false;
        }

        foreach ($config_variables as $key => $value) {
            $contents = str_replace('{$' . $key . '}', $this->protectString($value), $contents);
        }

        $success = file_put_contents($config_path, $contents);
        \PiotrPolak\PepisCMS\Modulerunner\OpCacheUtil::safeInvalidate($config_path);
        return $success;
    }

    /**
     * Reads config file of specified path and returns associative array
     *
     * @param string $config_path
     * @return array|bool
     */
    public function readConfig($config_path)
    {
        if (file_exists($config_path)) {
            /** @noinspection PhpIncludeInspection */
            include($config_path);

            if (isset($config) && is_array($config)) {
                return $config;
            }
        }

        return false;
    }

    /**
     * Writes config file
     *
     * @param string $config_path
     * @param array $config_variables
     * @return bool
     */
    public function writeConfig($config_path, $config_variables)
    {
        $contents = "<?php if (!defined('BASEPATH')) exit('No direct script access allowed');\n" .
            "/**\n * Automatically generated config file\n\n * @date " . date('Y-m-d') .
            "\n * @file " . basename($config_path) . "\n */\n\n";

        foreach ($config_variables as $key => $value) {
            $contents .= '$config[ \'' . $this->protectString($key) . '\' ] = \'' . $this->getValue($value) . '\';' . "\n";
        }

        $success = file_put_contents($config_path, $contents);
        \PiotrPolak\PepisCMS\Modulerunner\OpCacheUtil::safeInvalidate($config_path);
        return $success;
    }

    /**
     * Protects string by replacing apostrophes with \'
     *
     * @param string $value
     * @return string
     */
    private function protectString($value)
    {
        return str_replace("'", "\\'", $value);
    }

    /**
     * @param $value
     * @return string
     */
    private function getValue($value)
    {
        if (is_bool($value)) {
            return ($value ? 'TRUE' : 'FALSE');
        } else {
            return $this->protectString($value);
        }
    }
}
