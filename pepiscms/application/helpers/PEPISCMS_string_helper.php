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

if (!function_exists('niceuri')) {

    /**
     * Generates a URL friendly string that can be used as URL slug (URL id). Supports Cyrillic as well.
     *
     * @param string $name
     * @return string
     */
    function niceuri($name)
    {
        //form_prep
        if (strlen($name) == 0) {
            return '';
        }

        //$name = strtolower( $name );
        $name = mb_strtolower($name, 'UTF-8');

        // Germanic
        $diacritic_search = 'á í ó ú ý ä ö ü';
        $diacritic_replace = 'a i o u y a o u';

        // DO NOT REMOVE SPACES AS CONCATENATING STRING!!
        // Polish
        $diacritic_search .= ' ą ć ę ł ń ó ś ź ż';
        $diacritic_replace .= ' a c e l n o s z z';

        // Romanian
        $diacritic_search .= ' ă â î ș ş ț ţ';
        $diacritic_replace .= ' a a i s s t t';

        // Spanish
        $diacritic_search .= ' ñ';
        $diacritic_replace .= ' n';

        // Italian
        $diacritic_search .= ' à è é ì ò ó ó ù';
        $diacritic_replace .= ' a e e i o o o u';

        // Slovak, Chech
        $diacritic_search .= ' á é í ĺ ó ŕ ú ý č ď ě ľ ň ř š ť ž ů';
        $diacritic_replace .= ' a r i i o r u y c d e i n r s t z u';

        // Russian
        $diacritic_search .= ' а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я';
        $diacritic_replace .= ' a b v g d e e j z i y k l m n o p r s t u f h c ch sh shch - y - e ju ja';

        $diacritic_search = explode(' ', $diacritic_search);
        $diacritic_replace = explode(' ', $diacritic_replace);

        $name = str_replace($diacritic_search, $diacritic_replace, $name);

        // Remove invisible characters, we don't want the words to be broken because of UTF
        $name = preg_replace('/[^(\x20-\x7F)]*/', '', $name);

        // Use the buildin function, just in any case
        get_instance()->load->helper('text');
        $name = convert_accented_characters($name);

        // Remove non printable characters
        $name = preg_replace('/[^A-Za-z0-9]/', '-', $name);

        // Remove multiple spaces
        $name = preg_replace('/[\-]+/', '-', $name);

        // Trim
        if ($name[0] == '-') {
            $name = substr($name, 1);
        }
        if ($name{strlen($name) - 1} == '-') {
            $name = substr($name, 0, strlen($name) - 1);
        }

        return $name;
    }
}

if (!function_exists('shortname')) {

    /**
     * Generates a shortened string, inserts ... in the middle of the shortened value.
     *
     * @param string $name
     * @param int $maxlength
     * @param string $separator
     * @return string
     */
    function shortname($name, $maxlength = 60, $separator = '...')
    {
        // shortname v2.0
        $seplen = strlen($separator);
        if (strlen($name) > $maxlength) {
            $name = substr($name, 0, ($maxlength - $seplen) / 2) . $separator . substr($name, strlen($name) - ($maxlength - $seplen) / 2, strlen($name));
        }
        return $name;
    }
}

if (!function_exists('remove_separators')) {

    /**
     * Returns string with all word separators removed (space, dash, minus, plus).
     *
     * @param string $str
     * @return string
     */
    function remove_separators($str)
    {
        return str_replace(array(' ', '-', '+', '_'), '', $str);
    }
}
