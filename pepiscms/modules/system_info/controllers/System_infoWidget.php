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

class System_infoWidget extends Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->load->moduleLanguage('system_info');
        $this->load->moduleConfig('system_info');
        $this->load->helper('number');
    }

    public function disk_usage()
    {
        $quota = intval($this->config->item('system_info_max_quota_in_mb')) * 1024 * 1024;
        $occupied_space = $this->System_info_model->getOccupiedSpace($this->config->item('system_info_watch_dir'));

        if ($quota < 1) {
            $quota = $this->System_info_model->getFreeSpace();
        } else {
            $quota = $quota - $occupied_space;
        }

        $this->load->library('Google_chart_helper');
        return $this->assign('free_space', $quota)
            ->assign('occupied_space', $occupied_space)
            ->display('disk_usage');
    }
}