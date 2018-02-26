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
 * PepisCMS
 * Version Header
 *
 * ------------------------------
 *
 * These constants can be used anywhere
 * in the application
 */

$__release_version = json_decode(file_get_contents(__DIR__.'/../../composer.json'));

$is_stable = strpos($__release_version->version, '-dev') === false;

define('PEPISCMS_VERSION', $__release_version->version);
define('PEPISCMS_PRODUCTION_RELEASE', $is_stable);

unset($__release_version);
