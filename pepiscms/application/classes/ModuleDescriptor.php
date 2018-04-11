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
 * Abstract class for building module descriptors
 *
 * @since 0.2.0
 */
abstract class ModuleDescriptor extends ContainerAware implements ModuleDescriptableInterface
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
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInUtilities()
    {
        return false;
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
    public function getAdminUtilitiesElements($language)
    {
        // NOT IMPLEMENTED YET
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemapURLs()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigVariables()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest($uri, $uri_component_one, $uri_component_two)
    {
        return null;
    }
}
