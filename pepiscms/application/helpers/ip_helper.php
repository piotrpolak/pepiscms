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

if (!function_exists('ip_info')) {

    /**
     * Returns IP info based on http://www.netip.de/
     *
     * @param string $ip
     * @return array
     * @throws InvalidArgumentException
     */
    function ip_info($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('IP is not valid');
        }

        $response = @file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip);
        if (empty($response)) {
            throw new InvalidArgumentException('Error contacting Geo-IP-Server');
        }

        $data = @json_decode($response);

        $ipInfo = array(
            'domain' => '',
            'country' => '',
            'state' => '',
            'town' => '',
        );

        $host = gethostbyaddr($ip);
        if($host) {
            $ipInfo['domain'] = $host;
        }

        if(isset($data->geoplugin_countryName)) {
            $ipInfo['country'] = $data->geoplugin_countryName;
        }
        if(isset($data->geoplugin_regionName)) {
            $ipInfo['state'] = $data->geoplugin_regionName;
        }
        if(isset($data->geoplugin_regionName)) {
            $ipInfo['town'] = $data->geoplugin_city;
        }

        return $ipInfo;
    }
}
