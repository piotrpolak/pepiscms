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

/**
 * Parent class for all admin controllers (module)
 */
abstract class ModuleAdminController extends AdminController
{
    /**
     * ModuleAdminController constructor.
     */
    public function __construct()
    {
        parent::__construct(false);
        $this->benchmark->mark('module_admin_controller_start');

        // Determining controller and method names
        $controller = $this->uri->segment(4);
        $method = $this->uri->segment(5);
        if (empty($method)) {
            $method = 'index';
        }
        $this->setControllerName($controller);
        $this->setMethodName($method);

        $this->assign('adminmenu', ''); // Just in case
        if (!$this->getAttribute('popup_layout')) {
            $this->benchmark->mark('menu_render_start');
            $rendered_menu = null;
            if (method_exists($this, 'renderMenu')) {
                $rendered_menu = $this->renderMenu();
                $this->assign('adminmenu', $rendered_menu);
            }

            if (!$rendered_menu) {
                $this->load->library('MenuRendor');
                $this->assign('adminmenu', $this->menurendor->render($controller, $method, $this->input->getParam('language_code')));
            }
            $this->benchmark->mark('menu_render_end');
        }

        // Preventing from having uninitialized variable
        if (!$this->getAttribute('is_utilities_only_module')) {
            // This weird order is necessary as renderMenu() sometimes enters first
            $this->assign('is_utilities_only_module', false);
        }

        $this->benchmark->mark('module_admin_controller_end');
    }

    /** Callback * */
    protected function renderMenu()
    {
        $module_name = $this->modulerunner->getRunningModuleName();

        // This weird order is necessary as renderMenu() sometimes enters first
        if (ModuleRunner::isModuleDisplayedInUtilities($module_name) && !ModuleRunner::isModuleDisplayedInMenu($module_name)) {
            $this->assign('is_utilities_only_module', true);
        }

        $this->load->library('MenuRendor');
        if ($this->getAttribute('is_utilities_only_module')) {
            return $this->menurendor->render('utilities', 'index', $this->input->getParam('language_code'));
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function __get($var)
    {
        return ContainerAware::__doGet($var);
    }

    /**
     * Loads and displays view.
     *
     * @param string|bool $view
     * @param bool $display_header
     * @param bool $display_footer
     * @param bool $return
     * @return bool
     */
    public function display($view = false, $display_header = true, $display_footer = true, $return = false)
    {
        // Preventing from displaying the same page for several times
        if ($this->already_displayed) {
            return false;
        }


        $return_html = '';

        // Displaying header if specified
        if ($display_header) {
            $last_view_html = $this->load->view('/admin/application_header', $this->response_attributes, $return);
            if ($return) {
                $return_html .= $last_view_html;
            }
        }

        // If view is an absolute path, then do not resolve the module path
        $path = realpath($view);

        if (!$path || !is_file($path)) {
            $module_directory = $this->load->resolveModuleDirectory($this->modulerunner->getRunningModuleName());

            if (!$view) {
                $view = $this->getMethodName();
            }

            $path = $module_directory . '/views/admin/' . $view . '.php';
        }

        // Loading theme
        $last_view_html = $this->load->theme($path, $this->response_attributes, $return);
        if ($return) {
            $return_html .= $last_view_html;
        }

        // Displaying footer if specified
        if ($display_footer) {
            $last_view_html = $this->load->view('/admin/application_footer', $this->response_attributes, $return);
            if ($return) {
                $return_html .= $last_view_html;
            }
        }

        // Resetting
        $this->response_attributes = array();
        $this->already_displayed = true;

        if ($return) {
            return $return_html;
        }
        return true;
    }
}
