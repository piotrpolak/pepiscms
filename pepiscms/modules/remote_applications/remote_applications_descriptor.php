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
 * Class Remote_applicationsDescriptor
 */
class Remote_applicationsDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * Remote_applicationsDescriptor constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        get_instance()->load->moduleLanguage($this->module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        $module_name = 'remote_applications';
        get_instance()->load->moduleLanguage($module_name);
        return get_instance()->lang->line($module_name . '_title');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        get_instance()->load->moduleLanguage($this->module_name);
        $description_label = $this->module_name . '_module_description';
        $description = get_instance()->lang->line($this->module_name . '_module_description');
        if ($description == $description_label) {
            return '';
        }

        return $description;
    }

}
