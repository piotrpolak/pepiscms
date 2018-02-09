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
 * CMS Utilities controller
 */
class Utilities extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('cms_enable_utilities') === FALSE) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }
        $this->load->language('acl');
        $this->load->language('setup');

        $this->load->model('Module_model'); // Avoiging conflicts
        $this->load->library('SimpleSessionMessage');

        $this->assign('title', $this->lang->line('label_utilities_and_settings'));
    }

    public function index()
    {
        $this->display();
    }

    public function flush_html_cache()
    {
        $this->auth->refreshSession();
        try {
            $this->load->model('Page_model');
            $stats = $this->Page_model->clean_pages_cache();

            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            $this->load->helper('number');
            $this->simplesessionmessage->setMessage('utilities_cache_successfully_cleaned', $stats['count'], byte_format($stats['size']));

            redirect(admin_url() . 'utilities');
        } catch (Exception $e) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR);
            $this->simplesessionmessage->setMessage('utilities_label_cache_unable_to_open_directory_might_be_empty');
            redirect(admin_url() . 'utilities');
        }
    }

    public function flush_security_policy_cache()
    {
        $this->auth->refreshSession();
        $stats = SecurityManager::flushCache();

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->load->helper('number');
        $this->simplesessionmessage->setMessage('utilities_cache_successfully_cleaned', $stats['count'], byte_format($stats['size']));

        redirect(admin_url() . 'utilities');
    }

    public function flush_system_cache()
    {
        $this->auth->refreshSession();
        $this->load->library('Cachedobjectmanager');

        $stats = $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->load->helper('number');
        $this->simplesessionmessage->setMessage('utilities_cache_successfully_cleaned', $stats['count'], byte_format($stats['size']));
        redirect(admin_url() . 'utilities');
    }
}
