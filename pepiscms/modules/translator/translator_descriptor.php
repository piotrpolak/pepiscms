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
 * Class TranslatorDescriptor
 */
class TranslatorDescriptor extends ModuleDescriptor
{
    /**
     * TranslatorDescriptor constructor.
     */
    public function __construct()
    {
        $module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        get_instance()->load->moduleLanguage($module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return get_instance()->lang->line('translator_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return get_instance()->lang->line('translator_module_description');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.2.1';
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return TRUE;
    }
}
