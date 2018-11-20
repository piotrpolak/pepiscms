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
 * Class LogsAdmin
 */
class LogsAdmin extends ModuleAdminController
{
    /**
     * Kind of cache
     */
    private $users = array();

    private $level_labels = array(
        Logger::MESSAGE_LEVEL_INFO => 'INFO',
        Logger::MESSAGE_LEVEL_DEBUG => 'DEBUG',
        Logger::MESSAGE_LEVEL_NOTICE => 'NOTICE',
        Logger::MESSAGE_LEVEL_WARNING => 'WARNING',
        Logger::MESSAGE_LEVEL_ERROR => 'ERROR',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->language('logs');
        //$this->Log_model->removeFrameworkLogs();
        $this->Log_model->importFrameworkLogs();

        $this->levels = array(Logger::MESSAGE_LEVEL_INFO => 'INFO', Logger::MESSAGE_LEVEL_NOTICE => 'NOTICE', Logger::MESSAGE_LEVEL_WARNING => 'WARNING', Logger::MESSAGE_LEVEL_ERROR => 'ERROR', Logger::MESSAGE_LEVEL_DEBUG => 'DEBUG');

        $this->assign('title', $this->lang->line('logs_module_name'));

        $this->users = $this->Generic_model->setTable($this->config->item('database_table_logs'))->getAssocPairs('user_id', 'user_email', $this->User_model->getTable());
    }

