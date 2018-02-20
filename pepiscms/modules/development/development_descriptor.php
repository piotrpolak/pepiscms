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
 * Class DevelopmentDescriptor
 */
class DevelopmentDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * DevelopmentDescriptor constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        get_instance()->load->moduleLanguage($this->module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return get_instance()->lang->line($this->module_name . '_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return get_instance()->lang->line($this->module_name . '_module_description');
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
    public function getAdminDashboardElements($language)
    {
        return array(
            array(
                'controller' => $this->module_name,
                'method' => 'module_make',
                'label' => get_instance()->lang->line($this->module_name . '_make_a_new_module'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'module_make_32.png',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return array(
            array(
                'controller' => $this->module_name,
                'method' => 'module_make',
                'label' => get_instance()->lang->line($this->module_name . '_make_a_new_module'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'module_make_16.png',
            ),
            array(
                'controller' => $this->module_name,
                'method' => 'fix_missing_translation_files',
                'label' => get_instance()->lang->line($this->module_name . '_fix_missing_translations'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'fix_missing_translation_files_16.png',
            ),
            array(
                'controller' => $this->module_name,
                'method' => 'generate_header_file',
                'label' => get_instance()->lang->line($this->module_name . '_generate_header_file'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'generate_header_file_16.png',
            ),
            array(
                'controller' => $this->module_name,
                'method' => 'send_test_email',
                'label' => get_instance()->lang->line($this->module_name . '_send_test_email'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'send_test_email_16.png',
            ),
            array(
                'controller' => $this->module_name,
                'method' => 'switch_user',
                'label' => get_instance()->lang->line($this->module_name . '_switch_user'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'switch_user_16.png',
            ),
            array(
                'controller' => $this->module_name,
                'method' => 'fix_autoincrement_on_cms_tables',
                'label' => get_instance()->lang->line($this->module_name . '_fix_autoincrement_on_cms_tables'),
                'description' => '',
                'icon_url' => module_resources_url($this->module_name) . 'fix_autoincrement_on_cms_tables_16.png',
            ),
        );
    }
}
