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

if (!function_exists('mysqldump')) {

    /**
     * Dumps database to file
     *
     * @param string $db_host
     * @param string $database
     * @param string $db_user
     * @param string $db_password
     * @param bool|array $tables
     * @param string|bool $dump_to_filename
     * @param bool $dump_structure
     * @return boolean
     */
    function mysqldump($db_host, $database, $db_user, $db_password, $tables = false, $dump_to_filename = false, $dump_structure = true)
    {
        if ($dump_to_filename && file_exists($dump_to_filename)) {
            return false;
        }

        $tables_sql = '';
        if (is_array($tables)) {
            $tables_sql = ' --tables';
            foreach ($tables as $table) {
                $tables_sql .= ' ' . escapeshellarg($table);
            }
        }

        $return_var = 1;
        system('command -v mysqldump >/dev/null 2>&1', $return_var);
        if ($return_var !== 0) {
            if (class_exists('Logger')) {
                Logger::warning('mysqldump command not found', 'BACKUP');
            }
            return false;
        }


        $command = 'mysqldump -h ' . escapeshellarg($db_host) . ' --password=' . escapeshellarg($db_password) . ' --user=' . escapeshellarg($db_user) . ' --databases ' . escapeshellarg($database) . '' . $tables_sql; //
        if ($dump_to_filename) {
            $command .= ' > ' . escapeshellarg($dump_to_filename) . '';
        }

        if (!$dump_structure) {
            $command .= ' --no-create-info';
        }

        $dump = '';
        $return_var = 1;
        ob_start();
        ob_clean();
        system($command, $return_var);
        if (!$dump_to_filename) {
            $dump = ob_get_contents();
        }
        ob_end_clean();


        $success = ($return_var == 0);
        if ($success) {
            if ($dump_to_filename) {
                return true;
            } else {
                return $dump;
            }
        }

        if (class_exists('Logger')) {
            Logger::warning('Unable to create MySQL dump, return code ' . $return_var, 'BACKUP');
        }
        return false;
    }
}
