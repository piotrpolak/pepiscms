<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

/**
 * Logout controller
 */
class Logout extends AdminController
{
    public function index()
    {
        Logger::info('Logout', 'LOGIN');
        $this->auth->logout(TRUE);
        redirect(admin_url() . 'login/logout');
    }
}
