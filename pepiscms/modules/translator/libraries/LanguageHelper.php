<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * @version 1.3
 */
class LanguageHelper
{
    /**
     * Returns lang associative array
     *
     * @param string $path
     * @return array
     */
    public function getLanguageByPath($path)
    {
        if (!file_exists($path)) {
            return array();
        }

        include($path);
        if (!isset($lang)) {
            return array();
        }
        return $lang;
    }

    /**
     * Tells whether a file is writeable
     *
     * @param $module
     * @param $lang_name
     * @param bool $language_file
     * @return bool
     */
    public function isLangFileWritableByModule($module, $lang_name, $language_file = FALSE)
    {
        if (!$language_file) {
            $language_file = $module . '_lang.php';
        }

        if ($module == 'system') {
            // pepiscms translation
            $path = APPPATH . 'language/';

            // codeigniter translation
            if (!file_exists($path . $lang_name . '/' . $language_file)) {
                $path = APPPATH . '../../codeigniter/language/';
            }
        } else {
            $CI = get_instance();
            $path = $CI->load->resolveModuleDirectory($module) . 'language/';
        }


        $path = $path . $lang_name . '/' . $language_file;
        if (!file_exists($path)) return TRUE;

        return is_really_writable($path);
    }

    /**
     * Returns lang array
     *
     * @param string $module
     * @param string $lang_name
     * @param bool|string $language_file
     * @return array
     */
    public function getLanguageByModuleName($module, $lang_name, $language_file = FALSE)
    {
        if (!$language_file) {
            $language_file = $module . '_lang.php';
        }

        if ($module == 'system') {
            // pepiscms translation
            $path = APPPATH . 'language/';

            // codeigniter translation
            if (!file_exists($path . $lang_name . '/' . $language_file)) {
                $path = APPPATH . '../../codeigniter/language/';
            }
        } else {
            $CI = get_instance();
            $path = $CI->load->resolveModuleDirectory($module) . 'language/';
        }


        $path = $path . $lang_name . '/' . $language_file;

        return $this->getLanguageByPath($path);
    }

    /**
     * Returns language keys
     *
     * @param string $path
     * @return array
     */
    public function getKeysByPath($path)
    {
        if (!file_exists($path)) {
            return array();
        }
        include($path);
        if (!isset($lang)) {
            return array();
        }
        return array_keys($lang);
    }

    /**
     * Removes key by specified path
     *
     * @param string $module
     * @param string $key
     * @param string $language_file
     * @return bool
     */
    public function deleteField($module, $key, $language_file)
    {
        $dirs = $this->getModuleLanguages($module, TRUE);
        foreach ($dirs as $dir) {
            $path = $dir . '/' . $language_file;
            if (!file_exists($path)) {
                continue;
            }

            $this->removeKeyByPath($key, $path);
        }

        return TRUE;
    }

    /**
     * Sets key for a given file
     *
     * @param string $module
     * @param string $lang_name
     * @param string $key
     * @param string $value
     * @param string|bool $language_file
     * @return bool
     */
    public function setModuleLanguageField($module, $lang_name, $key, $value, $language_file = FALSE)
    {
        if (!$language_file) {
            $language_file = $module . '_lang.php';
        }
        if ($module == 'system') {
            // pepiscms translation
            $path = APPPATH . 'language/';

            // codeigniter translation
            if (!file_exists($path . $lang_name . '/' . $language_file)) {
                $path = APPPATH . '../../codeigniter/language/';
            }
        } else {
            $CI = get_instance();
            $path = $CI->load->resolveModuleDirectory($module) . 'language/';
        }

        $lang = $this->getLanguageByModuleName($module, $lang_name, $language_file);
        $lang[$key] = $value;

        $path = $path . $lang_name . '/' . $language_file;
        return $this->dumpFile($path, $lang);
    }

    /**
     * Removes key from specified path
     *
     * @param string $key
     * @param string $path
     * @return bool
     */
    public function removeKeyByPath($key, $path)
    {
        $lang = $this->getLanguageByPath($path);
        unset($lang[$key]);

        return $this->dumpFile($path, $lang);
    }

