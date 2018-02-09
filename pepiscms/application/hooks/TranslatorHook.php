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
 * Class TranslatorHook
 *
 * @since 0.1.0
 */
class TranslatorHook
{
    public static $enableTranslator = FALSE;

    public function filterContent()
    {
        $CI = &get_instance();
        $output = $CI->output->get_output();

        $match_count = preg_match_all("/\<translate\>([a-zA-Z1-9 '\.,\"\(\)\-]*)\<\/translate\>/i", $output, $matches);
        for ($i = 0; $i < $match_count; $i++) {
            $output = str_replace($matches[0][$i], $matches[1][$i] . ' <a class="translate" href="admin/translator/translate_field/string-' . urlencode(base64_encode($matches[1][$i])) . '">Translate</a>', $output);
        }

        echo $output;
    }

    public function enableTranslator()
    {
        self::$enableTranslator = TRUE;
    }

}
