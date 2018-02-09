<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/*
 * To enable mainpage controller add the following lines to your config.php
 *
    $config['mainpage_module'] = 'mainpage';
    $config['mainpage_module_method'] = 'index';
 */

/**
 * Mainpage controller to be used as the home page
 */
class Mainpage extends ModuleController
{
    public function index()
    {
        // Place your logic here

        $this->document->setTitle("Main page demo");
        $this->document->setDescription("Main page demo");

        $this->assign('current_datetime', new DateTime());
        $this->display();
    }
}