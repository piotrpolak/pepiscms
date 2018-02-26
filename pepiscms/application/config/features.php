<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['feature_is_enabled_backup'] = true;
$config['feature_is_enabled_filemanager'] = true;
$config['feature_is_enabled_setup'] = true;
$config['feature_is_enabled_acl'] = true;
$config['feature_is_enabled_menu'] = true;

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/features.php')) {
    require_once INSTALLATIONPATH . 'application/config/features.php';
}
