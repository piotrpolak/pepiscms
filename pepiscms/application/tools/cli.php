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
 * PepisCMS CLI
 *
 * @since 0.2.2
 * @version 0.1.1
 */
if (!defined('VIEWPATH')) {
    echo '<span style="font-family: Verdana, Geneva, sans-serif; font-size: 14px; line-height: 1.8em">PepisCMS 2.4 requires an upgraded index.php file. Please upgrade the file first from <code style="background-color: #000; color: #1CC722; font-size: 14px; line-height: 1.8em; padding: 3px;">pepiscms/resources/config_template/template_index.php</code>';
    die();
}
if (defined('STDIN') && php_sapi_name() == 'cli' && isset($argv[1])) {
    $_SERVER['SERVER_NAME'] = 'COMMAND_LINE';
    $_SERVER['REQUEST_URI'] = $_SERVER['QUERY_STRING'] = '/public/displaypage/page/' . $argv[1];
}
