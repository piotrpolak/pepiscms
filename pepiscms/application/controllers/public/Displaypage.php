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
 * Frontend dispatcher
 *
 * @since 0.1.0
 */
class DisplayPage extends EnhancedController
{
    const LANGUAGE_SEGMENT_INDEX = 4;

    public function mainpage()
    {
        $this->execute($this->getLanguageCode());
    }

    public function page()
    {
        // there is no language code prefix
        if ($this->uri->total_segments() === self::LANGUAGE_SEGMENT_INDEX) {
            $language_code = '';
            $uri_start_segment = self::LANGUAGE_SEGMENT_INDEX - 1;
        } // there is a language code prefix
        else {
            $language_code = $this->uri->segment(self::LANGUAGE_SEGMENT_INDEX);
            $uri_start_segment = self::LANGUAGE_SEGMENT_INDEX; // Array offset not segment position

            // language code prefix should be of 2 charachters long
            if (strlen($language_code) != 2) {
                $language_code = '';
                $uri_start_segment = self::LANGUAGE_SEGMENT_INDEX - 1;
            }
        }

        // generating the relative URI
        $uri = implode('/', array_slice($this->uri->segment_array(), $uri_start_segment));
        $this->execute($language_code, $uri);
    }

    /**
     * Main dispatch service
     *
     * @param string $language_code
     * @param string $uri
     * @return void
     */
    private function execute($language_code = '', $uri = '')
    {
        $this->load->library('ModuleRunner');
        $this->load->library('Widget');

        $this->doFrontendCheck();
        $this->doIntranetCheck($uri);

        $this->load->model('Site_language_model');
        Dispatcher::setSiteLanguage($this->Site_language_model->getLanguageByCodeCached($language_code));

        // unable to determine site language, show 404 error
        if (!Dispatcher::getSiteLanguage()) {
            show_error('Unable to determine site language', 404, 'Page not found');
            // show_404();
        }

        // Mainpage module handler
        if (!$uri) {
            $module_name = $this->config->item('mainpage_module');
            if ($module_name) {
                $method = $this->config->item('mainpage_module_method');
                $method = $method ? $method : 'index';
                if ($this->modulerunner->runModule($module_name, $method)) {
                    return;
                }
            }
        } // Attempt to run a module
        else {
            $uri_components = explode($this->config->item('module_uri_separator'), $uri);
            $method = isset($uri_components[1]) ? $uri_components[1] : 'index';
            $module_name = $uri_components[0];
            if ($module_name) {
                if ($this->modulerunner->runModule($module_name, $method)) {
                    return;
                }
            }
        }

        if (!$this->modulerunner->isModuleInstalled('pages')) {
            show_404();
        }


        $this->output->cache($this->config->item('cache_expires'));
        $this->load->model('Page_model');
        $page = null;

        if (strlen($uri) == 0) {  // For the default page (no item uri)
            $page = $this->Page_model->getDefaultPageCached(Dispatcher::getSiteLanguage()->code);
        } else {  // For any other document
            $page = $this->Page_model->getPageByUriCached($uri, Dispatcher::getSiteLanguage()->code);
        }

        if ($page == null) {
            show_404();
        } else {
            $this->doDisplayPage($page);
        }
    }

    /**
     * @return string
     */
    private function getLanguageCode()
    {
        // If there are exactly 4 segments, that means a language prefix is incuded, the user accessed /<language_iso>/
        if ($this->uri->total_segments() === self::LANGUAGE_SEGMENT_INDEX) {
            return $this->uri->segment(self::LANGUAGE_SEGMENT_INDEX);
        }
        return '';
    }

    /**
     * @param $uri
     */
    private function doIntranetCheck($uri)
    {
        $intranet = $this->config->item('cms_intranet');
        if ($intranet) {
            $this->load->library('Auth');

            if (!$this->auth->isAuthorized()) {
                if ($uri) {
                    $_SESSION['request_redirect'] = $uri . $this->config->item('url_suffix');
                } else {
                    $_SESSION['request_redirect'] = './';
                }

                redirect(admin_url() . 'login/sessionexpired');
            }
        }
    }

    private function doFrontendCheck()
    {
        $enable_frontend = $this->config->item('cms_enable_frontend');
        if ($enable_frontend === false) {
            redirect(admin_url() . 'login');
        }
    }

    /**
     * @param $page
     */
    private function doDisplayPage($page)
    {
        $data = array();
        $this->load->library('Document');
        $this->document->setId($page->page_id)
            ->setTitle($page->page_title)
            ->setContents($page->page_contents)
            ->setDescription($page->page_description)
            ->setKeywords($page->page_keywords)
            ->setRelativeUrl(Dispatcher::getUriPrefix() . $page->page_uri . '.html')
            ->setDefault($page->page_is_default);
        $data['document'] = $this->document;

        // Loading theme

        $site_theme_basepath = INSTALLATIONPATH . $this->config->item('theme_path') . $this->config->item('current_theme');
        $site_theme_file = $site_theme_basepath . '/index.php';
        if (file_exists($site_theme_file)) {
            $this->load->theme($site_theme_file, $data);
        } else {
            $this->load->library('Twig');
            $data['document'] = $this->document;
            $this->twig->setSiteThemeBasepath($site_theme_basepath);
            $output = $this->twig->render(APPPATH . 'views/public/cms_page.html.twig', $data);
            CI_Controller::get_instance()->output->set_output($output);
        }
    }
}
