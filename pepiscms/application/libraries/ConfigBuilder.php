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
            $contents = str_replace('{$' . $key . '}', self::protectString($value), $contents);
        }

        return file_put_contents($config_path, $contents);
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
            include($config_path);

            if (isset($config) && is_array($config))
                return $config;
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
        $contents = "<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');\n";
        $contents .= "/**\n * Automatically generated config file\n\n * @date " . date('Y-m-d') . "\n * @file " . basename($config_path) . "\n */\n\n";


        foreach ($config_variables as $key => $value) {
            if (is_bool($value)) {
                $contents .= '$config[ \'' . self::protectString($key) . '\' ] = ' . ($value ? 'TRUE' : 'FALSE') . ';' . "\n";
            } else {
                $contents .= '$config[ \'' . self::protectString($key) . '\' ] = \'' . self::protectString($value) . '\';' . "\n";
            }
        }

        return file_put_contents($config_path, $contents);
    }

    /**
     * Protects string by replacing apostrophes with \'
     *
     * @param string $value
     * @return string
     */
    private static function protectString($value)
    {
        return str_replace("'", "\\'", $value);
    }
}
