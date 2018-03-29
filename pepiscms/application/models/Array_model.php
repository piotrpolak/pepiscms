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
 * Array model is the an abstract model implementing feedable interface out of
 * raw array data, allows building abstract models for CRUD from any source
 * (XML, CSV, web services)
 *
 * @since 0.2.2.7
 */
abstract class Array_model extends PEPISCMS_Model implements BasicDataFeedableInterface, AdvancedDataFeedableInterface, EntitableInterface
{
    const DATE_FORMAT = 'Y-m-d';
    const DEFAULT_ID_FIELD_NAME = 'id';

    /**
     * Order by column name
     *
     * @var bool|string
     */
    private $_order_by_column = false;

    /**
     * Order type ASC or DESC
     *
     * @var string
     */
    private $_order = 'ASC';

    /**
     * Number of seconds for the cache to live
     *
     * @var int
     */
    private $cache_ttl = 0;

    /**
     * Cache param value
     *
     * @var mixed
     */
    private $cache_param = null;

    /**
     * Basic feed cache array
     *
     * @var array
     */
    private $basic_feed_cached = array();

    // -------------------------------------------------------------------------
    // Array_model specific methods
    // -------------------------------------------------------------------------

    /**
     * Sets cache TTL in seconds
     *
     * @param int $cache_ttl
     * @local
     */
    public function setCacheTtl($cache_ttl)
    {
        $this->cache_ttl = $cache_ttl;
    }

    /**
     * Returns cache TTL in seconds
     *
     * @return int
     * @local
     */
    public function getCacheTtl()
    {
        return $this->cache_ttl;
    }

    /**
     * Sets cache param in seconds
     *
     * @param mixed $cache_param
     * @local
     */
    public function setCacheParam($cache_param)
    {
        $this->cache_param = $cache_param;
    }

    /**
     * Returns cache param in seconds
     *
     * @return mixed
     * @local
     */
    public function getCacheParam()
    {
        return $this->cache_param;
    }

    /**
     * Removes all cache, to be used by delete, saveById
     *
     * @local
     */
    public function cleanCache()
    {
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup($this->getCacheCollectionName());
    }

    /**
     * Returns cache collection name
     *
     * @return string
     * @local
     */
    protected function getCacheCollectionName()
    {
        return strtolower('array_cache_' . get_class($this));
    }

    // -------------------------------------------------------------------------
    // AdvancedDataFeedableInterface
    // -------------------------------------------------------------------------

    /**
     * Gets distinct values from a table's collumn.
     * If table parameter is not specified, it will take it from the instance $table variable.
     * You can predefine the return array by specifying $pairs.
     *
     * @param string $column
     * @return array
     * @local
     */
    public function getDistinctAssoc($column)
    {
        // Output array
        $distinct_assoc = array();

        // Getting the feed out of the cache
        $output = $this->getBasicFeedCached(false);

        // For every line
        foreach ($output as $line) {
            // Checking if the column is defined in the input feed and not defined in the output feed
            if (!isset($line->$column) || isset($distinct_assoc[$line->$column])) {
                continue;
            }

            // Building the output feed
            $distinct_assoc[$line->$column] = $line->$column;
        }

        return $distinct_assoc;
    }


    /**
     * Returns a raw array of objects
     *
     * @param mixed $extra_param
     * @return array a raw array of objects
     * @local
     */
    public function getBasicFeedCached($extra_param)
    {
        // Library level cache
        if (isset($this->basic_feed_cached[$extra_param])) {
            return $this->basic_feed_cached[$extra_param];
        }

        // Only if the cache is used
        if ($this->cache_ttl > 0) {
            $collection = $this->getCacheCollectionName();
            $cache_variable_name = 'array_feed_cache_' . __CLASS__ . '_' . serialize(array($extra_param, $this->cache_param));

            // Reading cache
            $this->load->library('Cachedobjectmanager');
            $output = $this->cachedobjectmanager->getObject($cache_variable_name, $this->cache_ttl, $collection);

            // No cache - then read the the feed from the object and save it in cache
            if (!$output) {
                $this->load->model('Module_model');
                $output = $this->getBasicFeed($extra_param);
                $this->cachedobjectmanager->setObject($cache_variable_name, $output, $collection);
            }
        } else {
            // No cache, read the feed directly from the object
            $output = $this->getBasicFeed($extra_param);
        }

        $this->basic_feed_cached[$extra_param] = $output;
        return $output;
    }

    /**
     * Returns data feed compatible with DataFeedableInterface, array consisting of rowcound and the actual data
     *
     * @param string $columns
     * @param int $offset
     * @param int $rowcount
     * @param string $order_by_column
     * @param string $order
     * @param array $filters
     * @param Mixed $extra_param
     * @return array
     * @local
     */
    public function getAdvancedFeed($columns, $offset, $rowcount, $order_by_column, $order, $filters, $extra_param)
    {
        // Reading the basic feed out of the implementation class
        $output = $this->getBasicFeedCached($extra_param);

        if ($order_by_column) {
            $this->applyOrder($output, $order_by_column, $order);
        }

        if (count($filters)) {
            self::applyFilters($output, $filters);
        }

        return array($this->applyOffset($output, $offset, $rowcount), count($output));
    }

    /**
     * Orders the feed by a given column
     *
     * @param array $output
     * @param string $column
     * @param string $order
     */
    protected function applyOrder(&$output, $column, $order)
    {
        $this->_order_by_column = $column;
        $this->_order = $order;
        uasort($output, array($this, 'sort'));
    }

