<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

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
        $this->load->moduleLanguage($module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return $this->lang->line('translator_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return $this->lang->line('translator_module_description');
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
        return true;
    }
}
