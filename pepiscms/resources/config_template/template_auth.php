<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* See core/pepiscms/application/config/auth.php for values that can be overwritten */

/**
 * native or cas available
 */
$config['auth_driver'] = 'TEMPLATE_AUTHENTIFICATION_DRIVER';

/**
 * Driver specific options
 */
$config['auth_driver_options'] = array(
        'cas_host'                                      => 'TEMPLATE_CAS_SERVER',
        'cas_port'                                      => 'TEMPLATE_CAS_PORT',
        'cas_url'                                       => 'TEMPLATE_CAS_PATH',
        'implicit_user_group_ids'                       => array(1), // Implicit group 1=Operators - access to own account and modules
        'implicit_user_status'                          => 1, // 1 for active, 0 for inactive
        'allowed_domains'                               => array(), // You can restrict the possibility to login to specified domain names
        'allowed_usernames'                             => array(), // You can restrict the possibility to login to specified users
);