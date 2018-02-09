<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * Parent class for all admin controllers (module)
 */
abstract class ModuleAdminController extends AdminController
{
    /**
     * Array of available modules.
     *
     * @var null|array
     */
    private $available_modules = NULL;

    /**
     * ModuleAdminController constructor.
     */
    public function __construct()
    {
        parent::__construct(FALSE);
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
            $rendered_menu = NULL;
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
            $this->assign('is_utilities_only_module', FALSE);
        }

        $this->benchmark->mark('module_admin_controller_end');
    }

    /** Callback * */
    protected function renderMenu()
    {
        $module_name = $this->modulerunner->getRunningModuleName();

        // This weird order is necessary as renderMenu() sometimes enters first
        if (ModuleRunner::isModuleDisplayedInUtilities($module_name) && !ModuleRunner::isModuleDisplayedInMenu($module_name)) {
            $this->assign('is_utilities_only_module', TRUE);
        }

        $this->load->library('MenuRendor');
        if ($this->getAttribute('is_utilities_only_module')) {
            return $this->menurendor->render('utilities', 'index', $this->input->getParam('language_code'));
        }
        return NULL;
    }

    /**
     * Attempts to load module model.
     *
     * @param $var
     * @return mixed
     * @since 1.0.0
     */
    public function __get($var)
    {
        $ci = CI_Controller::get_instance();

        // Automatic loading of module models
        if (!isset($ci->$var) && strpos($var, '_model') !== FALSE) {
            $ci->load->model($var);
            if (!isset($ci->$var)) {
                if ($this->available_modules === NULL) {
                    $this->available_modules = ModuleRunner::getAvailableModules();
                }

                foreach ($this->available_modules as $module) {
                    $ci->load->moduleModel($module, $var);
                    if (isset($ci->$var)) {
                        log_message('debug', 'Successfully loaded module model ' . $module . ':' . $var);
                        break;
                    }
                }
            }
        }
        return $ci->$var;
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
    public function display($view = false, $display_header = true, $display_footer = true, $return = FALSE)
    {
        // Preventing from displaying the same page for several times
        if ($this->already_displayed) {
            return FALSE;
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
        return TRUE;
    }

    /**
     * Displays the specified view as PDF.
     *
     * @param string|bool $view
     * @param bool $display_header
     * @param bool $display_footer
     */
    public function displayPDF($view = false, $display_header = true, $display_footer = true)
    {
        $this->load->helper('pdf');
        html_to_pdf($this->display($view, $display_header, $display_footer, true), FALSE, base_url());
        die();
    }
}
