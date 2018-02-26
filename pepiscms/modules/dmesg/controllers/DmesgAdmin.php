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
 * Utilities for development
 */
class DmesgAdmin extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('title', 'Dmesg');
    }

    public function index()
    {
        $output = null;
        exec('dmesg | tail -n 30', $output, $ret_val);
        if ($ret_val !== 0) {
            die('Wrong return code ' . $ret_val);
        }

        $this->assign('output', $output);
        $this->display();
    }
}
