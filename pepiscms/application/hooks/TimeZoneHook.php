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
 * TimeZoneHook
 */
class TimeZoneHook
{
    public function setTimezone($params = FALSE)
    {
        $timezone = FALSE;
        $file = INSTALLATIONPATH . 'application/config/_pepiscms.php';

        if (file_exists($file)) {
            require_once($file);
            if (isset($config['timezone'])) {
                $timezone = $config['timezone'];
            }
        }

        if ($timezone) {
            date_default_timezone_set($timezone);
        }
    }

}
