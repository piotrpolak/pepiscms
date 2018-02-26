<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

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
        $path = $this->load->resolveModuleDirectory($this->module_name, false) . '/resources/install.sql';
        if (!file_exists($path)) {
            return false;
        }
        $this->db->query(file_get_contents($path));
        return true;
    }

    /**
     * Executed on uninstall
     */
    public function onUninstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, false) . '/resources/uninstall.sql';
        if (!file_exists($path)) {
            return false;
        }
        $this->db->query(file_get_contents($path));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return false;
    }
}
