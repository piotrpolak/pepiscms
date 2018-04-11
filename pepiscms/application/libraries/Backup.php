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
 * Utility for generating and parsing PepisCMS specific XML backup files
 *
 * @since 0.1.0
 */
class Backup extends ContainerAware
{
    private $current_version = '1.3';
    private $supported_name = 'pepis_cms_backup';

    /**
     * Returns an array containing the list of supported versions
     *
     * @return array
     */
    public static function getSupportedVersions()
    {
        return array('1.0', '1.1', '1.2', '1.3');
    }

    /**
     * Backup constructor.
     */
    public function __construct()
    {
        $this->load->moduleModel('pages', 'Page_model');
        $this->load->model('Menu_model');
        $this->load->model('Site_language_model');
    }

    /**
     * Restores backup from file
     *
     * @param string $file_path
     * @param int $user_id
     * @return bool
     * @throws Exception
     */
    public function restore($file_path, $user_id)
    {
        try {
            $sxe = @new SimpleXMLElement($file_path, null, true);
        } catch (Exception $exception) {
            throw new Exception('not_a_valid_xml_document');
        }

        $attributes = $sxe->attributes();

        if ($this->supported_name != $sxe->getName() || !in_array($attributes->version, Backup::getSupportedVersions())) {
            throw new Exception('not_a_valid_backup_file');
        } else {
            if ($attributes->version != $this->current_version) {
                BackupCompatibilityTransformationUtility::transform($sxe, $attributes->version, Backup::getSupportedVersions());
            }

            $this->db->trans_start();

            // Truncating tables
            $this->Page_model->doBackupPrepare();
            $this->Menu_model->doBackupPrepare();
            $this->Site_language_model->doBackupPrepare();


            // Restoring languages
            if (isset($sxe->sitelanguages->item)) {
                $this->Site_language_model->doBackupRestore($sxe->sitelanguages->item);
            }

            // Restoring pages
            if (isset($sxe->pages->item)) {
                $this->Page_model->doBackupRestore($sxe->pages->item, $user_id);
            }


            // Restoring menu
            if (isset($sxe->menu->item)) {
                $this->Menu_model->doBackupRestore($sxe->menu->item);
            }

            $this->db->trans_complete();

            return !($this->db->trans_status() === false);
        }
    }

    /**
     * Creates backup and returns XML output
     *
     * @return string
     */
    public function create()
    {
        $this->load->helper('xml');

        $backup = '';

        $backup .= '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
        $backup .= '<pepis_cms_backup version="' . $this->current_version . '">' . "\r\n";

        $backup_items = array();
        $backup_items[] = array(
            'pages',
            $this->Page_model->doBackupProjection(),
            array('page_id', 'page_uri', 'page_title', 'page_description', 'page_keywords', 'page_contents', 'page_is_default', 'page_is_displayed_in_sitemap', 'language_code')
        );
        $backup_items[] = array(
            'menu',
            $this->Menu_model->doBackupProjection(),
            array('item_id', 'item_order', 'parent_item_id', 'item_name', 'language_code', 'item_url', 'page_id')
        );
        $backup_items[] = array(
            'sitelanguages',
            $this->Site_language_model->doBackupProjection(),
            array('code', 'label', 'is_default', 'ci_language')
        );


        /*
         * Foreach element
         */
        foreach ($backup_items as $backup_item) {
            $item_name = &$backup_item[0];
            $item_elements = &$backup_item[1];

            if (count($item_elements) > 0) { // If there are pages
                $backup .= "\t<$item_name>\r\n";

                $properties = $backup_item[2];

                foreach ($item_elements as $element) {
                    $backup .= "\t\t<item>\r\n";

                    $backup .= reflect2xml($element, $properties, "\t\t\t");

                    $backup .= "\t\t</item>\r\n";
                }

                $backup .= "\t</$item_name>\r\n";
            }
        }

        $backup .= '</pepis_cms_backup>' . "\r\n";

        return $backup;
    }
}
