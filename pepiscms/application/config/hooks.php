<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_controller'][] = array(
    'class' => 'LanguageCheck',
    'function' => 'docheck',
    'filename' => 'LanguageCheck.php',
    'filepath' => 'hooks',
);
$hook['pre_controller'][] = array(
    'class' => 'TimeZoneHook',
    'function' => 'setTimezone',
    'filename' => 'TimeZoneHook.php',
    'filepath' => 'hooks',
);

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/hooks.php')) {
    include(INSTALLATIONPATH . 'application/config/hooks.php');
}