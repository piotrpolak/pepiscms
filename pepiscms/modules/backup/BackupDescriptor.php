<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * Class BackupDescriptor
 */
class BackupDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * BackupDescriptor constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        $this->load->moduleLanguage($this->module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        $this->load->moduleLanguage($this->module_name);
        return $this->lang->line($this->module_name . '_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        $this->load->moduleLanguage($this->module_name);
        $description_label = $this->module_name . '_module_description';
        $description = $this->lang->line($this->module_name . '_module_description');
        if ($description == $description_label) {
            return '';
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, FALSE) . '/resources/install.sql';
        if (!file_exists($path)) {
            return FALSE;
        }
        $this->db->query(file_get_contents($path));
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, FALSE) . '/resources/uninstall.sql';
        if (!file_exists($path)) {
            return FALSE;
        }
        $this->db->query(file_get_contents($path));
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return array(
            array(
                'controller' => $this->module_name,
                'method' => 'sql_do',
                'label' => $this->lang->line($this->module_name . '_sql_do'),
                'description' => $this->lang->line($this->module_name . '_sql_do_description'),
                'icon_url' => module_resources_url($this->module_name) . 'do_backup_16.png',
            ),
            array(
                'controller' => $this->module_name,
                'method' => 'sql_do',
                'label' => $this->lang->line($this->module_name . '_sql_do_groups_and_rights'),
                'description' => $this->lang->line($this->module_name . '_sql_do_groups_and_rights_description'),
                'icon_url' => module_resources_url($this->module_name) . 'do_backup_16.png',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return FALSE;
    }

}