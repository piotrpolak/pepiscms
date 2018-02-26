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
 * SubmenuItemBuilder.
 *
 * @since 1.0.0
 */
class SubmenuBuilder
{
    /**
     * @var SubmenuItemBuilder[]
     */
    private $itemBuilders = array();

    /**
     * Private constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return SubmenuBuilder
     */
    public static function create()
    {
        return new SubmenuBuilder();
    }

    /**
     * @return SubmenuItemBuilder
     */
    public function addItem()
    {
        $itemBuilder = new SubmenuItemBuilder($this);
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
