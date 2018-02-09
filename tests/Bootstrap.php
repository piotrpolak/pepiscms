<?php

// Errors on full!
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

$dir = realpath(dirname(__FILE__));

// Path constants
defined('PROJECT_BASE') OR define('PROJECT_BASE', realpath($dir . '/../') . '/');
defined('VENDOR_PATH') OR define('VENDOR_PATH', realpath($dir . '/../../..//') . '/');
defined('SYSTEM_PATH') OR define('SYSTEM_PATH', PROJECT_BASE . 'codeigniter/');
defined('APPPATH') OR define('APPPATH', PROJECT_BASE . 'pepiscms/application/');
defined('BASEPATH') OR define('BASEPATH', PROJECT_BASE . 'pepiscms/');

require_once(VENDOR_PATH . 'autoload.php');
