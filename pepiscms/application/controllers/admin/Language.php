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
 * Controller for changing the application language
 */
class Language extends CI_Controller
{
    public function set()
    {
        if ($this->uri->segment(4)) {
            $this->lang->setAdminLanguage($this->uri->segment(4));
        }

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(admin_url());
        }
    }
}
