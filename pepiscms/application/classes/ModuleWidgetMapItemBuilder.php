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
 * ModuleWidgetMapItemBuilder.
 *
 * @since 1.0.0
 */
class ModuleWidgetMapItemBuilder
{
    private $moduleWidgetMapBuilder;
    private $label;
    private $moduleName;
    private $widgetName;
    private $widgetParameters;

    /**
     * ModuleWidgetMapItemBuilder constructor.
     */
    public function __construct(ModuleWidgetMapBuilder $moduleWidgetMapBuilder)
    {
        CI_Controller::get_instance()->load->library('Widget');
        $this->moduleWidgetMapBuilder = $moduleWidgetMapBuilder;
    }

    public function withLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function withModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    public function withWidgetName($widgetName)
    {
        $this->widgetName = $widgetName;
        return $this;
    }

    public function withWidgetParameters()
    {
        $this->widgetParameters = func_get_args();
        return $this;
    }

    /**
     * @return ModuleWidgetMapBuilder
     */
    public function end()
    {
        return $this->moduleWidgetMapBuilder;
    }

    public function build()
    {
        return array(
            'label' => $this->label,
            'module_name' => $this->moduleName,
            'widget_name' => $this->widgetName,
            'widget_parameters' => $this->widgetParameters,
            'show_in_main_pane' => true,
            'show_in_side_pane' => true,
        );
    }
}