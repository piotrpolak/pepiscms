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
 * Pattern compiler utility.
 *
 * @since 1.0.0
 */
class PatternCompiler
{
    /**
     * Compiles pattern, if keys to be replaced are specified, then the script will not parse pattern (faster)
     *
     * @param $pattern
     * @param $object_with_data
     * @param array $keys_to_be_replaced
     * @return string
     */
    public static function compile($pattern, $object_with_data, $keys_to_be_replaced = array())
    {
        if (count($keys_to_be_replaced) == 0) {
            preg_match_all('/{([a-z_0-9]+)}/', $pattern, $matches);
            $keys_to_be_replaced = $matches[1];
        }

        $is_object = is_object($object_with_data);

        foreach ($keys_to_be_replaced as $key) {
            if ($is_object) {
                if (!isset($object_with_data->$key)) {
                    continue;
                }
                $pattern = trim(str_replace('{' . $key . '}', $object_with_data->$key, $pattern));
            } else { // Array
                if (!isset($object_with_data[$key])) {
                    continue;
                }
                $pattern = trim(str_replace('{' . $key . '}', $object_with_data[$key], $pattern));
            }
        }

        return $pattern;
    }
}
