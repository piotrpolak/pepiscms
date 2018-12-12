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
    }

    public function disk_usage()
    {
        $this->load->library('Google_chart_helper');
        return $this->assign('free_space', $this->System_info_model->getFreeSpace())
            ->assign('occupied_space', $this->System_info_model->getOccupiedSpace())
            ->display('disk_usage');
    }
}