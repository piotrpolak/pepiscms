<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['licence'] = FALSE;

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/licence.php')) {
    require_once INSTALLATIONPATH . 'application/config/licence.php';
}