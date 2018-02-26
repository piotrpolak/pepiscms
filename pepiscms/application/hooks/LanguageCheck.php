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
 * Class LanguageCheck
 */
class LanguageCheck
{
    public function docheck($params = null)
    {
        /* Is there any cookie? */
        if (isset($_COOKIE['language'])) {
            /* CFG must be global */
            global $CFG;

            /* Getting the list of languages */
            $languages = $CFG->item('languages');

            /* Checking if a language exists */
            if (isset($languages[$_COOKIE['language']])) {
                /* Seting the language item */
                $CFG->set_item('language', $languages[$_COOKIE['language']][0]);
            }
        }
    }
}
