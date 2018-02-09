<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Modifying this file will lead to warranty void.
 * Changing enabled features is against the licence agreement!
 */
$config['feature_is_enabled_backup'] = TRUE;
$config['feature_is_enabled_filemanager'] = TRUE;
$config['feature_is_enabled_setup'] = TRUE;
$config['feature_is_enabled_remote_applications'] = TRUE;
$config['feature_is_enabled_acl'] = TRUE;
$config['feature_is_enabled_menu'] = TRUE;

if (file_exists(INSTALLATIONPATH . 'application/config/features.php')) {
    require_once INSTALLATIONPATH . 'application/config/features.php';
}