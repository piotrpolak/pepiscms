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
 * User group model
 *
 * @since 0.1.0
 */
class Group_model extends Generic_model
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable($this->config->item('database_table_groups'));
        $this->setIdFieldName('group_id');
    }

    /**
     * Returns an object representing group
     *
     * @param int $group_id
     * @param string $fields
     * @return object
     */
    public function getById($group_id, $fields = '*')
    {
        $row = $this->db->select($fields)
            ->where('group_id', $group_id)
            ->get($this->getTable())
            ->row();

        $row->access = $this->getAccessRightsByGroupId($group_id);

        return $row;
    }

    /**
     * Returns access rights by group id
     *
     * @param int $group_id
     * @return array
     */
    public function getAccessRightsByGroupId($group_id)
    {
        $result = $this->db->select('*')
            ->where('group_id', $group_id)
            ->get($this->config->item('database_table_group_to_entity'))
            ->result();

        $access = array();
        foreach ($result as $line) {
            $access[$line->entity] = $line->access;
        }

        return $access;
    }

    /**
     * Inserts a new group
     *
     * @param string $group_name
     * @param array $access
     * @return int
     */
    public function insertGroup($group_name, $access = array())
    {
        $this->db->set('group_name', $group_name)
            ->insert($this->getTable());

        $group_id = $this->db->insert_id();

        foreach ($access as $entity => $access) {
            $this->db->set('group_id', $group_id)
                ->set('entity', $entity)
                ->set('access', $access)
                ->insert($this->config->item('database_table_group_to_entity'));
        }
        return $group_id;
    }

    /**
     * Tells whether the group exists
     *
     * @param string $group_name
     * @return bool
     */
    public function isGroupNameTaken($group_name)
    {
        if ($this->db->where('UPPER(group_name)', 'UPPER(' . $this->db->escape($group_name) . ')', false)->count_all_results($this->getTable())) {
            return true;
        }

        return false;
    }

    /**
     * Updates a group
     *
     * @param int $group_id
     * @param bool $group_name
     * @param array $access
     */
    public function update($group_id, $group_name = false, $access = array())
    {
        if ($group_name) {
            $this->db->set('group_name', $group_name)
                ->where('group_id', $group_id)
                ->update($this->getTable());
        }

        $this->db->where('group_id', $group_id)
            ->delete($this->config->item('database_table_group_to_entity'));

        foreach ($access as $entity => $access) {
            $this->db->set('group_id', $group_id)
                ->set('entity', $entity)
                ->set('access', $access)
                ->insert($this->config->item('database_table_group_to_entity'));
        }
    }

    /**
     * Deletes the group, moves all the users to NOBODY
     *
     * @param int $group_id
     * @return bool
     */
    public function deleteById($group_id)
    {
        $this->db->where('group_id', $group_id)
            ->delete($this->config->item('database_table_group_to_entity'));

        $this->db->where('group_id', $group_id)
            ->delete($this->config->item('database_table_user_to_group'));

        return $this->db->where('group_id', $group_id)
            ->delete($this->getTable());
    }
}
