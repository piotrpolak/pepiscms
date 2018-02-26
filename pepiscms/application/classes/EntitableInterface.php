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
 * Entitable interface specyfying methods used to manipulate an entity by the form builder
 *
 * Provides methods for manipulating entities, used mostrly in CRUD.
 *
 * @since 0.1.5
 */
interface EntitableInterface
{
    /**
     * Saves by id, $data must be an associative array
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     * @local
     */
    public function saveById($id, $data);

    /**
     * Returns object by ID
     *
     * @param mixed $id
     * @return object
     * @local
     */
    public function getById($id);

    /**
     * Deletes by id
     *
     * @param mixed $id
     * @return bool
     * @local
     */
    public function deleteById($id);
}
