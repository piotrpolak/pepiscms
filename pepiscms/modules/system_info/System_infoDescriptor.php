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
 * Class System_infoDescriptor
 */
class System_infoDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * System_infoDescriptor constructor.
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
    public function getAdminDashboardWidgetsMap($language)
    {
        return ModuleWidgetMapBuilder::create()
            ->addItem()
                ->withLabel($this->lang->line($this->module_name . '_disk_usage'))
                ->withLabelIcon(module_icon_small_url($this->module_name))
                ->withModuleName('system_info')
                ->withWidgetName('disk_usage')
                ->withCacheTtl(3600)
            ->end()
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigVariables()
    {
        return CrudDefinitionBuilder::create()
            ->withField('system_info_max_quota_in_mb')
                ->withValidationRules('numeric')
                ->withDescription('system_info_max_quota_in_mb_description')
            ->end()
            ->withField('system_info_watch_dir')
                ->withNoValidationRules()
                ->withDescription('system_info_watch_dir_description')
            ->end()
            ->build();
    }
}
