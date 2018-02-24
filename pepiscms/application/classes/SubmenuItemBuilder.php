<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
        'controller' => FALSE,
        'method' => '',
        'description' => '',
        'label' => FALSE,
        'item_url' => FALSE,
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