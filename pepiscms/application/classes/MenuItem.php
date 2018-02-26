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
 * Menu item representation. Not a menu itself.
 *
 * @since 0.2.0
 */
class MenuItem extends Menu
{
    /** @var int */
    private $id;
    /** @var int */
    private $parent_id;
    /** @var string */
    private $relative_url;
    /** @var string */
    private $canonical_url;
    /** @var string */
    private $label;
    /** @var MenuItem */
    private $parent;

    /**
     * Returns ID of the current menu item
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID of the current menu item
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns ID of the parent menu item
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Sets ID of the parent menu item
     *
     * @param int $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * Returns relative URL of the item
     *
     * @return string
     */
    public function getRelativeUrl()
    {
        return $this->relative_url;
    }

    /**
     * Sets relative URL of the element
     *
     * @param string $relative_url
     */
    public function setRelativeUrl($relative_url)
    {
        $this->relative_url = $relative_url;
    }

    /**
     * Sets canonical absolute URL of the menu element
     *
     * @param string $canonical_url
     */
    public function setCanonicalAbsoluteUrl($canonical_url)
    {
        $this->canonical_url = $canonical_url;
    }

    /**
     * Returns absolute canonical URL, if it does not exist, then it is created from relative URL
     *
     * @return string
     */
    public function getCanonicalAbsoluteUrl()
    {
        if ($this->canonical_url === null) {
            $this->setCanonicalAbsoluteUrl(base_url() . $this->getRelativeUrl());
        }
        return $this->canonical_url;
    }

    /**
     * Returns label of the menu element
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets label of the menu element
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Returns parent menu element
     *
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets parent element
     *
     * @param Menu $parent
     */
    public function setParent(Menu $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns siblings of the current element
     *
     * @return array
     */
    public function getSiblings()
    {
        return $this->getParent()->getChildren();
    }

    /**
     * @return array
     * @deprecated as PepisCMS 1.0.0
     */
    public function getSiblinks()
    {
        return $this->getParent()->getChildren();
    }
}
