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
 * CMS Utilities controller
 */
class Utilities extends AbstractDashboardController
{
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('cms_enable_utilities') === false) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }
        $this->load->language('acl');
        $this->load->language('setup');

        $this->load->model('Module_model');;
        $this->load->library('SimpleSessionMessage');
        $this->load->helper('number');
        $this->load->library('Cachedobjectmanager');

        $this->assign('title', $this->lang->line('label_utilities_and_settings'));
    }

    public function index()
    {
        // TODO Grouping not implemented by view
        $dashboard_elements = $this->getDashboardElements();
        $dashboard_elements_grouped = $this->getDashboardElementsGrouped($dashboard_elements, 'common_utilities');

        $this->assign('dashboard_elements_grouped', $dashboard_elements_grouped)->display();
    }

    /**
     * @inheritdoc
     */
    protected function getElements(ModuleDescriptableInterface $descriptior)
    {
        return $descriptior->getAdminUtilitiesElements($this->lang->getCurrentLanguage());
    }

    public function flush_security_policy_cache()
    {
        $this->auth->refreshSession();
        $stats = SecurityManager::flushCache();

        if ($stats) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->setMessage('utilities_cache_successfully_cleaned', $stats['count'], byte_format($stats['size']));
        }

        redirect(admin_url() . 'utilities');
    }

    public function flush_system_cache()
    {
        $this->auth->refreshSession();
        $this->db->cache_delete_all();
        \PiotrPolak\PepisCMS\Modulerunner\OpCacheUtil::safeReset();
        $stats = $this->cachedobjectmanager->cleanup();
        if ($stats) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->simplesessionmessage->setMessage('utilities_cache_successfully_cleaned', $stats['count'],
                    byte_format($stats['size']));
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->simplesessionmessage->setMessage('utilities_opcache_and_db_cache_successfully_cleaned');
        }

        redirect(admin_url() . 'utilities');
    }
}