    /**
     * Dumps lang array to the specified file
     *
     * @param $path
     * @param $array
     * @param string $arrayName
     * @return bool
     */
    public function dumpFile($path, $array, $arrayName = 'lang')
    {
        $contents = "<?php if(!defined('BASEPATH')) exit('No direct script access allowed');\n";
        $contents .= "/**\n * Automatically generated language file\n *\n * @date " . date('Y-m-d') . "\n * @file " . basename($path) . "\n */";

        if (count($array) == 0) {
            $contents .= "\n" . '$' . $arrayName . '[] = \'\'; // Protection against empty translations';
        } else {
            ksort($array);


            // Computing the max lengths
            $max_length = 5;
            foreach ($array as $key => $line) {
                if (strlen($key) > $max_length) {
                    $max_length = strlen($key);
                }
            }
            $max_length += 9;

            foreach ($array as $key => $value) {
                if (!$key) {
                    continue;
                }
                $contents .= "\n" . self::ensureStringLength('$' . $arrayName . '[\'' . self::protectString($key) . '\']', $max_length) . ' = \'' . self::protectString($value) . '\';';
            }
        }

        $success = file_put_contents($path, $contents);
        if ($success) {
            // Protection against optcache introduced in PHP5.6
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($path, true);
            }
        }

        return $success;
    }

    /**
     * Formatting function
     *
     * @param string $value
     * @param int $length
     * @return string
     */
    static function ensureStringLength($value, $length = 20)
    {
        $strlen = strlen($value);

        if ($strlen > $length) {
            $value = $value;
            //$value = substr( $value, 0, $length );
        } else {
            $diff = $length - $strlen;

            for ($i = 0; $i < $diff; $i++) {
                $value .= ' ';
            }
        }
        return $value;
    }

    /**
     * Makes sure there are not invalid characters breaking PHP source
     *
     * @param string $value
     * @return string
     */
    private static function protectString($value)
    {
        return str_replace("'", "\\'", $value);
    }

    /**
     * Returns translation keys
     *
     * @param string $module
     * @param bool|array $languages
     * @param bool|string $language_file
     * @return array
     */
    public function getModuleTranslationKeys($module, $languages = FALSE, $language_file = FALSE)
    {
        if (!$language_file) {
            $language_file = $module . '_lang.php';
        }

        if (!$languages) {
            $languages = $this->getModuleLanguages($module);
        }

        if ($module != 'system') {
            $CI = get_instance();
            $path = $CI->load->resolveModuleDirectory($module) . 'language/';
        }

        $keys = array();
        foreach ($languages as $lang_name) {
            if ($module == 'system') {
                // pepiscms translation
                $path = APPPATH . 'language/';

                // codeigniter translation
                if (!file_exists($path . $lang_name . '/' . $language_file)) {
                    $path = APPPATH . '../../codeigniter/language/';
                }
            }
            $language_path = $path . $lang_name . '/' . $language_file;
            $keys = array_merge($keys, $this->getKeysByPath($language_path));
        }

        return array_unique(array_filter($keys));
    }

    /**
     * Return the list of module languages
     *
     * @param string $module
     * @param bool $return_dirs
     * @return array
     */
    public function getModuleLanguages($module, $return_dirs = FALSE)
    {
        if ($module == 'system') {
            $path = APPPATH . 'language/';
        } else {
            $CI = get_instance();
            $path = $CI->load->resolveModuleDirectory($module) . 'language/';
        }

        $language_dirs = glob($path . '*', GLOB_ONLYDIR);
        if ($return_dirs) {
            return $language_dirs;
        }

        $languages = array();
        if (is_array($language_dirs)) {
            foreach ($language_dirs as $lang) {
                $languages[] = basename($lang);
            }
        }
        return $languages;
    }

    /**
     * Return the list of system languages
     *
     * @param $module
     * @param bool $return_dirs
     * @return array
     */
    public function getModuleLanguageFiles($module, $return_dirs = FALSE)
    {
        if ($module == 'system') {
            $path = APPPATH . 'language/';
        } else {
            $CI = get_instance();
            $path = $CI->load->resolveModuleDirectory($module) . 'language/';
        }

        $language_dirs = glob($path . '*/*_lang.php');
        if ($return_dirs) {
            return $language_dirs;
        }

        $languages = array();
        foreach ($language_dirs as &$lang) {
            $languages[] = basename($lang);
        }

        return array_unique($languages);
    }

}