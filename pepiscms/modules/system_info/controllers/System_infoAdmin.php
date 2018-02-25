<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Class System_infoAdmin
 */
class System_infoAdmin extends ModuleAdminController
{

    public function __construct()
    {
        parent::__construct();
        $module_name = strtolower(str_replace('Admin', '', __CLASS__));
        $this->load->language($module_name);
        $this->assign('title', $this->lang->line('system_info_module_name'));
    }

    public function index()
    {
        $this->load->helper('number');

        if (file_exists(INSTALLATIONPATH . 'application/config/email.php')) {
            $this->load->config('email');
        }

        if (function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid(fileowner(INSTALLATIONPATH));
            $owner = $owner['name'];
        } else {
            $owner = get_current_user();
        }

        include INSTALLATIONPATH . 'application/config/database.php';

        // Getting variables from included file
        $database_config = $db[$active_group];
        unset($database_config['password']);

        $this->assign('database_config', $database_config);
        $this->assign('owner', $owner);
        $this->assign('current_user', exec('whoami'));

        $this->display();
    }

}