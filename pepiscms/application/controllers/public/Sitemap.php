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
 * Sitemap controller
 */
class Sitemap extends CI_Controller
{
    public function index()
    {
        $enable_frontend = $this->config->item('cms_enable_frontend');
        $intranet = $this->config->item('cms_intranet');

        if ($enable_frontend === false || $intranet) {
            show_404();
        }

        // No caching since there is no support for headers caching

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }

        $this->load->library('CachedDirectoryReader', array('cache_directory' => $cache_path, 'cache_expires' => 60 * 60 * 12));

        $data['base_url'] = base_url();

        $data['urls'] = array();

        $static_path = 'static/';
        if (!file_exists($static_path)) {
            $data['urls'] = $this->cacheddirectoryreader->readDirectory($static_path);
        }


        $this->load->library('ModuleRunner');
        $this->load->model('Module_model');
        $modules = ModuleRunner::getAvailableModules();
        foreach ($modules as $module_name) {
            $module_links = $this->Module_model->getModuleSitemapURLs($module_name);
            if (is_array($module_links)) {
                $data['urls'] = array_merge($data['urls'], $module_links);
            }
        }

        $response_type = $this->uri->segment(4);

        if ($response_type == 'txt') {
            header('Content-type: text/plain');
        } elseif ($response_type == 'xml') {
            header('Content-type: text/xml');
        } else {
            show_404();
        }

        $this->load->view('public/sitemap_' . $response_type, $data);
    }
}
