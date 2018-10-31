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
    const CONTROLLER_KEY = 'controller';
    const METHOD_KEY = 'method';
    const DESCRIPTION_KEY = 'description';
    const LABEL_KEY = 'label';
    const ITEM_URL_KEY = 'item_url';
    const URL_KEY = 'url';
    const TARGET_KEY = 'target';
    const GROUP_KEY = 'group';
    const IS_POPUP_KEY = 'is_popup';
    const ICON_URL_KEY = 'icon_url';

    /**
     * @var SubmenuBuilder
     */
    private $submenuItemsBuilder;

    private $data = array(
        self::CONTROLLER_KEY => false,
        self::METHOD_KEY => '',
        self::DESCRIPTION_KEY => '',
        self::LABEL_KEY => false,
        self::ITEM_URL_KEY => false,
        self::URL_KEY => false,
        self::TARGET_KEY => false,
        self::GROUP_KEY => false,
        self::IS_POPUP_KEY => false,
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
        $this->data[self::URL_KEY] = $url;
        return $this;
    }

    /**
     * @param $target
     * @return $this
     */
    public function withTarget($target)
    {
        $this->data[self::TARGET_KEY] = $target;
        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function withGroup($group)
    {
        $this->data[self::GROUP_KEY] = $group;
        return $this;
    }

    /**
     * @param $controller
     * @return $this
     */
    public function withController($controller)
    {
        $this->data[self::CONTROLLER_KEY] = $controller;
        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    public function withMethod($method)
    {
        $this->data[self::METHOD_KEY] = $method;
        return $this;
    }

    /**
     * @param $label
     * @return $this
     */
    public function withLabel($label)
    {
        $this->data[self::LABEL_KEY] = $label;
        return $this;
    }

    /**
     * @param $description
     * @return $this
     */
    public function withDescription($description)
    {
        $this->data[self::DESCRIPTION_KEY] = $description;
        return $this;
    }

    /**
     * @param $icon_url
     * @return $this
     */
    public function withIconUrl($icon_url)
    {
        $this->data[self::ICON_URL_KEY] = $icon_url;
        return $this;
    }

    /**
     * @param $is_popup
     * @return $this
     */
    public function withPopup($is_popup)
    {
        $this->data[self::IS_POPUP_KEY] = $is_popup;
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
