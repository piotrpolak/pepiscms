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
 * ModuleWidgetMapBuilder.
 *
 * @since 1.0.0
 */
class ModuleWidgetMapBuilder
{
    /**
     * @var ModuleWidgetMapItemBuilder[]
     */
    private $itemBuilders;

    /**
     * Private constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return ModuleWidgetMapBuilder
     */
    public static function create()
    {
        return new ModuleWidgetMapBuilder();
    }

    /**
     * @return ModuleWidgetMapItemBuilder
     */
    public function addItem()
    {
        $itemBuilder = new ModuleWidgetMapItemBuilder($this);
        $this->itemBuilders[] = $itemBuilder;
        return $itemBuilder;
    }

    /**
     * @return array
     */
    public function build()
    {
        $result = array();
        foreach ($this->itemBuilders as $itemBuilder) {
            $result[] = $itemBuilder->build();
        }

        return $result;
    }
}