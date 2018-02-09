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
 * Bootstrap hook overwrites some features of common
 * @param string $level
 * @param string $message
 * @param bool $php_error
 */
function log_message($level = 'error', $message, $php_error = FALSE)
{
    if (!class_exists('Logger')) {
        return;
    }

    $level = strtoupper($level);
    $_levels = array('ERROR' => '1', 'DEBUG' => '2', 'INFO' => '3', 'ALL' => '4');

    if (config_item('log_threshold') == 0 || $_levels[$level] > config_item('log_threshold')) {
        return;
    }

    $collection = $php_error ? 'PHP' : 'SYSTEM';

    switch ($level) {
        case 'ERROR':
            Logger::error($message, $collection);
            break;
        case 'DEBUG':
            Logger::notice($message, $collection);
            break;
        default:
            Logger::info($message, $collection);
            break;
    }
}
