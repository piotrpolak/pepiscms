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

    public function xml_do()
    {
        if (!$this->modulerunner->isModuleInstalled('pages')) {
            show_404();
        }

        $this->load->helper('string');

        @ob_clean();

        $file_name = niceuri($this->config->item('site_name')) . '-' . date('Y-m-d_H-i-s') . '.xml';

        // Sending header
        header('Content-type: text/xml; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $file_name);

        $this->load->library('Backup');
        echo $this->backup->create();
    }

    public function xml_restore()
    {
        if (!$this->modulerunner->isModuleInstalled('pages')) {
            show_404();
        }

        $this->load->language('filemanager');

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }
        $cache_path .= 'tmp/';

        $this->load->library('FormBuilder');
        $this->formbuilder->setTitle($this->lang->line('backup_xml_restore'));
        $this->formbuilder->setSubmitLabel($this->lang->line('global_button_upload'));
        $this->formbuilder->setCallback(array($this, '_fb_callback_on_import'), FormBuilder::CALLBACK_ON_SAVE);
        $this->formbuilder->setBackLink(module_url()); // TODO Add applied filters to the back link, the same as edit
        $this->formbuilder->setDefinition(array(
            'file_to_import' => array(
                'upload_path' => $cache_path,
                'show_in_grid' => false,
                'show_in_form' => true,
                'input_type' => FormBuilder::FILE,
                'upload_allowed_types' => 'xml',
                'validation_rules' => '',
                'label' => $this->lang->line('filemanager_label_select_file'),
            ),
            'commit' => array(
                'input_type' => FormBuilder::HIDDEN,
                'input_default_value' => 1,
            )
        ));

        $this->assign('title', $this->lang->line('backup_xml_restore'));
        $this->assign('formbuilder', $this->formbuilder->generate());
        $this->display();
    }

    /**
     * Handles import file upload and parsing
     *
     * @param array $data_array
     * @return boolean
     */
    public function _fb_callback_on_import($data_array)
    {
        $this->load->library('Backup');
        $this->load->helper('string');

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }
        $cache_path .= 'tmp/';

        // checking if there was a file submited
        if (!isset($data_array['file_to_import']) || !$data_array['file_to_import']) {
            $this->formbuilder->setValidationErrorMessage($this->lang->line('backup_xml_restore_not_a_valid_xml_document'));
            return false;
        }

        $file = $cache_path . $data_array['file_to_import'];

        // just in case
        if (!file_exists($file)) {
            $this->formbuilder->setValidationErrorMessage($this->lang->line('crud_imported_file_does_not_exist'));
            return false;
        }

        // First do the backup of the current contents
        $file_name = niceuri($this->config->item('site_name')) . '-' . date('Y-m-d_H-i-s') . '-backup.xml';
        $backup_path = INSTALLATIONPATH . 'application/backup/';
        if (!file_exists($backup_path)) {
            mkdir($backup_path);
        }
        $backup_path .= $file_name;
        @file_put_contents($backup_path, $this->backup->create());

        // Restoring
        try {
            // Trying to restore
            if ($this->backup->restore($file, $this->auth->getUserId())) {
                Logger::info('Backup restore', 'BACKUP');

                // Removing file after restoring
                unlink($file);
                return true;
            } else {
                $this->formbuilder->setValidationErrorMessage($this->lang->line('backup_xml_restore_unable_to_restore'));
                return false;
            }
        } catch (Exception $exception) {

            // Preparing error message
            if ($exception->getMessage() == 'not_a_valid_backup_file') {
                $this->formbuilder->setValidationErrorMessage($this->lang->line('backup_xml_restore_not_a_valid_backup_file'));
            } else {
                $this->formbuilder->setValidationErrorMessage($this->lang->line('backup_xml_restore_not_a_valid_xml_document'));
            }
            return false;
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
