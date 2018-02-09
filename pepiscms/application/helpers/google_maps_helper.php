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

if (!function_exists('google_maps_locate')) {

    /**
     * Returns array containing coordinates of a given address
     *
     * @param string $address
     * @return array
     * @throws Exception
     */
    function google_maps_locate($address)
    {
        $url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&components=country:PL&sensor=false';
        $contents = file_get_contents($url);
        if (!$contents) {
            throw new Exception('No HTTP response');
        }

        $contents_json = json_decode($contents);
        if (!$contents_json) {
            throw new Exception('Unable to decode JSON ' . $contents);
        }
        unset($contents); // We needed it till this moment for debugging

        if (isset($contents_json->results[0]->geometry->location)) {
            $data = array(
                'lat' => $contents_json->results[0]->geometry->location->lat,
                'lng' => $contents_json->results[0]->geometry->location->lng,
            );

            return $data;
        } elseif ($contents_json->status == 'ZERO_RESULTS') {
            return FALSE;
        } else {
            throw new Exception('Unable to find location. Google Maps Status: ' . $contents_json->status);
        }
    }
}