    /**
     * Sorting function
     *
     * @param string $a
     * @param string $b
     * @return int
     */
    protected function sort($a, $b)
    {
        $oc = $this->_order_by_column;
        if ($this->_order == 'DESC') {
            return strnatcmp($b->$oc, $a->$oc);
        } else {
            return strnatcmp($a->$oc, $b->$oc);
        }
    }

    /**
     * Slices out the array, similar to LIMIT in SQL
     *
     * @param $output
     * @param $offset
     * @param $rowCount
     * @return array
     */
    protected function applyOffset($output, $offset, $rowCount)
    {
        $filtered_output = array();

        $current_index = -1;

        foreach ($output as $line) {
            $current_index++;

            // Row before index
            if ($offset > $current_index) {
                continue;
            }

            // Row after index - no need for looping
            if ($offset + $rowCount - 1 < $current_index) {
                break;
            }

            $filtered_output[] = $line;
        }

        return $filtered_output;
    }

    /**
     * Static method that applies filters specified in the DataGrid format upon the specified DB object.
     * This method is very useful when you write your own getAdvancedFeed method and you want it to be compatible with the filers.
     *
     * @param $output
     * @param $filters
     */
    protected function applyFilters(&$output, $filters)
    {
        $output_new = array();

        // Reading the original feed line by line
        foreach ($output as &$line) {
            // Value indicating whether the line should be added to the feed
            $add_line = true;

            foreach ($filters as $filter) {
                // Filtering is case insensitive
                $column_value = strtolower($line->{$filter['column']});

                // Rounding up dates, correcting the behavior
                if ($filter['type'] == DataGrid::FILTER_DATE) {
                    // Extracting date out of datetime out of  the column value
                    $column_value = new DateTime($column_value);
                    $column_value = new DateTime($column_value->format(self::DATE_FORMAT));

                    // Extracting date out of datetime out of  the query value
                    foreach ($filter['values'] as &$value) {
                        $value = new DateTime($value);
                        $value = new DateTime($value->format(self::DATE_FORMAT));
                    }
                } else {
                    // For non-date filters
                    foreach ($filter['values'] as &$value) {
                        $value = strtolower($value);
                    }
                }


                // Applying IN (in array) filter
                if ($filter['condition'] == 'in') {
                    $add_line = false;
                    foreach ($filter['values'] as $query) {
                        if ($column_value == $query) {
                            $add_line = true;
                            break;
                        }
                    }
                } else {
                    // For non IN filters
                    $query = $filter['values'][0];

                    // Applying LIKE filter
                    if ($filter['condition'] == 'like') {
                        if (strpos($column_value, $query) === false) {
                            $add_line = false;
                            break;
                        }
                    } // Applying EQ (equals) filter
                    elseif ($filter['condition'] == 'eq') {
                        if ($column_value != $query) {
                            $add_line = false;
                            break;
                        }
                    } // Applying NE (not equals) filter
                    elseif ($filter['condition'] == 'ne') {
                        if ($column_value == $query) {
                            $add_line = false;
                            break;
                        }
                    } // Applying GT (greater) filter
                    elseif ($filter['condition'] == 'gt') {
                        if (!($column_value > $query)) {
                            $add_line = false;
                            break;
                        }
                    } // Applying GE (greater or equal) filter
                    elseif ($filter['condition'] == 'ge') {
                        if (!($column_value >= $query)) {
                            $add_line = false;
                            break;
                        }
                    } // Applying LT (less than) filter
                    elseif ($filter['condition'] == 'lt') {
                        if (!($column_value < $query)) {
                            $add_line = false;
                            break;
                        }
                    } // Applying LE (less or equal) filter
                    elseif ($filter['condition'] == 'le') {
                        if (!($column_value <= $query)) {
                            $add_line = false;
                            break;
                        }
                    }
                }
            }


            // Cleaning some memory and going to the next element
            if (!$add_line) {
                $line = null;
                unset($line);
                continue;
            }

            // Adding a new line
            $output_new[] = $line;
        }

        // Overwriting output
        $output = $output_new;
    }

    // -------------------------------------------------------------------------
    // Entitable
    // -------------------------------------------------------------------------

    /**
     * Returns object by ID
     *
     * @param mixed $id
     * @return stdClass|bool
     * @local
     */
    public function getById($id)
    {
        // Getting all the items
        $feed = $this->getAdvancedFeed('*', 0, 999999, false, false, array(), false);

        // Getting id field name
        $id_field_name = $this->getIdFieldName();

        // Walking trough the items until object of specified ID is found
        foreach ($feed[0] as $line) {
            if ($line->$id_field_name == $id) {
                return $line;
            }
        }

        return false;
    }

    /**
     * Deletes by id
     *
     * @param mixed $id
     * @return bool
     * @local
     */
    public function deleteById($id)
    {
        $this->cleanCache();
        return false;
    }

    /**
     * Saves by id, $data must be an associative array and it will be filtered
     * using accepted post fields
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     * @local
     */
    public function saveById($id, $data)
    {
        $this->cleanCache();
        return false;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns a list of all elements
     *
     * @return array
     * @local
     */
    public function getAll()
    {
        $feed = $this->getAdvancedFeed('*', 0, 999999, false, false, array(), false);
        return $feed[0];
    }

    /*
     * Required by CRUD
     */
    public function setWhere($where)
    {
    }

    /*
     * Required by CRUD
     */
    public function getIdFieldName()
    {
        return self::DEFAULT_ID_FIELD_NAME;
    }

    /*
     * Required by CRUD
     */
    public function getTable()
    {
        return false;
    }
}
