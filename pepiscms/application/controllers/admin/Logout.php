<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Logout controller
 */
class Logout extends AdminController
{
    public function index()
    {
        Logger::info('Logout', 'LOGIN');
        $this->auth->logout(true);
        redirect(admin_url() . 'login/logout');
    }
}
