<?php

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sqlconsole_helper_model
 *
 * @since 0.2.3.0
 */
class Sqlconsole_helper_model extends CI_Model
{
    /**
     * Runs multiple queries at once
     *
     * @param $sql
     * @param string $query_separator
     * @return mixed
     */
    public function runMultipleSqlQueries($sql, $query_separator = ';')
    {
        $rs = false;

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

        return $rs;
    }
}
