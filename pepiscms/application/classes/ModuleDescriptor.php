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
 * Abstract class for building module descriptors
 *
 * @since 0.2.0
 */
abstract class ModuleDescriptor implements ModuleDescriptableInterface
{
    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return 'Untitled Module';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInMenu()
    {
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
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
    public function getAdminUtilitiesElements($language)
    {
        // NOT IMPLEMENTED YET
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemapURLs()
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigVariables()
    {
        return FALSE;
    }

}