<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * {module_name} descriptor class
 * 
 * @author {author}
 * @date {date}
 * @classTemplateVersion 20180406
 */
class {module_class_name}Descriptor extends ModuleDescriptor {

    /**
     * Cached variable
     * 
     * @var String 
     */
    private $module_name;
    
    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        $this->load->moduleLanguage( $this->module_name);
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
        if($description == $description_label) {
            return '';
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInMenu()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, false) . '/resources/install.sql';
        if (!file_exists($path)) {
            return false;
        }
        $this->db->query(file_get_contents($path));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, false) . '/resources/uninstall.sql';
        if (!file_exists($path)) {
            return false;
        }
        $this->db->query(file_get_contents($path));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return false;

//        return SubmenuBuilder::create()
//            ->addItem()
//                ->withController($this->module_name)
//                ->withMethod('edit')
//                ->withLabel($this->lang->line($this->module_name . '_add'))
//                ->withDescription($this->lang->line($this->module_name . '_add_description'))
//                ->withIconUrl(module_resources_url($this->module_name) . 'icon_16.png')
//            ->end()
//            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return false;

//        return SubmenuBuilder::create()
//            ->addItem()
//                ->withController($this->module_name)
//                ->withMethod('edit')
//                ->withLabel($this->lang->line($this->module_name . '_add'))
//                ->withDescription($this->lang->line($this->module_name . '_add_description'))
//                ->withIconUrl(module_resources_url($this->module_name) . 'icon_16.png')
//            ->end()
//            ->build();
    }

}
