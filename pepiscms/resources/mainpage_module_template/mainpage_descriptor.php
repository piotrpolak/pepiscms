<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2017, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Class MainpageDescriptor
 */
class MainpageDescriptor extends ModuleDescriptor
{
    /**
     * Cached variable
     *
     * @var String
     */
    private $module_name;

    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return 'Main page';
    }

    /**
     * Executed on installation
     */
    public function onInstall()
    {
        $path = get_instance()->load->resolveModuleDirectory($this->module_name, FALSE) . '/resources/install.sql';
        if (!file_exists($path)) {
            return FALSE;
        }
        get_instance()->db->query(file_get_contents($path));
        return TRUE;
    }

    /**
     * Executed on uninstall
     */
    public function onUninstall()
    {
        $path = get_instance()->load->resolveModuleDirectory($this->module_name, FALSE) . '/resources/uninstall.sql';
        if (!file_exists($path)) {
            return FALSE;
        }
        get_instance()->db->query(file_get_contents($path));
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return FALSE;
    }
}
