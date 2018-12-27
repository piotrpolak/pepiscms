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
 * Menu model
 *
 * @since 0.1.0
 */
class Page_model extends Generic_model
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable($this->config->item('database_table_pages'));
        $this->setIdFieldName('page_id');
        $this->setAcceptedPostFields(array(
            'page_uri',
            'page_title',
            'page_description',
            'page_keywords',
            'page_contents',
            'page_image_path',
            'user_id_modified',
            'timestamp_modified',
            'user_id_created',
            'timestamp_created',
            'page_is_displayed_in_sitemap',
            'language_code'
        ));
    }

    /**
     * Sets the page as default for the given language
     *
     * @param $page_id
     * @param string $language_code
     * @return mixed
     */
    public function setDefault($page_id, $language_code = 'en')
    {
        $this->db->set('page_is_default', 0)
            ->where('page_is_default', 1)
            ->where('language_code', $language_code)
            ->update($this->config->item('database_table_pages'));

        return $this->db->set('page_is_default', 1)
            ->where('page_id', $page_id)
            ->update($this->config->item('database_table_pages'));
    }

    /**
     * Returns a page by URI
     *
     * @param $page_uri
     * @param string $language_code
     * @return mixed
     */
    public function getPageByUri($page_uri, $language_code = 'en')
    {
        return $this->db->select('*')
            ->where($this->config->item('database_table_pages') . '.page_uri', $page_uri)
            ->where('language_code', $language_code)
            ->get($this->config->item('database_table_pages'))
            ->row();
    }

    /**
     * Returns page by URI but cached
     *
     * @param $page_uri
     * @param string $language_code
     * @return mixed
     */
    public function getPageByUriCached($page_uri, $language_code = 'en')
    {
        $this->load->library('Cachedobjectmanager');

        $object_name = 'uri_page_' . md5($page_uri) . '_' . $language_code;
        $object = $this->cachedobjectmanager->getObject($object_name, 3600 * 24, 'pages');
        if ($object === false) {
            $object = $this->getPageByUri($page_uri, $language_code);
            $this->cachedobjectmanager->setObject($object_name, $object, 'pages');
        }

        return $object;
    }

    /**
     * Returns pages that are not attached to menu
     *
     * @param string $language_code
     * @return mixed
     */
    public function getNoMenuPages($language_code = 'en')
    {
        // * changed to a list, eliminating page contents
        return $this->db->select('page_id, page_uri, page_title, page_description, page_keywords, page_image_path, timestamp_created, timestamp_modified, page_is_default, page_is_displayed_in_sitemap')
            ->where($this->config->item('database_table_pages') . '.page_id NOT IN (SELECT ' . $this->config->item('database_table_menu') . '.page_id FROM ' . $this->config->item('database_table_menu') . ' WHERE page_id IS NOT NULL)')
            ->where('language_code', $language_code)
            ->order_by('page_uri')
            ->from($this->config->item('database_table_pages'))
            ->get()
            ->result();
    }

    /**
     * Returns a default page for the given language code
     *
     * @param string $language_code
     * @return mixed
     */
    public function getDefaultPage($language_code = 'en')
    {
        return $this->db->select('*')
            ->where('page_is_default', 1)
            ->where('language_code', $language_code)
            ->from($this->config->item('database_table_pages'))
            ->get()
            ->row();
    }

    /**
     * Returns a default page for the given language code, cached
     *
     * @param string $language_code
     * @return mixed
     */
    public function getDefaultPageCached($language_code = 'en')
    {
        $this->load->library('Cachedobjectmanager');

        $object_name = 'default_page_' . $language_code;
        $object = $this->cachedobjectmanager->getObject($object_name, 3600 * 24, 'pages');
        if ($object === false) {
            $object = $this->getDefaultPage($language_code);
            $this->cachedobjectmanager->setObject($object_name, $object, 'pages');
        }

        return $object;
    }

    /**
     * Tells whenever the given URI is taken
     *
     * @param $page_uri
     * @param string $language_code
     * @return bool
     */
    public function isUriTaken($page_uri, $language_code = 'en')
    {
        $this->db->where('page_uri', $page_uri)
            ->where('language_code', $language_code)
            ->from($this->config->item('database_table_pages'));

        return $this->db->count_all_results() > 0;
    }

    /**
     * Returns the list of all URIs but for the default one
     * Used by site exporter
     *
     * @param bool $with_title
     * @return array
     */
    public function getAllUris($with_title = false)
    {
        // For sitemap
        $select_what = 'page_uri, language_code';
        if ($with_title) {
            $select_what .= ', page_title';
        }

        return $this->db->select($select_what)
            ->order_by('page_uri')
            ->where('page_is_default != \'1\'')// TODO
            ->get($this->config->item('database_table_pages'))
            ->result();
    }

    /* Returns the list of all sitemapable URIs but for the default one
     *
     * @param bool $with_title
     * @return array
     */
    public function getAllSitemapableUris($with_title = false)
    {
        // For sitemap
        $select_what = 'page_uri, language_code';
        if ($with_title) {
            $select_what .= ', page_title';
        }

        return $this->db->select($select_what)
            ->order_by('page_uri')
            ->where('page_is_default != \'1\'')// TODO
            ->where('page_is_displayed_in_sitemap = \'1\'')// TODO
            ->get($this->config->item('database_table_pages'))
            ->result();
    }

    /**
     * Cleans HTML cache and returns the number of deleted files
     *
     * @return array|bool
     * @throws Exception
     */
    public function clean_pages_cache()
    {
        $return = array('size' => 0, 'count' => 0);

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }

        $pages_cache_path = ($this->config->item('pages_cache_path') == '') ? 'pages/' : $this->config->item('pages_cache_path');
        $cache_path .= $pages_cache_path;

        if (!file_exists($cache_path) || !is_dir($cache_path)) {
            return false;
        }

        $dir = @opendir($cache_path);

        if (!$dir) {
            throw new Exception('Unable to open the cache directory');
        } else {
            while ($file = readdir($dir)) {
                if (is_file($cache_path . $file)) {
                    $file_size = filesize($cache_path . $file);
                    if (@unlink($cache_path . $file)) {
                        $return['size'] += $file_size;
                        $return['count']++;
                    }
                }
            }
        }
        @closedir($dir);

        return $return;
    }
}
