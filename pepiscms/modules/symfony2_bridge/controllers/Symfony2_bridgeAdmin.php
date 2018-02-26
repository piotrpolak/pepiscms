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
 * Symfony2 utilities
 */
class Symfony2_bridgeAdmin extends ModuleAdminController
{
    private $level_colors;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('DataGrid');
        $this->load->moduleLanguage('symfony2_bridge');
        $this->load->model('Symfony2_cache_model');

        $this->assign('title', $this->lang->line('symfony2_bridge_module_name'));
        $this->load->moduleLibrary('symfony2_bridge', 'Symfony2_bridge');

        $this->level_colors = array(
            'DEBUG' => DataGrid::ROW_COLOR_GREEN,
            'INFO' => DataGrid::ROW_COLOR_BLUE,
            'NOTICE' => DataGrid::ROW_COLOR_YELLOW,
            'WARNING' => DataGrid::ROW_COLOR_ORANGE,
            'ERROR' => DataGrid::ROW_COLOR_RED,
            'CRITICAL' => DataGrid::ROW_COLOR_RED,
            'ALERT' => DataGrid::ROW_COLOR_RED,
            'EMERGENCY' => DataGrid::ROW_COLOR_RED,
        );
    }

    public function index()
    {
        try {
            $this->assign('kernel', $this->symfony2_bridge->getKernel());
        } catch (Exception $e) {
            $this->assign('kernel', false);
        }

        $this->display();
    }

    public function logs()
    {
        $logFile = $this->symfony2_bridge->getKernel()->getLogDir() . '/';
        if (!$logFile) {
            show_error(sprintf($this->lang->line('symfony2_bridge_log_file_does_not_exist'), $logFile));
        }

        $this->load->moduleModel('symfony2_bridge', 'Symfony2_log_model');

        $this->Symfony2_log_model->setLogBasePath($logFile);
        $this->datagrid->setBaseUrl(module_url() . 'logs/');
        $this->datagrid->setFeedObject($this->Symfony2_log_model);
        $this->datagrid->setDefaultOrder('datetime', 'desc');
        $this->datagrid->setItemsPerPage(150);
        $this->datagrid->setRowCssClassFormattingFunction(array($this, '_datagrid_row_callback'));


        $this->datagrid->addFilter($this->lang->line('symfony2_bridge_datetime_from'), 'datetime', DataGrid::FILTER_DATE, date('Ymd'), DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL);
        $this->datagrid->addFilter($this->lang->line('symfony2_bridge_datetime_to'), 'datetime', DataGrid::FILTER_DATE, date('Ymd'), DataGrid::FILTER_CONDITION_LESS_OR_EQUAL);

        $definition = array(
            'datetime' => array(),
            'environment' => array(
                'filter_type' => DataGrid::FILTER_SELECT,
                'filter_values' => $this->Symfony2_log_model->getDistinctAssoc('environment'),
            ),
            'logger' => array(
                'filter_type' => DataGrid::FILTER_SELECT,
                'filter_values' => $this->Symfony2_log_model->getDistinctAssoc('logger'),
            ),
            'level' => array(
                'filter_type' => DataGrid::FILTER_SELECT,
                'filter_values' => $this->Symfony2_log_model->getDistinctAssoc('level'),
            ),
            'message' => array(
                'filter_type' => DataGrid::FILTER_BASIC,
            )
        );

        $module_name = 'symfony2_bridge';
        // Getting translations and setting input groups
        foreach ($definition as $field => &$def) {
            $key = isset($def['field']) ? $def['field'] : $field;

            // Getting label
            if (!isset($def['label'])) {
                $def['label'] = $this->lang->line($module_name . '_' . $key);
            }
        }
        $this->datagrid->setDefinition($definition);

        $this->assign('title', $this->lang->line('symfony2_bridge_symfony2_logs'));
        $this->assign('datagrid', $this->datagrid->generate());
        $this->display();
    }

    /**
     * Datagrid row format callback
     *
     * Available class names: green, red, blue, gray...
     *
     * @param object $line
     * @return string
     */
    public function _datagrid_row_callback($line)
    {
        if (isset($this->level_colors[$line->level])) {
            return $this->level_colors[$line->level];
        }

        return '';
    }

    public function clear_cache()
    {
        $cache_prod_is_removed = $this->Symfony2_cache_model->removeCache('prod');
        $cache_dev_is_removed = $this->Symfony2_cache_model->removeCache('dev');

        $this->load->library('user_agent');
        $this->load->library('SimpleSessionMessage');
        if ($cache_prod_is_removed || $cache_dev_is_removed) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            $this->simplesessionmessage->setRawMessage(sprintf($this->lang->line('symfony2_bridge_successfully_removed_cache'), ($cache_prod_is_removed ? 'production ' : '') . ($cache_dev_is_removed ? 'development ' : '')));
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_WARNING);
            $this->simplesessionmessage->setRawMessage($this->lang->line('symfony2_bridge_unable_to_remove_cache'));
        }

        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }
}
