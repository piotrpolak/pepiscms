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

define('PEPISCMS_VERSION', $__release_version->version);
define('PEPISCMS_PRODUCTION_RELEASE', TRUE);

unset($__release_version);