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
 * Menu model
 *
 * @since 0.1.0
 */
class Menu_model extends Generic_model implements BackupableInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable($this->config->item('database_table_menu'));
        $this->setIdFieldName('item_id');
        $this->setAcceptedPostFields(array(
            'item_name',
            'parent_item_id',
            'item_order',
            'language_code',
            'page_id',
            'item_url'
        ));

        $this->load->library('Cachedobjectmanager');
    }

    /**
     * Returns page by menu item id
     *
     * @param $item_id
     * @return null
     */
    public function getPageIdByItemId($item_id)
    {
        $row = $this->db->where('item_id', $item_id)
            ->select('page_id')
            ->get($this->getTable())
            ->row();

        if ($row == null) {
            return null;
        }

        return $row->page_id;
    }

    /**
     * Returns menu item by page it
     *
     * @param $page_id
     * @return null
     */
    public function getItemIdByPageId($page_id)
    {
        $row = $this->db->where('page_id', $page_id)
            ->select('item_id')
            ->get($this->getTable())
            ->row();

        if ($row == null) {
            return null;
        }

        return $row->item_id;
    }

    /**
     * Inserts a new item to menu
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function saveById($id, $data)
    {
        if (!$id) {
            if (!isset($data['language_code']) || !$data['language_code']) {
                $data['language_code'] = 'en';
            }

            $row = $this->db->select('max(item_order)+1 as max_order')
                ->where('parent_item_id', $data['parent_item_id'])
                ->where('language_code', $data['language_code'])
                ->limit(1)
                ->get($this->getTable())
                ->row();

            if (!$row->max_order) {
                $row->max_order = 1;
            }

            $data['item_order'] = $row->max_order;
            $id = false;
        }

        return parent::saveById($id, $data);
    }

    /**
     * Returns menu structure (recursive)
     *
     * @param int $parent_item_id
     * @param string $language_code
     * @return array menu structure
     */
    public function getMenu($parent_item_id = 0, $language_code = 'en')
    {
        $menu_elements = array();

        $menu = $this->getSubMenu($parent_item_id, $language_code);

        foreach ($menu as $row) {
            if ($row['item_id'] == $parent_item_id) {
                continue;
            }

            $row['submenu'] = $this->getMenu($row['item_id'], $language_code);
            $menu_elements[] = $row;
        }

        return $menu_elements;
    }

    /**
     * Flattens menu into one dimensional array
     *
     * @param int $parent_item_id
     * @param string $language_code
     * @param bool $dont_enter_item_id
     * @param bool $separator
     * @param array $menu
     * @param bool $return
     * @param string $prefix
     * @return array|void
     */
    public function getMenuFlat($parent_item_id = 0, $language_code = 'en', $dont_enter_item_id = false, $separator = false, &$menu = array(), $return = true, $prefix = '')
    {
        if (!$language_code) {
            $language_code = 'en';
        }

        if (!$separator) {
            $separator = ' &raquo ';
        }

        $submenu = $this->getSubMenu($parent_item_id, $language_code);

        foreach ($submenu as $row) {
            if ($dont_enter_item_id && $dont_enter_item_id == $row['item_id']) {
                continue;
            }

            $menu[$row['item_id']] = $prefix . $row['item_name'];
            $this->getMenuFlat($row['item_id'], $language_code, $dont_enter_item_id, $separator, $menu, false, $menu[$row['item_id']] . $separator);
        }

        if ($return) {
            return $menu;
        }
    }

    /**
     * Returns menu structure (recursive) Cached
     *
     * @param int $parent_item_id
     * @param string $language_code
     * @return array menu structure
     */
    public function getMenuCached($parent_item_id = 0, $language_code = 'en')
    {
        $object_name = 'menu_' . $parent_item_id . '_' . $language_code;
        $object = $this->cachedobjectmanager->getObject($object_name, 3600 * 24, 'pages');

        if ($object === false) {
            $object = $this->getMenu($parent_item_id, $language_code);
            $this->cachedobjectmanager->setObject($object_name, $object, 'pages');
        }

        return $object;
    }

    /**
     * Returns submenu structure
     *
     * @param int $parent_item_id
     * @param string $language_code
     * @return array
     */
    public function getSubMenu($parent_item_id = 0, $language_code = 'en')
    {
        if ($parent_item_id === null) {
            return array();
        }

        // item_uri is just for compatibility
        return $this->select()
            ->where('parent_item_id', $parent_item_id)
            ->where($this->config->item('database_table_menu') . '.language_code', $language_code)
            ->order_by('item_order')
            ->get()
            ->result_array();
    }


    /**
     * Returns menu structure (recursive) Cached
     *
     * @param int $parent_item_id
     * @param string $language_code
     * @return array menu structure
     */
    public function getSubMenuCached($parent_item_id = 0, $language_code = 'en')
    {
        $object_name = 'submenu_' . $parent_item_id . '_' . $language_code;
        $object = $this->cachedobjectmanager->getObject($object_name, 3600 * 24, 'pages');

        if ($object === false) {
            $object = $this->getSubMenu($parent_item_id, $language_code);
            $this->cachedobjectmanager->setObject($object_name, $object, 'pages');
        }

        return $object;
    }

    /**
     * Returns super menu structure
     *
     * @param int $item_id
     * @param string $language_code
     * @return array
     */
    public function getSuperMenu($item_id = 0, $language_code = 'en')
    {
        if ($item_id === null) {
            return null;
        }

        $row = $this->select()
            ->where($this->config->item('database_table_menu') . '.item_id', $item_id)
            ->limit(1)
            ->get()
            ->row();

        if (!$row || !$row->parent_item_id) {
            return;
        }

        return $this->getSubMenu($row->parent_item_id, $language_code);
    }

    // ------------------------------------------------------------------------

    /**
     * Returns menu item for given page id
     *
     * @param int $page_id
     * @return bool|stdClass
     */
    public function getElementByPageId($page_id)
    {
        if (!$page_id) {
            return false;
        }
        return $this->db->select('*')->where('page_id', $page_id)->get($this->getTable())->row();
    }

    /**
     * Checks if a given menu element has children
     *
     * @param int $item_id
     * @return bool
     */
    public function hasChildren($item_id)
    {
        $this->db->where('parent_item_id', $item_id)->from($this->config->item('database_table_menu'));

        return ($this->db->count_all_results() == 0 ? false : true);
    }

    /**
     * Checks is the given item exists
     *
     * @param int $item_name
     * @param int $parent_item_id
     * @param string $language_code
     * @return bool
     */
    public function itemExists($item_name, $parent_item_id, $language_code = 'en')
    {
        $this->db->where('item_name', $item_name)
            ->where('parent_item_id', $parent_item_id)
            ->where('language_code', $language_code)
            ->from($this->config->item('database_table_menu'));

        return ($this->db->count_all_results() == 0 ? false : true);
    }

    /**
     * Returns the menu path
     *
     * @param int $item_id
     * @return array
     */
    public function getItemPath($item_id)
    {
        // Finds a path to an item

        $path = array();

        //FIXME This does not return the correct path when one of the elements is a mapped element

        while ($item_id != 0) {
            $row = $this->db->select($this->config->item('database_table_menu') . '.item_id, ' . $this->config->item('database_table_menu') . '.parent_item_id, ' . $this->config->item('database_table_menu') . '.item_name, ' . $this->config->item('database_table_pages') . '.page_uri')
                ->where($this->config->item('database_table_menu') . '.item_id', $item_id)
                ->join($this->config->item('database_table_pages'),
                    $this->config->item('database_table_pages') . '.page_id = ' . $this->config->item('database_table_menu') . '.page_id')
                ->get($this->config->item('database_table_menu'))
                ->row();

            if (!isset($row->parent_item_id)) {
                break;
            }

            $item_id = $row->parent_item_id;

            $path[] = $row;
        }

        return array_reverse($path);
    }

    // ------------------------------------------------------------------------
    // Implementation of BackupableInterface
    // ------------------------------------------------------------------------

    /**
     * Projects the table for backup
     *
     * @return array
     */
    public function doBackupProjection()
    {
        // Used for backup only
        return $this->db->select('*')
            ->order_by('item_id')
            ->get($this->config->item('database_table_menu'))
            ->result();
    }

    /**
     * Restores table's content from backup
     *
     * @param array $items
     * @param int|null $user_id
     * @return void
     */
    public function doBackupRestore(&$items, $user_id = null)
    {
        $this->db->query('ALTER TABLE ' . $this->config->item('database_table_menu') . ' DISABLE KEYS');
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        $fields = array('item_id', 'parent_item_id', 'item_order', 'item_name', 'language_code', 'item_url', 'page_id');

        foreach ($items as $item) {
            foreach ($fields as $field) {
                $this->db->set($field, '' . $item->$field);
            }

            if (strlen('' . $item->parent_item_id) == 0) {
                $this->db->set('parent_item_id', null);
            }

            $success = $this->db->insert($this->config->item('database_table_menu'));

            if ($success) {
                if ($item->item_id == 0) {
                    $this->db->set('item_id', 0)
                        ->where('item_id = 1')
                        ->update($this->config->item('database_table_menu'));
                }
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        $this->db->query('ALTER TABLE ' . $this->config->item('database_table_menu') . ' ENABLE KEYS');
    }

    /**
     * Prepares for backup restore (cleans the table)
     */
    public function doBackupPrepare()
    {
        // Removes all the items
        $result = $this->db->select('item_id')
            ->order_by('parent_item_id DESC')
            ->get($this->config->item('database_table_menu'))->result();

        foreach ($result as $row) {
            if ($row->item_id == 0) {
                continue;
            }

            $this->db->where('item_id', $row->item_id)
                ->from($this->config->item('database_table_menu'))
                ->delete();
        }

        // Trunkates table
        $this->db->truncate($this->config->item('database_table_menu'));
    }

    /**
     * @return CI_DB_query_builder
     */
    private function select()
    {
        return $this->db->select($this->config->item('database_table_menu') . '.*, ' . $this->config->item('database_table_pages') . '.page_uri, ' . $this->config->item('database_table_menu') . '.item_url AS item_uri, ' . $this->config->item('database_table_pages') . '.timestamp_modified, ' . $this->config->item('database_table_pages') . '.timestamp_created, ' . $this->config->item('database_table_pages') . '.page_id, ' . $this->config->item('database_table_pages') . '.page_is_default')
            ->from($this->config->item('database_table_menu'))
            ->join($this->config->item('database_table_pages'),
                $this->config->item('database_table_pages') . '.page_id = ' . $this->config->item('database_table_menu') . '.page_id', 'left');
    }
}
