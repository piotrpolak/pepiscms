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
 * Upgradedb utility controller
 *
 * @since 0.2.3
 */
class Upgradedb extends EnhancedController
{
    public function index()
    {
        $this->load->moduleModel('sqlconsole', 'Sqlconsole_helper_model');
        $sql_basepath = APPPATH . '../resources/sql/upgrade/';
        $lock_filepath = INSTALLATIONPATH . 'application/cache/upgradedb.lock';
        $count = 0;

        if (file_exists($lock_filepath)) {
            // TODO Translation
            show_error('Application already upgraded at ' . date('Y-m-d H:i:s', filemtime($lock_filepath)) . ' Delete cache/upgradedb.lock to enable upgrade utility. <a href="' . admin_url() . '">Go back to admin panel</a>');
        }

        touch($lock_filepath);

        // Update 0.2.2 to 0.2.3
        if (!$this->db->table_exists('cms_users') && $this->db->table_exists('users')) {
            $sql_input = file_get_contents($sql_basepath . '0.2.3.0-stage1.sql');
            $this->Sqlconsole_helper_model->runMultipleSqlQueries($sql_input);
            $count++;
        }
        if (!$this->db->table_exists('cms_pages') && !$this->db->table_exists('cms_menu') && $this->db->table_exists('pages') && $this->db->table_exists('menu')) {
            $sql_input = file_get_contents($sql_basepath . '0.2.3.0-stage2.sql');
            $this->Sqlconsole_helper_model->runMultipleSqlQueries($sql_input);
            $count++;
        }
        if (!$this->db->table_exists('cms_password_history')) {
            $sql_input = file_get_contents($sql_basepath . '1.0.0-stage1.sql');
            $this->Sqlconsole_helper_model->runMultipleSqlQueries($sql_input);
            $count++;

            if ($this->db->table_exists('cms_menu')) {
                $sql_input = file_get_contents($sql_basepath . '1.0.0-stage2.sql');
                $this->Sqlconsole_helper_model->runMultipleSqlQueries($sql_input);
                $count++;
            }
        }

        if (!$count) {
            $message = 'System database up to date.';
        } else {
            $message = 'Executed ' . $count . ' upgrade scripts.';
        }

        $this->load->library('Auth');
        $this->auth->refreshSession();
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();

        show_error($message . ' <a href="' . admin_url() . '">Go back to admin panel</a>', 400, 'Status');
    }
}
