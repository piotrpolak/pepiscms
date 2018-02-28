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
 *
 * @since 0.1.0
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

        $this->setControllerName($controller);
        $this->setMethodName($this->computeMethod($segment));
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

        if ($this->already_displayed) {
            return false;
        }

        if (!$view) {
            $view = $this->getMethodName();
        }

        $module_directory = $this->load->resolveModuleDirectory($this->modulerunner->getRunningModuleName());
        $views_basepath = $module_directory . '/views/public/';

        $twig_suffix = '.html.twig';
        $use_twig = false;


        // Building document object
        $this->document->setRelativeUrl(str_replace(base_url(), '', current_url()));
        $this->document->setCanonicalAbsoluteUrl(current_url());
        $this->document->setDefault(false);


        if (strpos($view, $twig_suffix) !== false) {
            $use_twig = true;
        } elseif (file_exists($views_basepath . $view . $twig_suffix)) {
            $view = $view . $twig_suffix;
            $use_twig = true;
        }


        // Rendering Twig template
        if ($use_twig) {
            $this->load->library('Twig');
            $this->response_attributes['document'] = $this->document;
            $this->twig->setSiteThemeBasepath($this->getSiteThemeBasePath());
            $this->output->set_output($this->twig->render($views_basepath . $view, $this->response_attributes));
        } else {
            $this->document->setContents($this->load->theme($views_basepath . $view . '.php', $this->response_attributes, true));
            $this->load->theme($this->getSiteThemeBasePath() . '/index.php', array(
                'document' => $this->document
            ));
        }

        $this->response_attributes = array();
        $this->already_displayed = true;

        $this->benchmark->mark('display_end');
        return true;
    }

    /**
     * @param $segment
     * @return mixed|string
     */
    private function computeMethod($segment)
    {
        $method = $this->uri->segment($segment);

        if (empty($method)) {
            $method = 'index';
        }
        return $method;
    }

    /**
     * @return string
     */
    private function getSiteThemeBasePath()
    {
        return INSTALLATIONPATH . $this->config->item('theme_path') . $this->config->item('current_theme');
    }
}
