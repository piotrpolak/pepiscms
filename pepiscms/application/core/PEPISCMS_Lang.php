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
 * MY_Lang, provides some extra features
 */
class PEPISCMS_Lang extends CI_Lang
{
    /**
     * Load a language file
     *
     * @param mixed $langfile Language file name
     * @param string $idiom Language name (english, etc.)
     * @param bool $return Whether to return the loaded array of translations
     * @param bool $add_suffix Whether to add suffix to $langfile
     * @param string $alt_path Alternative path to look for the language file
     *
     * @return string[]    Array containing translations, if $return is set to true
     */
    public function load($langfile, $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
    {
        // Loading English translation no matter what (if it exists)
        if ($idiom !== 'english') {
            parent::load($langfile, 'english', $return, $add_suffix, $alt_path);
        }

        return parent::load($langfile, $idiom, $return, $add_suffix, $alt_path);
    }


    /**
     * Returns translated label.
     *
     * @param string $line
     * @param null $default_value
     * @return null|string
     */
    public function line($line = '', $default_value = null)
    {
        // Changed parameter order - might be problematic

        if ($line == '' || !isset($this->language[$line])) {
            //trigger_error('Language field <em>'.$line.'</em> is not defined', E_USER_WARNING);
            if ($default_value === null) {
                return $line;
            }

            return $default_value;
        } else {
            if (class_exists('TranslatorHook') && TranslatorHook::$enableTranslator) {
                return $this->language[$line] . ' <a class="translate" href="admin/translator/translate_field/field-' . $line . '">Translate</a>';
            }

            return $this->language[$line];
        }
    }

    /**
     * This will return the name of the language that can be used for.
     *
     * @return string
     */
    public function getCurrentLanguage()
    {
        $CI = &get_instance();
        $idiom = false;

        if (class_exists('Dispatcher') && ($d_lang = Dispatcher::getSiteLanguage())) {
            if (isset($d_lang->ci_language) && $d_lang->ci_language) {
                $idiom = $d_lang->ci_language;
            }
        } else {
            $CI->load->helper('cookie');
            $idiom = get_cookie('language');
        }

        if (!$idiom) {
            // WORKAROUND for $deft_lang = $CI->config->item( 'language' ); as it did not give correct values
            $deft_lang = '';
            if (file_exists(INSTALLATIONPATH . 'application/config/_pepiscms.php')) {
                include(INSTALLATIONPATH . 'application/config/_pepiscms.php');
                $deft_lang = $config['language'];
                unset($config);
            }
            $idiom = ($deft_lang == '') ? 'english' : $deft_lang;
        }

        return $idiom;
    }

    /**
     * Sets admin language.
     *
     * @param $language
     * @return bool
     */
    public function setAdminLanguage($language)
    {
        // TODO Remove logic from library
        $languages = get_instance()->config->item('enabled_languages');
        if (in_array($language, $languages)) {
            get_instance()->load->helper('cookie');
            set_cookie('language', $language, 10000);
            return true;
        }

        return false;
    }

    /**
     * Returns admin language code.
     *
     * @return string
     */
    public function getAdminLanguageCode()
    {
        // TODO Remove logic from library
        $languages = get_instance()->config->item('languages');
        if (isset($languages[$this->getCurrentLanguage()])) {
            return $languages[$this->getCurrentLanguage()][3];
        }
        return 'en';
    }

    /**
     * Returns the list of enabled languages.
     *
     * @return array
     */
    public function getEnabledAdminLanguages()
    {
        $enabled_language_names = get_instance()->config->item('enabled_languages');
        $languages = get_instance()->config->item('languages');
        $enabled_languages = array();
        foreach ($languages as $key => $language) {
            if (in_array($key, $enabled_language_names)) {
                $enabled_languages[$key] = $language;
            }
        }

        return $enabled_languages;
    }

    /**
     * Load a language file
     *
     * @param string $langfile
     * @param string $idiom
     * @param bool $return
     * @param bool|string $module_name
     * @return bool
     */
    public function loadForModule($langfile = '', $idiom = '', $return = false, $module_name = false)
    {
        // TODO Remove this hack
        $langfile = str_replace('.php', '', str_replace('_lang.', '', $langfile)) . '_lang.php';

        if (!$module_name && in_array($langfile, $this->is_loaded, true)) {
            return false;
        }

        $CI = &get_instance();

        if (!$idiom) {
            $idiom = $this->getCurrentLanguage();
        }

        if ($idiom == '') {
            $deft_lang = $CI->config->item('language');
            $idiom = ($deft_lang == '') ? 'english' : $deft_lang;
        }

        $something_loaded = false;


        // For modules
        if ($module_name) {
            $module_path = $CI->load->resolveModuleDirectory($module_name);


            // Loading English translation no matter what (if it exists)
            if ($idiom != 'english' && file_exists($module_path . 'language/english/' . $langfile)) {
                include($module_path . 'language/english/' . $langfile);
                $something_loaded = true;
            }

            // Attempting to load the final language
            if (file_exists($module_path . 'language/' . $idiom . '/' . $langfile)) {
                include($module_path . 'language/' . $idiom . '/' . $langfile);
                $something_loaded = true;
            }
        }


        /*
         * For non modules and for builtin modules
         */
        // Determine where the language file is and load it
        if (!$module_name || !$something_loaded) {
            // Loading English translation no matter what (if it exists)
            if ($idiom != 'english' && file_exists(APPPATH . 'language/english/' . $langfile)) {
                include(APPPATH . 'language/english/' . $langfile);
                $something_loaded = true;
            }

            if (file_exists(APPPATH . 'language/' . $idiom . '/' . $langfile)) {
                include(APPPATH . 'language/' . $idiom . '/' . $langfile);
                $something_loaded = true;
            } else {
                // Loading English translation no matter what (if it exists)
                if ($idiom != 'english' && file_exists(BASEPATH . 'language/english/' . $langfile)) {
                    include(BASEPATH . 'language/english/' . $langfile);
                    $something_loaded = true;
                }

                // Attempting to load the final language
                if (file_exists(BASEPATH . 'language/' . $idiom . '/' . $langfile)) {
                    include(BASEPATH . 'language/' . $idiom . '/' . $langfile);
                    $something_loaded = true;
                }
            }
        }


        if (!$something_loaded) {
            if ($module_name) {
                //show_error( 'Unable to load the requested module ('.$module_name.') language file: language/' . $idiom . '/' . $langfile );
            } else {
                show_error('Unable to load the requested language file: language/' . $idiom . '/' . $langfile);
            }
            return false;
        }

        if (!isset($lang)) {
            log_message('error', 'Language file contains no data: language/' . $idiom . '/' . $langfile);
            return false;
        }

        if ($return == true) {
            return $lang;
        }

        $this->is_loaded[] = $langfile;
        $this->language = array_merge($this->language, $lang);
        unset($lang);

        log_message('debug', 'Language file loaded: language/' . $idiom . '/' . $langfile);
        return true;
    }
}