    public function index()
    {
        $this->load->library('DataGrid');
        $this->load->library('SimpleSessionMessage');
        $this->load->helper('text');

        $this->datagrid->setTitle($this->lang->line('logs_module_name'))
            ->setTable($this->config->item('database_table_logs'))
            ->setItemsPerPage(400)
            ->setDefaultOrder('timestamp', 'desc')
            ->setBaseUrl(module_url('logs') . 'index')
            ->setRowCssClassFormattingFunction(array($this, '_datagrid_row_callback'))
            ->addFilter(array('field' => 'timestamp', 'label' => 'Date from', 'filter_type' => DataGrid::FILTER_DATE, 'filter_condition' => 'ge'))
            ->addFilter(array('field' => 'timestamp', 'label' => 'Date to', 'filter_type' => DataGrid::FILTER_DATE, 'filter_condition' => 'le'));

        $module_name = 'logs';
        $definition = array(
            'timestamp' => array(
                'grid_formating_callback' => array($this, '__datagrid_format_date_column')
            ),
            'level' => array(
                'filter_type' => DataGrid::FILTER_SELECT,
                'filter_values' => $this->levels,
                'grid_formating_callback' => array($this, '_datagrid_format_level_column')
            ),
            'collection' => array(
                'filter_type' => DataGrid::FILTER_MULTIPLE_SELECT,
                'filter_values' => $this->Generic_model->getDistinctAssoc('collection')
            ),
            'message' => array(
                'filter_type' => DataGrid::FILTER_BASIC,
                'grid_formating_callback' => array($this, '_datagrid_format_message_column'),
            ),
            'user_id' => array(
                'filter_type' => DataGrid::FILTER_SELECT,
                'filter_values' => $this->users,
                'values' => $this->users,
                'grid_formating_callback' => array($this, '_datagrid_format_user_column'),
            ),
            'ip' => array(
                'filter_type' => DataGrid::FILTER_BASIC,
                'grid_formating_callback' => array($this, '_datagrid_format_ip_column')
            ),
            'module' => array(
                'filter_type' => DataGrid::FILTER_SELECT,
                'filter_values' => $this->Generic_model->getDistinctAssoc('module')
            ),
            'url' => array(
                'grid_formating_callback' => array($this, '_datagrid_format_url_column')
            ),
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

        $this->assign('datagrid', $this->datagrid->generate())
            ->assign('simple_session_message', $this->simplesessionmessage->getLocalizedMessage());
        $this->display();
    }

    public function performance_test()
    {
        $this->benchmark->mark('logs_performance_test_write_start');
        for ($i = 1; $i <= 100; $i++) {
            Logger::info('Logs performance test ' . $i, 'TEST');
        }

        Logger::info('Logs performance test 2', 'TEST');
        Logger::info('Logs performance test 3', 'TEST');

        $this->benchmark->mark('logs_performance_test_read_start');
        $this->db->from('logs')->order_by('timestamp', 'DESC')->limit(1000)->get()->result();

        $this->benchmark->mark('logs_performance_test_delete_start');
        $this->db->where('collection', 'TEST')->delete('logs');

        $this->benchmark->mark('logs_performance_test_delete_end');

        $write = $this->benchmark->elapsed_time('logs_performance_test_write_start', 'logs_performance_test_read_start');
        $read = $this->benchmark->elapsed_time('logs_performance_test_read_start', 'logs_performance_test_delete_start');
        $delete = $this->benchmark->elapsed_time('logs_performance_test_delete_start', 'logs_performance_test_delete_end');

        $msg = 'Write ' . ($write < 0.2 ? 'OK' : 'SLOW') . ' (' . $write . '/0.2)';
        $msg .= ' Read ' . ($read < 0.02 ? 'OK' : 'SLOW') . ' (' . $read . '/0.02)';
        $msg .= ' Delete ' . ($delete < 0.01 ? 'OK' : 'SLOW') . ' (' . $delete . '/0.01)';

        echo $msg;
    }

    public function mylogin()
    {
        $this->load->library('DataGrid');
        $this->datagrid->setTable($this->config->item('database_table_logs'), array(
            'user_id' => $this->auth->getUserId(),
            'collection' => 'LOGIN'
        ))
        ->setItemsPerPage(400)
        ->setDefaultOrder('timestamp', 'desc')
        ->setBaseUrl(module_url('logs') . '/mylogin')
        ->setRowCssClassFormattingFunction(array($this, '_datagrid_row_callback'));

        $module_name = 'logs';
        $definition = array(
            'timestamp' => array(),
            'message' => array(),
            'ip' => array(
                'grid_formating_callback' => array($this, '_datagrid_format_ip_column')
            )
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


        $this->assign('title', $this->lang->line('global_logs_view_own_login_history'))
            ->assign('datagrid', $this->datagrid->generate());
        $this->display();
    }

    public function details()
    {
        $id = $this->input->getParam('id');
        if (!$id) {
            show_404();
        }

        $item = $this->Log_model->getById($id);
        if (!$item) {
            show_404();
        }

        $this->assign('level_labels', $this->level_labels)
            ->assign('item', $item)
            ->assign('id', $id)
            ->assign('title', $this->lang->line('logs_log_details') . ' - ' . $id)
            ->assign('users', $this->users);
        $this->display();
    }

    public function ip()
    {
        $ip = $this->input->getParam('ip');
        if (!$ip) {
            show_404();
        }

        $ip_users = $this->Log_model->getUsersByIp($ip);
        if (!$ip_users) {
            show_404();
        }

        $this->assign('ip', $ip)
            ->assign('title', $this->lang->line('logs_ip') . ' ' . $ip)
            ->assign('ip_users', $ip_users)
            ->assign('ip_info', $this->Log_model->ip_info($ip));
        $this->display();
    }

    public function user()
    {
        $id = $this->input->getParam('id');
        if (!$id) {
            show_404();
        }
        $user = $this->User_model->getById($id);
        if (!$user) {
            show_404();
        }

        $this->assign('title', $this->lang->line('logs_user_details') . ' ' . $user->user_email)
            ->assign('user_data', $user)
            ->assign('related_users', $this->Log_model->getRelatedUsersByUserId($id))
            ->assign('user_activity', $this->Log_model->getUserActivitySummaryByUserId($id))
            ->assign('user_ips_activity', $this->Log_model->getIpActivitySummaryUserId($id));
        $this->display();
    }

    public function reseterrorlock()
    {
        $hash = $this->input->getParam('hash');
        if ($hash && strlen($hash) == 32 && $hash == str_replace(array('.', '/'), '', $hash)) {
            Logger::resetErrorLock($hash);
        }

        $this->load->library('SimpleSessionMessage');
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');
        redirect(module_url('logs'));
    }

    public function _datagrid_format_ip_column($content, $line)
    {
        return '<a href="' . module_url('logs') . 'ip/ip-' . $content . '">' . $content . '</a>';
    }

    public function __datagrid_format_date_column($content, $line)
    {
        list($date, $hour) = explode(' ', $content);
        return '<span style="white-space: nowrap"><a href="' . module_url() . 'details/id-' . $line->id . '"><b>' . $date . '</b><br>' . $hour . '</a></span>';
    }

    public function _datagrid_format_url_column($content, $line)
    {
        $label = str_replace(base_url(), '', $content);
        $label = str_replace('.', '.<wbr>', $label);
        $label = str_replace('/', '/<wbr>', $label);
        return '<a href="' . $content . '" title="' . $content . '">' . $label . '</a>';
    }

    public function _datagrid_format_message_column($content, $line)
    {
        $short = str_replace(array('/', '.'), array('/&#8203;', '.&#8203;'), substr($content, 0, 120));
        if ($short == $content) {
            return $short;
        }

        return '<span title="' . $content . '">' . $short . '...</span>';
    }

    public function _datagrid_format_user_column($content, $line)
    {
        if ($line->user_id > 0) {
            return '<a href="' . module_url('logs') . 'user/id-' . $line->user_id . '">' . $content . '</a>';
        } else {
            return 'N/A';
        }
    }

    public function _datagrid_format_level_column($content, $line)
    {
        if ($content == Logger::MESSAGE_LEVEL_INFO) {
            return '<span style="background-color: #CFFF9F; padding: 2px; display: block;">' . $this->level_labels[$content] . '</span>';
        }
        if ($content == Logger::MESSAGE_LEVEL_DEBUG) {
            return '<span style="background-color: #CFFF9F; padding: 2px; display: block;">' . $this->level_labels[$content] . '</span>';
        }
        if ($content == Logger::MESSAGE_LEVEL_NOTICE) {
            return '<span style="background-color: #FFFF99; padding: 2px; display: block;">' . $this->level_labels[$content] . '</span>';
        }
        if ($content == Logger::MESSAGE_LEVEL_WARNING) {
            return '<span style="background-color: #FFB871; padding: 2px; display: block;">' . $this->level_labels[$content] . '</span>';
        }
        if ($content == Logger::MESSAGE_LEVEL_ERROR) {
            return '<span style="background-color: #FF9F9F; padding: 2px; display: block;">' . $this->level_labels[$content] . '</span>';
        }

        return $content;
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
