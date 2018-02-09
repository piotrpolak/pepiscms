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
 * Defines method used to change order of elements
 *
 * @since 0.2.2.7
 */
interface MoveableInterface
{
    /**
     * This method is used to move elements of a list. It is used for changing the order of menu elements in page controller.
     *
     * Don't try to understand how it works, even for the author is looks like magic after some months.
     * The following method takes all the items belonging to the same "group"
     * (all items having the same value of constraint_field),
     * moves the elements (swaps the item_order field) and normalizes the item_order values.
     *
     * @param int $id
     * @param string $direction
     * @param string|bool $table
     * @param string|bool $constraint_field_name
     * @param string $item_order_field_name
     * @param string $id_field_name
     * @return void
     * @local
     */
    public function move($id, $direction, $table = FALSE, $constraint_field_name = FALSE, $item_order_field_name = 'item_order', $id_field_name = 'id');
}
