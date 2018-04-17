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
 * Class SqlconsoleAdmin
 */
class SqlconsoleAdmin extends ModuleAdminController
{
    // TODO Use query helper
    // TODO Fix insert template

    private $cache_ttl = 600; // 10mins
    private $maximum_query_length_for_history = 1024;

    public function __construct()
    {
        parent::__construct();

        $this->load->moduleLanguage();
        $this->load->helper('text');

        $this->assign('result', array());
        $this->assign('query_error', false);
        $this->assign('query_success', false);

        $this->assign('title', $this->lang->line('sqlconsole_module_name') . ' v' . $this->Module_model->getModuleDescriptor($this->modulerunner->getRunningModuleName())->getVersion());
        $this->assign('database_name', $this->_get_database_name());
    }

    public function index()
    {
        // Reading history
        $query_history = $this->auth->getSessionVariable('sqlconsole_query_history');
        if (!$query_history) {
            $query_history = array();
        }

        // Reading input
        $sql_input = trim($this->input->post('sql_input'));
        $query_separator = trim($this->input->post('query_separator'));
        if (!$query_separator) {
            $query_separator = ';';
        }

        if (strlen($sql_input) > 0) {
            if (!$this->_is_query_dangerous($sql_input)) {
                $this->db->db_debug = false;

                $this->load->moduleModel('sqlconsole', 'Sqlconsole_helper_model');
                $rs = $this->Sqlconsole_helper_model->runMultipleSqlQueries($sql_input, $query_separator);

                if (count($query_history) && end($query_history) !== $sql_input && strlen($sql_input) < $this->maximum_query_length_for_history && $rs) {
                    $query_history[] = $sql_input;
                    if (count($query_history) > 5) {
                        array_shift($query_history);
                    }
                }

                if (!$rs) {
                    $error = $this->db->error();
                    if (is_array($error)) {
                        $this->assign('query_error', $error['code'] . ' - ' . $error['message']);
                    }
                } else {
                    if (is_object($rs)) {
                        $this->assign('result', $rs->result_object());
                    } else {
                        $this->assign('query_success', true);
                    }
                }
            } else {
                $this->assign('query_error', $this->lang->line('sqlconsole_module_dangerous_query'));
            }

            if ($this->_is_query_refreshing($sql_input)) {
                $this->auth->setSessionVariable('sqlconsole_tables', null);
            }
        }

        // Read tables from session
        $tables = $this->auth->getSessionVariable('sqlconsole_tables');
        if ($tables === null) {
            $tables = array();
        }
        $tables_refresh_time = $this->auth->getSessionVariable('sqlconsole_tables');
        if ($tables_refresh_time === null) {
            $tables_refresh_time = 0;
        }


        // No session cache
        if (!$tables || $tables_refresh_time < time() - $this->cache_ttl) {
            $this->load->moduleLibrary('crud', 'TableUtility');
            $tables = $this->tableutility->getTablesDefinition();
            $this->auth->setSessionVariable('sqlconsole_tables', $tables);
            $this->auth->setSessionVariable('sqlconsole_tables_refresh_time', time());
        }
        $this->assign('tables', $tables);

        $this->auth->setSessionVariable('sqlconsole_query_history', $query_history);
        $this->assign('query_history', array_reverse($query_history));
        $this->assign('sql_input', $sql_input);
        $this->assign('query_separator', $query_separator);
        $this->display();
    }

    private function _is_query_refreshing($sql)
    {
        $sql = str_replace(array("\n", "\n"), ' ', $sql);
        $sql = strtolower(preg_replace('/\s+/', ' ', $sql));

        if (strpos($sql, 'drop table') !== false || strpos($sql, 'alter table') !== false || strpos($sql, 'rename table') !== false || $sql == 'refresh') {
            return true;
        }

        return false;
    }

    private function _is_query_dangerous($sql)
    {
        $sql = str_replace(array("\n", "\n"), ' ', $sql);
        $sql = strtolower(preg_replace('/\s+/', ' ', $sql));

        if (strpos($sql, 'drop database') !== false) {
            return true;
        }

        return false;
    }

    private function _get_database_name()
    {
        include INSTALLATIONPATH . 'application/config/database.php';
        $database_config = $db[$active_group];
        return $database_config['database'];
    }
}
