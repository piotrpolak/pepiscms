<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['feature_is_enabled_backup'] = TRUE;
$config['feature_is_enabled_filemanager'] = TRUE;
$config['feature_is_enabled_setup'] = TRUE;
$config['feature_is_enabled_acl'] = TRUE;
$config['feature_is_enabled_menu'] = TRUE;

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/features.php')) {
    require_once INSTALLATIONPATH . 'application/config/features.php';
}