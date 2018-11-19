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
 * Class Cms_usersDescriptor
 */
class Cms_usersDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * Cms_usersDescriptor constructor.
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        $this->load->moduleLanguage($this->module_name);
        return SubmenuBuilder::create()
            ->addItem()
                ->withController($this->module_name)
                ->withMethod('edit')
                ->withLabel($this->lang->line($this->module_name . '_add'))
                ->withDescription($this->lang->line($this->module_name . '_add_description'))
                ->withIconUrl(module_resources_url($this->module_name) . 'icon_add_32.png')
            ->end()
            ->build();
    }
}
