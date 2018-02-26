<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['licence'] = false;

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/licence.php')) {
    require_once INSTALLATIONPATH . 'application/config/licence.php';
}
