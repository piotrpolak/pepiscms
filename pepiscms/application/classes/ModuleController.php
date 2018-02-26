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
 * Parent class for all public controllers (module)
 */
abstract class ModuleController extends EnhancedController
{
    /**
     * ModuleController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Logger');

        // Determining controller and method names
        $segment = 5;
        if (!Dispatcher::getSiteLanguage()->is_default) {
            $segment = 6;
        }
        $controller = $this->uri->segment($segment - 1);
        $method = $this->uri->segment($segment);

        if (empty($method)) {
            $method = 'index';
        }

        $this->setControllerName($controller);
        $this->setMethodName($method);
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
        $this->benchmark->mark('display_start');
        // Preventing from displaying the same page for several times
        if ($this->already_displayed) {
            return false;
        }

        // If there is no view specified, the default view is index
        if (!$view) {
            $view = $this->getMethodName();
        }

        $module_directory = $this->load->resolveModuleDirectory($this->modulerunner->getRunningModuleName());
        $views_basepath = $module_directory . '/views/public/';
        $site_theme_basepath = INSTALLATIONPATH . $this->config->item('theme_path') . $this->config->item('current_theme');

        $twig_suffix = '.html.twig';
        $use_twig = false;

        // Check if twig suffix is attached to the view string
        if (strpos($view, $twig_suffix) !== false) {
            $use_twig = true;
        } // Check if the twig file is present for a given view
        elseif (file_exists($views_basepath . $view . $twig_suffix)) {
            $view = $view . $twig_suffix;
            $use_twig = true;
        }


        // Building document object
        $this->document->setRelativeUrl(str_replace(base_url(), '', current_url()));
        $this->document->setCanonicalAbsoluteUrl(current_url());
        $this->document->setDefault(false);


        // Rendering Twig template
        if ($use_twig) {
            $this->load->library('Twig');
            $this->response_attributes['document'] = $this->document;
            $this->twig->setSiteThemeBasepath($site_theme_basepath);
            $output = $this->twig->render($views_basepath . $view, $this->response_attributes);
            CI_Controller::get_instance()->output->set_output($output);
        } else {
            $this->document->setContents($this->load->theme($views_basepath . $view . '.php', $this->response_attributes, true));
            $data['document'] = $this->document;
            $this->load->theme($site_theme_basepath . '/index' . '.php', $data);
        }

        // Reseting
        $this->response_attributes = array();
        $this->already_displayed = true;

        $this->benchmark->mark('display_end');
        return true;
    }
}
