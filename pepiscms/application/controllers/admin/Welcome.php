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
 * Welcome default redirect controller
 */
class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->library('Auth');
        $this->load->library('SecurityManager');

        if (!$this->auth->isAuthorized()) {
            redirect(admin_url() . 'login');
        } else {
            if ($this->config->item('cms_customization_on_login_redirect_url')) {
                redirect(base_url() . $this->config->item('cms_customization_on_login_redirect_url'));
            } elseif (SecurityManager::hasAccess('pages') && $this->config->item('cms_enable_pages')) {
                redirect(admin_url() . 'pages');
            } else {
                redirect(admin_url() . 'about/dashboard');
            }
        }
    }
}
