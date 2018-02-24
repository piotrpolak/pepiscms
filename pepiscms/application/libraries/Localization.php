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
 * Localization helper
 * Some handy methods to display translated versions of the object fields
 *
 * @since 0.1.5
 */
class Localization extends ContainerAware
{

    /**
     * Returns localized label based on the language
     *
     * @param Object $object
     * @param string $field
     * @param Language|bool $language
     * @return string|bool
     */
    public static function localize($object, $field, $language = FALSE)
    {
        if (!$language) {
            $language = Dispatcher::getSiteLanguage();
        }

        $field .= self::getFieldLanguageSuffix($language);

        if (isset($object->$field)) {
            return $object->$field;
        }

        return FALSE;
    }

    /**
     * Returns localized label based on the language, HTML protected
     *
     * @param Object $object
     * @param string $field
     * @param Language|bool $language
     * @return string|bool
     */
    public static function localizeHTML($object, $field, $language = FALSE)
    {
        $line = self::localize($object, $field, $language);
        if ($line) {
            return str_replace('"', '&quot;', $line);
        }

        return FALSE;
    }

    /**
     * Returns field's language suffix for a given language
     *
     * @param string|boolean $language
     * @return string
     */
    public static function getFieldLanguageSuffix($language = FALSE)
    {
        if (!$language) {
            $language = Dispatcher::getSiteLanguage();
        }

        if ($language->is_default) {
            return '';
        }
        return '_' . $language->code;
    }

    /**
     * Returns URI language prefix
     *
     * @param Language|bool $language
     * @return string
     */
    public static function getUriPrefix($language = FALSE)
    {
        if (!$language) {
            $language = Dispatcher::getSiteLanguage();
        }

        if ($language->is_default) {
            return '';
        }
        return $language->code . '/';
    }

    /**
     * Localizes definition by adding extra fields
     * It takes the definition by reference
     *
     * @param array $definition
     * @param TranslateableInterface $object
     */
    public function localizeDefinition(&$definition, TranslateableInterface $object)
    {
        $this->load->model('Site_language_model');
        $languages = $this->Site_language_model->getLanguages();
        if (count($languages) == 1)
            return;

        // For non default languages
        foreach ($languages as $language) {
            if ($language->is_default) {
                continue;
            }
            $suffix = '_' . $language->code;

            foreach ($object->getTranslateableFieldNames() as $field) {
                if (isset($definition[$field])) {
                    $definition[$field . $suffix] = $definition[$field];
                    $definition[$field . $suffix]['show_in_grid'] = FALSE;
                    $definition[$field . $suffix]['label'] .= ' ' . strtoupper($language->code);
                    if (isset($definition[$field . $suffix]['field']) && $definition[$field . $suffix]['field']) {
                        $definition[$field . $suffix]['field'] .= $suffix;
                    }
                    $object->addAcceptedPostField($field . $suffix);

                    $definition[$field . $suffix]['input_group'] = $language->label;
                } elseif (is_callable(array($object, 'addAcceptedPostField'))) {
                    $object->addAcceptedPostField($field . $suffix);
                }
            }
        }

        // For the default language
        foreach ($object->getTranslateableFieldNames() as $field) {
            if (isset($definition[$field])) {
                $definition[$field]['label'] .= ' ' . strtoupper($languages[0]->code);
                $definition[$field]['input_group'] = $languages[0]->label;
            }
        }
    }
}
