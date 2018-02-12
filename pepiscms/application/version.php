<?php
/*
 * PepisCMS
 * Version Header
 * 
 * ------------------------------
 * 
 * These constants can be used anywhere
 * in the application
 */

$__release_version = json_decode(file_get_contents(__DIR__.'/../../composer.json'));

$is_stable = strpos($__release_version->version, '-dev') === FALSE;

define('PEPISCMS_VERSION', $__release_version->version);
define('PEPISCMS_PRODUCTION_RELEASE', $is_stable);

unset($__release_version);