<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once BASEPATH . '../application/config/autoload.php';

if (file_exists(INSTALLATIONPATH . 'application/config/database.php')) {
    $autoload['libraries'][] = 'database';
}

$autoload['helper'] = array('url', 'path', 'dialog_message');
$autoload['config'] = array('_pepiscms', 'features', 'database_tables');
$autoload['model'] = array('Generic_model');