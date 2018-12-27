<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class BackupAdmin
 */
class BackupAdmin extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        // Getting module name from class name
        $module_name = strtolower(str_replace('Admin', '', __CLASS__));

        // Loading module language, model, libraries, helpers
        $this->load->language($module_name);
        $this->load->moduleLanguage('crud');
        $this->load->library('Widget');

        // Setting useful labels, please note the convention
        $this->assign('title', $this->lang->line($module_name . '_module_name'));
    }

    public function index()
    {
        $this->display();
    }

    public function sql_do()
    {
        $this->load->helper('string');
        $this->load->helper('mysqldump');
        $this->load->helper('os');

        if (is_windows()) {
            show_error($this->lang->line('backup_dump_disabled_on_windows'));
        }

        $db_settings = $this->getDatabaseSettings();

        $dump = mysqldump($db_settings['hostname'], $db_settings['database'], $db_settings['username'], $db_settings['password']);
        if ($dump) {
            Logger::info('Doing full database dump', 'BACKUP');
            $file_name = niceuri($this->config->item('site_name')) . '-' . date('Y-m-d_H-i-s') . '.sql';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header('Content-Disposition: attachment; filename=' . $file_name);
            die($dump);
        } else {
            show_error($this->lang->line('backup_dump_unable_to_make'));
        }
    }

    public function sql_do_groups_and_rights()
    {
        $this->load->helper('string');
        $this->load->helper('mysqldump');
        $this->load->helper('os');

        if (is_windows()) {
            show_error($this->lang->line('backup_dump_disabled_on_windows'));
        }

        $db_settings = $this->getDatabaseSettings();

        $dump = mysqldump($db_settings['hostname'], $db_settings['database'], $db_settings['username'], $db_settings['password'], array($this->config->item('database_table_groups'), $this->config->item('database_table_group_to_entity')), false, false);
        if ($dump) {
            Logger::info('Doing partial database dump', 'BACKUP');
            $file_name = niceuri('groups-' . $this->config->item('site_name')) . '-' . date('Y-m-d_H-i-s') . '.sql';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header('Content-Disposition: attachment; filename=' . $file_name);
            die($dump);
        } else {
            show_error($this->lang->line('backup_dump_unable_to_make'));
        }
    }

    /**
     * @return array
     */
    private function getDatabaseSettings()
    {
        require(INSTALLATIONPATH . '/application/config/database.php');
        if (!isset($db)) {
            show_error($this->lang->line('backup_database_settings_not_found'));
        }

        if (!($db[$active_group]['dbdriver'] == 'mysql' || $db[$active_group]['dbdriver'] == 'mysqli')) {
            show_error($this->lang->line('backup_dump_works_only_with_mysql'));
        }

        return $db[$active_group];
    }
}
