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
 * Query_helper
 *
 * @since 1.0
 */
class Query_helper extends ContainerAware
{
    /**
     * Runs multiple queries at once in a transaction. Returns result of last successful read query.
     *
     * @param $sql
     * @param string $query_separator
     * @return mixed
     */
    public function runMultipleSqlQueries($sql, $query_separator = ';')
    {
        $rs = false;

        $this->db->trans_start();
        if (strpos($sql, $query_separator) !== false) {
            $queries = explode($query_separator, $sql);
            foreach ($queries as $query) {
                $query = trim($query);
                if (strlen($query) == 0) {
                    continue;
                }

                $return_object = $this->db->is_write_type($query) ? null : true;
                $rs = $this->db->query($query, false, $return_object);
            }
        } else {
            $return_object = $this->db->is_write_type($sql) ? null : true;
            $rs = $this->db->query($sql, false, $return_object);
        }

        if (!$this->db->trans_complete()) {
            return false;
        }

        return $rs;
    }

    /**
     * Concatenates files contents and executes the queries inside a transaction.
     * @param array $paths
     * @param string $query_separator
     * @return mixed
     */
    public function runMultipleSqlQueriesFromPaths(array $paths, $query_separator = ';')
    {
        $sql = '';
        foreach ($paths as $path) {
            $sql .= file_get_contents($path) . ';';
        }

        return $this->runMultipleSqlQueries($sql, $query_separator);
    }
}