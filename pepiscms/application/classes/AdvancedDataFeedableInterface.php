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
 * Feedable interface used by DataGrid
 *
 * Any model that is used by DataGrid should implement this inferface.
 * It is not required that the implementation is database one.
 *
 * @since 0.1.4
 */
interface AdvancedDataFeedableInterface
{
    /**
     * Returns data feed compatible with DataFeedableInterface, array consisting of rowcount and the actual data
     *
     * @param string $columns
     * @param int $start
     * @param int $rowcount
     * @param string $orderby_column
     * @param string $order
     * @param array $filters
     * @param Mixed $extra_param
     * @return array
     * @local
     */
    public function getAdvancedFeed($columns, $start, $rowcount, $orderby_column, $order, $filters, $extra_param);
}
