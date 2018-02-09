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
 * Utility for generating and parsing PepisCMS specific XML backup files
 *
 * @since 0.1
 */
class Backup
{

    private $CI;
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
     *
     * @param array|null $params
     */
    function __construct($params = NULL)
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Page_model');
        $this->CI->load->model('Menu_model');
        $this->CI->load->model('Site_language_model');
    }

    /**
     * Restores backup from file
     *
     * @param string $file_path
     * @param int $user_id
     * @return bool
     * @throws Exception
     */
    function restore($file_path, $user_id)
    {
        try {
            $sxe = @new SimpleXMLElement($file_path, NULL, TRUE);
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

            $this->CI->db->trans_start();

            // Truncating tables
            $this->CI->Page_model->doBackupPrepare();
            $this->CI->Menu_model->doBackupPrepare();
            $this->CI->Site_language_model->doBackupPrepare();


            // Restoring languages
            if (isset($sxe->sitelanguages->item)) {
                $this->CI->Site_language_model->doBackupRestore($sxe->sitelanguages->item);
            }

            // Restoring pages
            if (isset($sxe->pages->item)) {
                $this->CI->Page_model->doBackupRestore($sxe->pages->item, $user_id);
            }


            // Restoring menu
            if (isset($sxe->menu->item)) {
                $this->CI->Menu_model->doBackupRestore($sxe->menu->item);
            }

            $this->CI->db->trans_complete();

            return !($this->CI->db->trans_status() === FALSE);
        }
    }

    /**
     * Creates backup and returns XML output
     *
     * @return string
     */
    function create()
    {
        $this->CI->load->helper('xml');

        $backup = '';

        $backup .= '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
        $backup .= '<pepis_cms_backup version="' . $this->current_version . '">' . "\r\n";

        $backup_items = array();
        $backup_items[] = array(
            'pages',
            $this->CI->Page_model->doBackupProjection(),
            array('page_id', 'page_uri', 'page_title', 'page_description', 'page_keywords', 'page_contents', 'page_is_default', 'page_is_displayed_in_sitemap', 'language_code')
        );
        $backup_items[] = array(
            'menu',
            $this->CI->Menu_model->doBackupProjection(),
            array('item_id', 'item_order', 'parent_item_id', 'item_name', 'language_code', 'item_url', 'page_id')
        );
        $backup_items[] = array(
            'sitelanguages',
            $this->CI->Site_language_model->doBackupProjection(),
            array('code', 'label', 'is_default', 'ci_language')
        );


        /*
         * Foreach element
         */
        foreach ($backup_items as $backup_item) {
            $item_name = &$backup_item[0];
            $item_elements = &$backup_item[1];

            if (count($item_elements) > 0) // If there are pages
            {

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

/**
 * Utility class to "upgrade" old backup files to the most current version.
 * Progressive upgrades are done in a row.
 *
 * @since 0.1.5
 */
class BackupCompatibilityTransformationUtility
{

    /**
     * Upgrades object backup object to desired format
     *
     * @param object $sxe
     * @param float $from_version
     * @param array $supported_versions
     */
    public static function transform(&$sxe, $from_version, $supported_versions)
    {
        $transformation_sequence = array();
        foreach ($supported_versions as $supported_version) {
            if (count($transformation_sequence) > 0 || $from_version == $supported_version) {
                $transformation_sequence[] = $supported_version;
            }
        }

        $count = count($transformation_sequence);
        $i = 0;

        while (true) {
            if (++$i >= $count) {
                break; // Important, do not mess
            }

            $to_version = $transformation_sequence[$i];

            self::doTransform($sxe, $from_version, $to_version);
            $from_version = $to_version;
        }
    }

    protected static function doTransform(&$sxe, $from_version, $to_version)
    {
        if ($from_version == '1.2' && $to_version == '1.3') {
            $menu2uri_map = array();
            if (isset($sxe->menu2uri->item)) {
                foreach ($sxe->menu2uri->item as $item) {
                    $key = '' . $item->item_id;
                    if (!$key) {
                        continue;
                    }

                    $menu2uri_map[$key] = '' . $item->item_uri;
                }
            }

            if (isset($sxe->page2menu->item)) {
                foreach ($sxe->page2menu->item as $item) {
                    $key = '' . $item->item_id;
                    if (!$key) {
                        continue;
                    }

                    $page2menu_map[$key] = '' . $item->page_id;
                }
            }

            if (isset($sxe->menu->item)) {
                foreach ($sxe->menu->item as $item) {
                    $key = '' . $item->item_id;
                    $item->item_url = isset($menu2uri_map[$key]) ? '' . $menu2uri_map[$key] : NULL;
                    $item->page_id = isset($page2menu_map[$key]) ? '' . $page2menu_map[$key] : NULL;
                }
            }

            return TRUE;
        } elseif ($from_version == '1.1' && $to_version == '1.2') {
            if (isset($sxe->pages->item)) {
                foreach ($sxe->pages->item as $item) {
                    $item->language_code = 'en';
                }
            }
            if (isset($sxe->menu->item)) {
                foreach ($sxe->menu->item as $item) {
                    $item->language_code = 'en';
                }
            }


            $sitelanguages = $sxe->addChild('sitelanguages');
            $item = $sitelanguages->addChild('item');

            $item->addChild('code', 'en');
            $item->addChild('label', 'English');
            $item->addChild('is_default', '1');
            $item->addChild('ci_language', 'english');


            return TRUE;
        } elseif ($from_version == '1.0' && $to_version == '1.1') {
            if (isset($sxe->pages->item)) {
                foreach ($sxe->pages->item as $item) {
                    $item->page_is_displayed_in_sitemap = '1';

                    if ($item->page_is_default == 'Y') {
                        $item->page_is_default = 1;
                    } else {
                        $item->page_is_default = 0;
                    }
                }
            }

            return TRUE;
        }

        return FALSE;
    }
}
