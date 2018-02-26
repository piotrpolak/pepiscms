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

if (!function_exists('reflect2xml')) {

    /**
     * Serializes object properties into XML, used by backu only
     *
     * @param object $object
     * @param array $properties_array
     * @param string $pre_string
     * @return string
     */
    function reflect2xml($object, &$properties_array, $pre_string = '')
    {
        $output = '';
        foreach ($properties_array as $property) {
            $output .= $pre_string . '<' . $property . '>';
            eval('$output .= htmlspecialchars($object->' . $property . ');');
            $output .= '</' . $property . '>' . "\r\n";
        }

        return $output;
    }
}
