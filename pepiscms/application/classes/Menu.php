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
 * Menu tree representation used by theme template (frontend).
 * Contains subelements called MenuItems.
 *
 * @since 0.2
 */
class Menu
{
    /** @var array|null */
    private $children = null;

    /**
     * Returns children elements of the current menu element
     *
     * @return array
     */
    public function getChildren()
    {
        // Initializing children
        if ($this->children == null) {
            $this->initializeChildren();
            $menu_items_array = get_instance()->Menu_model->getSubMenu($this->getId(), Dispatcher::getSiteLanguage()->code); // Hardcoded, to be fixed
            foreach ($menu_items_array as &$item) {
                $menu_item = new MenuItem();
                $menu_item->setLabel($item['item_name']);
                $menu_item->setId($item['item_id']);
                $menu_item->setParentId($item['parent_item_id']);
                $menu_item->setRelativeUrl(Menu::getItemUrl($item, Dispatcher::getUriPrefix())); // Hardcoded, to be fixed
                $menu_item->setParent($this);
                $this->addChild($menu_item);
            }
        }

        return $this->children;
    }

    /**
     * Return menu item by ID
     *
     * @param MenuItem $url
     * @return MenuItem
     */
    public function getMenuItemByCanonicalAbsoluteUrl($url)
    {
        return self::searchForChildByCanonicalAbsoluteUrl($this->getChildren(), $url);
    }

    /**
     * Static recursive function that parses all element structure
     *
     * @param Menu $children
     * @param string $url
     * @return bool|MenuItem
     */
    private static function searchForChildByCanonicalAbsoluteUrl($children, $url)
    {
        if (!is_array($children) || !(count($children) > 0)) {
            return false;
        }

        foreach ($children as $sub_child) {

            /** @var MenuItem $sub_child */
            if ($sub_child->getCanonicalAbsoluteUrl() == $url) {
                return $sub_child;
            }


            $sub_child = self::searchForChildByCanonicalAbsoluteUrl($sub_child->getChildren(), $url);
            if ($sub_child) {
                return $sub_child;
            }
        }

        return false;
    }

    /**
     * Compatibility, will always return 0
     *
     * @return int
     */
    protected function getId()
    {
        return 0;
    }

    /**
     * Compatibility, will always return FALSE
     *
     * @return bool
     */
    public function getParent()
    {
        return false;
    }

    /**
     * Initializes children, to be ready to use
     * Returns FALSE if children already initialized
     *
     * @return bool
     */
    private function initializeChildren()
    {
        if ($this->children == null) {
            $this->children = array();
            return true;
        }
        return false;
    }

    /**
     * Sets the children of the current menu element
     *
     * @param array $children
     */
    public function setChildren($children)
    {
        $this->initializeChildren();
        foreach ($children as $child) {
            /** @var MenuItem $child */
            $child->setParent($this);
            $child->setParentId($this->getId());
        }
        $this->children = $children;
    }

    /**
     * Adds a child to the current menu element
     *
     * @param MenuItem $child
     */
    public function addChild(MenuItem $child)
    {
        $this->initializeChildren();
        $child->setParent($this);
        $child->setParentId($this->getId());
        $this->children[] = $child;
    }

    /**
     * Tells whether the current menu element has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->getChildren()) > 0;
    }

    /**
     * Reads into the buffer the whole menu
     *
     * @return bool
     */
    public function preftechChildrenTree()
    {
        return self::preftechChildren($this->getChildren());
    }

    /**
     * Static recursive function that parses all element structure
     *
     * @param Menu $children
     * @param int|bool $current_id
     * @return bool
     */
    private static function preftechChildren($children, $current_id = false)
    {
        if (!(count($children) > 0)) {
            return true;
        }

        foreach ($children as $child) {
            /** @var MenuItem $child */
            if ($child->getId() == $current_id) {
                continue;
            }
            self::preftechChildren($child->getChildren(), $child->getId());
        }

        return true;
    }

    /**
     * Helper method used to normalize URLs
     *
     * @param array $item
     * @param string $language_prefix
     * @return string
     */
    public static function getItemUrl($item, $language_prefix)
    {
        if (!$item['page_uri']) {
            $url = $item['item_uri'];
        } else {
            $url = $language_prefix . $item['page_uri'] . '.html';
        }

        return $url;
    }
}
