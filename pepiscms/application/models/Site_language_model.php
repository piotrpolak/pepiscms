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
 * Site language model
 *
 * @since 0.1.2
 */
class Site_language_model extends Generic_model
{
    /**
     * Cache
     * @var Array
     */
    private $language_codes = null;

    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setIdFieldName('code');
        $this->setTable($this->config->item('database_table_site_languages'));
        $this->setAcceptedPostFields(array('code', 'label', 'is_default', 'ci_language'));

        $this->load->library('Cachedobjectmanager');
    }

    /**
     * Returns a language by code
     *
     * @param $code
     * @return stdClass
     */
    public function getLanguageByCode($code)
    {
        if (!$this->db || !$this->db->table_exists($this->getTable())) {
            // failsafe - for intranet applications having no database table
            $obj = new stdClass();
            $obj->is_default = 1;
            $obj->label = 'English';
            $obj->code = 'en';
            $obj->ci_language = 'english';
            return $obj;
        }

        $this->db->select('*');
        if (strlen($code) == 2) {
            $this->db->where('code', $code);
        } else {
            $this->db->where('is_default', 1);
        }

        return $this->db->get($this->getTable())->row();
    }

    /**
     * Returns a language by code, cached
     *
     * @param $language_code
     * @return object
     */
    public function getLanguageByCodeCached($language_code)
    {
        $object_name = 'language_' . $language_code;
        return $this->cachedobjectmanager->get($object_name, 'pages', 3600 * 24, function () use ($language_code) {
            return $this->getLanguageByCode($language_code);
        });
    }

    /**
     * Returns a list of translatable fields, the first element is the default implicite language
     *
     * @return array
     */
    public function getLanguageCodes()
    {
        // When there is no cache initialized
        if ($this->language_codes === null) {
            // Initialize cache
            $this->language_codes = array();

            // Read database values
            $languages = $this->db->select('code')
                ->order_by('is_default DESC, code')
                ->get($this->getTable())
                ->result();

            // Populate cache array
            foreach ($languages as $l) {
                $this->language_codes[] = $l->code;
            }
        }

        return $this->language_codes;
    }

    /**
     * Checks whether a language code is taken
     *
     * @param $code
     * @return bool
     */
    public function isCodeTaken($code)
    {
        return $this->db->where('code', $code)->count_all_results($this->getTable()) > 0;
    }

    /**
     * Returns array of all languages, default first
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->db->select('*')
            ->order_by('is_default DESC, code')
            ->get($this->getTable())
            ->result();
    }

    /**
     * Makes sure at least one language is default
     * When no default language is found, the function picks one language to be default
     *
     * @return bool
     */
    private function makeSureThereIsOneLanguageDefault()
    {
        // When there are no default languages
        if ($this->db->where('is_default', 1)->count_all_results($this->getTable()) == 0) {
            // Pick one language
            $row = $this->db->select('code')
                ->from($this->getTable())
                ->limit(1)
                ->get()
                ->row();

            if (!$row) {
                return false;
            }

            // Make one language default
            return $this->db->set('is_default', 1)
                ->where('code', $row->code)
                ->update($this->getTable());
        }

        return false;
    }

    /**
     * Deletes menu item
     *
     * @param mixed $id
     * @return mixed
     */
    public function deleteById($id)
    {
        $this->db->query('ALTER TABLE ' . $this->config->item('database_table_menu') . ' DISABLE KEYS');
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Delete menu, pages, then element itself within the transaction
        $this->db->trans_start();
        $this->db->where('language_code', $id)->delete($this->config->item('database_table_menu'));
        $this->db->where('language_code', $id)->delete($this->config->item('database_table_pages'));
        parent::deleteById($id);
        $this->db->trans_complete();

        $this->makeSureThereIsOneLanguageDefault();

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        $this->db->query('ALTER TABLE ' . $this->config->item('database_table_menu') . ' ENABLE KEYS');

        return $this->db->trans_status();
    }

    /**
     * Saves by id
     *
     * @param type $id
     * @param type $data
     * @return type
     */
    public function saveById($id, $data)
    {
        foreach ($data as $_param_name => $_param_value) {
            if (!in_array($_param_name, $this->getAcceptedPostFields())) {
                continue;
            }

            $$_param_name = $_param_value;
            $this->db->set($_param_name, $_param_value);
        }

        $this->db->trans_start();

        if ($id !== false) {
            $this->db->where('code', $id)->update($this->getTable());

            if ($data['code'] != $id) { // When code is changed
                $this->db->where('language_code', $id)
                    ->set('language_code', $data['code'])
                    ->update($this->config->item('database_table_menu'));

                $this->db->where('language_code', $id)
                    ->set('language_code', $data['code'])
                    ->update($this->config->item('database_table_pages'));
            }
        } else {
            $this->db->insert($this->getTable());
            $id = $data['code'];
        }

        if ($data['is_default']) {
            $this->db->where('code != ', $id)
                ->set('is_default', 0)
                ->update($this->getTable());
        }

        $this->db->trans_complete();

        $this->makeSureThereIsOneLanguageDefault();

        return $this->db->trans_status();
    }
}
