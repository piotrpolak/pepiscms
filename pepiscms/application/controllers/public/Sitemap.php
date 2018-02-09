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
 * Sitemap controller
 */
class Sitemap extends CI_Controller
{
    public function index()
    {
        // No caching since there is no support for headers caching

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }

        $this->load->library('CachedDirectoryReader', Array('cache_directory' => $cache_path, 'cache_expires' => 60 * 60 * 12));
        $this->load->model('Page_model');
        $this->load->model('Site_language_model');

        $data['base_url'] = base_url();
        $data['uris'] = $this->Page_model->getAllSitemapableUris();
        $data['url_suffix'] = $this->config->item('url_suffix');

        $sl = $this->Site_language_model->getLanguageByCode('');
        $data['defaul_language'] = $sl->code;

        $static_path = 'static/';
        if (!file_exists($static_path)) {
            $data['static'] = $this->cacheddirectoryreader->readDirectory($static_path);
        }


        $this->load->library('ModuleRunner');
        $this->load->model('Module_model');
        $modules = ModuleRunner::getAvailableModules();
        foreach ($modules as $module_name) {
            $module_links = $this->Module_model->getModuleSitemapURLs($module_name);
            if (is_array($module_links)) {
                $data['static'] = array_merge($data['static'], $module_links);
            }
        }

        if ($this->uri->segment(4) == 'txt') {
            header('Content-type: text/plain');
            $this->load->view('public/sitemap_txt', $data);
        } elseif ($this->uri->segment(4) == 'xml') {
            header('Content-type: text/xml');
            $this->load->view('public/sitemap_xml', $data);
        }
    }

}
