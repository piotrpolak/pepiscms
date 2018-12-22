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
 * Welcome default redirect controller
 */
class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->library(array('Auth', 'SecurityManager'));

        if (!$this->auth->isAuthorized()) {
            redirect(admin_url() . 'login');
        } else {
            if ($this->config->item('cms_customization_on_login_redirect_url')) {
                redirect(base_url() . $this->config->item('cms_customization_on_login_redirect_url'));
            } else {
                redirect(admin_url() . 'about/dashboard');
            }
        }
    }
}
