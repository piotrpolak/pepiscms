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
        $this->load->moduleLanguage($this->module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return $this->lang->line($this->module_name . '_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return $this->lang->line($this->module_name . '_module_description');
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return SubmenuBuilder::create()
            ->addItem()
            ->withController($this->module_name)
            ->withMethod('module_make')
            ->withLabel($this->lang->line($this->module_name . '_make_a_new_module'))
            ->withIconUrl(module_resources_url($this->module_name) . 'module_make_32.png')
        ->end()
        ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return SubmenuBuilder::create()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('module_make')
                ->withLabel($this->lang->line($this->module_name . '_make_a_new_module'))
                ->withIconUrl(module_resources_url($this->module_name) . 'module_make_16.png')
            ->end()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('fix_missing_translation_files')
                ->withLabel($this->lang->line($this->module_name . '_fix_missing_translations'))
                ->withIconUrl(module_resources_url($this->module_name) . 'fix_missing_translation_files_16.png')
            ->end()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('generate_header_file')
                ->withLabel($this->lang->line($this->module_name . '_generate_header_file'))
                ->withIconUrl(module_resources_url($this->module_name) . 'generate_header_file_16.png')
            ->end()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('send_test_email')
                ->withLabel($this->lang->line($this->module_name . '_send_test_email'))
                ->withIconUrl(module_resources_url($this->module_name) . 'send_test_email_16.png')
            ->end()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('switch_user')
                ->withLabel($this->lang->line($this->module_name . '_switch_user'))
                ->withIconUrl(module_resources_url($this->module_name) . 'switch_user_16.png')
            ->end()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('fix_autoincrement_on_cms_tables')
                ->withLabel($this->lang->line($this->module_name . '_fix_autoincrement_on_cms_tables'))
                ->withIconUrl(module_resources_url($this->module_name) . 'fix_autoincrement_on_cms_tables_16.png')
            ->end()
            ->build();
    }
}
