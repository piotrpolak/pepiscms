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

        $response = @file_get_contents('http://www.netip.de/search?query=' . $ip);
        if (empty($response)) {
            throw new InvalidArgumentException('Error contacting Geo-IP-Server');
        }

        $patterns = array();
        $patterns['domain'] = '#Domain: (.*?)&nbsp;#i';
        $patterns['country'] = '#Country: (.*?)&nbsp;#i';
        $patterns['state'] = '#State/Region: (.*?)<br#i';
        $patterns['town'] = '#City: (.*?)<br#i';

        $ipInfo = array();

        foreach ($patterns as $key => $pattern) {
            //store the result in array
            $ipInfo[$key] = preg_match($pattern, $response, $value) && !empty($value[1]) ? $value[1] : FALSE;
            $ipInfo[$key] = trim($ipInfo[$key]);
        }

        if ($ipInfo['country'] == '-') {
            $ipInfo['country'] = FALSE;
        }

        return $ipInfo;
    }

}