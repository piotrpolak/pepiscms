<?php // No die for a reason

/*
|--------------------------------------------------------------------------
| Twig loader basepath
|--------------------------------------------------------------------------
|
*/
$config['twig_loader_basepath'] = VENDOR_PATH . 'fabpot/Twig/lib/Twig/';

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/twig.php')) {
    require_once INSTALLATIONPATH . 'application/config/twig.php';
}
