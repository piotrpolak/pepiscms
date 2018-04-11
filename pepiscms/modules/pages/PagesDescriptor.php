<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * cms_pages descriptor class
 */
class PagesDescriptor extends ModuleDescriptor
{

    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        $this->load->moduleModel($this->module_name, 'Page_model');
        $this->load->model('Site_language_model');
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        $this->load->moduleLanguage($this->module_name);
        return $this->lang->line($this->module_name . '_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        $this->load->moduleLanguage($this->module_name);
        $description_label = $this->module_name . '_module_description';
        $description = $this->lang->line($this->module_name . '_module_description');
        if ($description == $description_label) {
            return '';
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInMenu()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        $paths = array(
            'pages.sql',
            'upgrade/0.2.3.0.sql',
            'upgrade/1.0.0.sql',
        );

        $sql_basepath = $this->load->resolveModuleDirectory($this->module_name, false) . '/resources/sql/';

        return $this->executeSqls($paths, $sql_basepath);
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        $sql_basepath = $this->load->resolveModuleDirectory($this->module_name, false) . '/resources/sql/';

        return $this->executeSqls(array('uninstall.sql'), $sql_basepath);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return SubmenuBuilder::create()
            ->addItem()
            ->withController($this->module_name)
            ->withMethod('edit')
            ->withLabel($this->lang->line($this->module_name . '_add'))
            ->withDescription($this->lang->line($this->module_name . '_add_description'))
            ->withIconUrl(module_resources_url($this->module_name) . 'icon_16.png')
            ->end()
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return SubmenuBuilder::create()
            ->addItem()
            ->withController($this->module_name)
            ->withMethod('edit')
            ->withLabel($this->lang->line($this->module_name . '_add'))
            ->withDescription($this->lang->line($this->module_name . '_add_description'))
            ->withIconUrl(module_resources_url($this->module_name) . 'icon_32.png')
            ->end()
            ->build();
    }

    /**
     * @param $paths
     * @param $sql_basepath
     * @return bool
     */
    private function executeSqls($paths, $sql_basepath)
    {
        $this->db->trans_start();
        foreach ($paths as $path) {
            $contents = file_get_contents($sql_basepath . $path);
            $queries = explode(';', $contents);


            foreach ($queries as $query) {
                if (!trim($query)) {
                    continue;
                }

                $this->db->query($query);
            }
        }
        return $this->db->trans_complete();
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemapURLs()
    {
        $uris = $this->Page_model->getAllSitemapableUris();
        $url_suffix = $this->config->item('url_suffix');

        $sl = $this->Site_language_model->getLanguageByCode('');
        $default_language_code = $sl->code;

        $output = array();
        foreach ($uris as $uri) {
            $output[] = ($default_language_code != $uri->language_code ? $uri->language_code . '/' : '') . $uri->page_uri . $url_suffix;
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest($uri, $uri_component_one, $uri_component_two)
    {
        $this->output->cache($this->config->item('cache_expires'));
        $url_suffix = $this->config->item('url_suffix');

        $page = null;

        if (strlen($uri) == 0) {  // For the default page (no item uri)
            $page = $this->Page_model->getDefaultPageCached(Dispatcher::getSiteLanguage()->code);
        } else {  // For any other document
            $page = $this->Page_model->getPageByUriCached($uri, Dispatcher::getSiteLanguage()->code);
        }

        if ($page === null) {
            return null;
        }

        $this->load->library('Document');
        $this->document->setId($page->page_id)
            ->setTitle($page->page_title)
            ->setContents($page->page_contents)
            ->setDescription($page->page_description)
            ->setKeywords($page->page_keywords)
            ->setRelativeUrl(Dispatcher::getUriPrefix() . $page->page_uri . $url_suffix)
            ->setDefault($page->page_is_default);

        return $this->document;
    }
}