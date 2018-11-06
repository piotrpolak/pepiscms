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
 * Example usage <?=$this->widget->create('logs', 'logs')->render( 'COLECTIONNAME', $this->formbuilder->getId(), 'Modifications' );
 */
class LogsWidget extends Widget
{
    public function warnings($days_before)
    {
        $this->load->model('Log_model');
        $this->load->helper('date');

        $values = fill_date_spectrum_values($this->Log_model->getWarningStatistics($days_before), time(), $days_before);

        $this->load->library('Google_chart_helper');
        return $this->google_chart_helper->drawSimpleLineChart($values, 'Date', 'Warnings',
            '100%', 200, 50, 'date', 'number', array('red'));
    }


    public function logs($collection, $resource_id = null, $title = false)
    {
        $where_conditions = array('collection' => $collection);

        if ($resource_id) {
            $where_conditions['resource_id'] = $resource_id;
        }


        $this->load->model('Log_model');
        $this->load->language('logs');

        if (!$title) {
            $title = $this->lang->line('logs_logs');
        }

        $this->load->library('DataGrid');
        $this->datagrid->setTitle($title)->setTable($this->Log_model->getTable(), $where_conditions)
            ->setItemsPerPage(400)
            ->setDefaultOrder('timestamp', 'desc')
            ->setOrderable(false)
            ->setBaseUrl(admin_url() . 'logs/mylogin')
            ->setRowCssClassFormattingFunction(array($this, '_datagrid_row_callback'));

        $module_name = 'logs';

        $definition = array(
            'timestamp' => array(),
            'message' => array(),
            'user_id' => array(
                'grid_formating_callback' => (SecurityManager::hasAccess('cms_users', 'edit', 'cms_users') ? array($this, '_datagrid_format_user_column') : false),
                'values' => $this->Generic_model->setTable($this->config->item('database_table_logs'))->getAssocPairs('user_id', 'user_email', $this->User_model->getTable()),
            ),
            'ip' => array()
        );

        // Getting translations and setting input groups
        foreach ($definition as $field => &$def) {
            $key = isset($def['field']) ? $def['field'] : $field;

            // Getting label
            if (!isset($def['label'])) {
                $def['label'] = $this->lang->line($module_name . '_' . $key);
            }
        }

        $this->datagrid->setDefinition($definition);

        return $this->datagrid->generate();
    }

    public function _datagrid_format_user_column($content, $line)
    {
        if ($line->user_id > 0) {
            return '<a href="' . module_url('cms_users') . 'edit/id-' . $line->user_id . '">' . $content . '</a>';
        } else {
            return 'N/A';
        }
    }

    public function _datagrid_row_callback($line)
    {
        if ($line->level == Logger::MESSAGE_LEVEL_INFO) {
            return DataGrid::ROW_COLOR_GREEN;
        }
        if ($line->level == Logger::MESSAGE_LEVEL_NOTICE) {
            return DataGrid::ROW_COLOR_YELLOW;
        }
        if ($line->level == Logger::MESSAGE_LEVEL_WARNING) {
            return DataGrid::ROW_COLOR_ORANGE;
        }
        if ($line->level == Logger::MESSAGE_LEVEL_ERROR) {
            return DataGrid::ROW_COLOR_RED;
        }

        return '';
    }
}
