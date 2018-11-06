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

if (!function_exists('utc_timestamp')) {

    /**
     * Generates a timestamp value compatible with MySQL UTC_TIMESTAMP function.
     *
     * @return string
     */
    function utc_timestamp()
    {
        return gmdate('Y-m-d H:i:s');
    }
}

if (!function_exists('date_spectrum')) {

    /**
     * Generates a date spectrum from days before to now.
     *
     * @param $today_time
     * @param int $days_before
     * @return array
     */
    function date_spectrum($today_time, $days_before = 30)
    {
        $result = array();

        $start = $today_time - ($days_before * 24 * 3600);

        for ($i = 0; $i <= $days_before; $i++) {
            $current = $start + ($i * 24 * 3600);
            $result[] = date('Y-m-d', $current);
        }

        return $result;
    }

}

if (!function_exists('fill_date_spectrum_values')) {

    /**
     * Takes values map and makes sure there are no date gaps.
     *
     * @param $values
     * @param $today_time
     * @param int $days_before
     * @param int $default_value
     * @return array
     */
    function fill_date_spectrum_values($values, $today_time, $days_before = 30, $default_value = 0)
    {
        $result = array();
        $spectrum = date_spectrum($today_time, $days_before);

        foreach ($spectrum as $key) {
            $result[$key] = isset($values[$key]) ? $values[$key] : $default_value;
        }

        return $result;
    }
}
