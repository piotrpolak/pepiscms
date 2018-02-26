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
class SubmenuItemBuilder
{
    /**
     * @var SubmenuBuilder
     */
    private $submenuItemsBuilder;

    private $data = array(
        'controller' => false,
        'method' => '',
        'description' => '',
        'label' => false,
        'item_url' => false,
        'url' => false,
        'target' => false,
        'group' => false,
        'is_popup' => false,
    );

    /**
     * SubmenuItemBuilder constructor.
     * @param SubmenuBuilder $submenuItemsBuilder
     */
    public function __construct(SubmenuBuilder $submenuItemsBuilder)
    {
        $this->submenuItemsBuilder = $submenuItemsBuilder;
    }

    /**
     * @param $url
     * @return $this
     */
    public function withUrl($url)
    {
        $this->data['url'] = $url;
        return $this;
    }

    /**
     * @param $target
     * @return $this
     */
    public function withTarget($target)
    {
        $this->data['target'] = $target;
        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function withGroup($group)
    {
        $this->data['group'] = $group;
        return $this;
    }

    /**
     * @param $controller
     * @return $this
     */
    public function withController($controller)
    {
        $this->data['controller'] = $controller;
        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    public function withMethod($method)
    {
        $this->data['method'] = $method;
        return $this;
    }

    /**
     * @param $label
     * @return $this
     */
    public function withLabel($label)
    {
        $this->data['label'] = $label;
        return $this;
    }

    /**
     * @param $description
     * @return $this
     */
    public function withDescription($description)
    {
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * @param $icon_url
     * @return $this
     */
    public function withIconUrl($icon_url)
    {
        $this->data['icon_url'] = $icon_url;
        return $this;
    }

    /**
     * @param $is_popup
     * @return $this
     */
    public function withPopup($is_popup)
    {
        $this->data['is_popup'] = $is_popup;
        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        return $this->data;
    }

    /**
     * @return SubmenuBuilder
     */
    public function end()
    {
        return $this->submenuItemsBuilder;
    }
}
