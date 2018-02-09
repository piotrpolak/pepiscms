<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * native or cas available
 */
$config['auth_driver'] = 'native';

/**
 * Driver specific options
 */
$config['auth_driver_options'] = array(
    'cas_host' => '',
    'cas_port' => '443',
    'cas_url' => '/cas',
    'implicit_user_group_ids' => array(1), // Implicit group 1=Operators - access to own account and modules
    'implicit_user_status' => 1, // 1 for active, 0 for inactive
    'allowed_domains' => array(), // You can restrict the possibility to login to specified domain names
    'allowed_usernames' => array(), // You can restrict the possibility to login to specified users
);

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/auth.php'))
{
    include(INSTALLATIONPATH . 'application/config/auth.php');
}
