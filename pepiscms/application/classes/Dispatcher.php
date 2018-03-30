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
 * Frontend dispatcher controller
 *
 * @since 0.1.0
 */
class Dispatcher
{
    private static $site_language = null;
    private static $uri_prefix = null;

    /**
     * Returns object representing site language
     *
     * @return Object
     */
    public static function getSiteLanguage()
    {
        return self::$site_language;
    }

    /**
     * Sets site language
     * @param object $site_language
     * @return void
     *
     * @example: Dispatcher::setSiteLanguage((object) array('code' => 'pl', 'name' => 'polish', 'label' => 'polish', 'is_default' => true));
     */
    public static function setSiteLanguage($site_language)
    {
        self::$site_language = $site_language;
    }

    /**
     * Returns URI prefix
     *
     * @return String
     */
    public static function getUriPrefix()
    {
        if (self::$uri_prefix === null) {
            self::$uri_prefix = self::$site_language->is_default ? '' : self::$site_language->code . '/';
        }

        return self::$uri_prefix;
    }
}
