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