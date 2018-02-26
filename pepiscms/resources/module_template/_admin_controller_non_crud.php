<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * {module_name} admin controller
 *
 * @author {author}
 * @date {date}
 * @classTemplateVersion 20150120
 */
class {module_class_name}Admin extends ModuleAdminController
{
    /**
     * Base path for file uploads
     *
     * @var String
     */
    //private $uploads_base_path = './application/cache/tmp/'; // Overwritten by constructor

    /**
     * Default constructor containing all necessary definitions
     */
    public function __construct()
    {
        parent::__construct();

        // Overwriting uploads base path
        $this->uploads_base_path = $this->config->item('uploads_path') . '{module_name}/';

        // Getting module name from class name
        $module_name = strtolower(str_replace('Admin', '', __CLASS__));

        // Loading module language, model, libraries, helpers
        $this->load->language($module_name);
        //$this->load->model('{model_class_name}');

        // Setting usefull labels, please note the convention
        $this->assign('title', $this->lang->line($module_name . '_module_name'));
    }

    public function index()
    {
        $this->display();
    }
}
