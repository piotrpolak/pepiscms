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
 * Class SqlconsoleDescriptor
 */
class SqlconsoleDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * SqlconsoleDescriptor constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        $this->load->moduleLanguage($this->module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return $this->lang->line($this->module_name . '_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return $this->lang->line($this->module_name . '_module_description');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.0.1';
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return TRUE;
    }
}
