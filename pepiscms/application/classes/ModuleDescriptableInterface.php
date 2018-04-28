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
 * Interface used for describing a module
 *
 * @since 0.2.0
 */
interface ModuleDescriptableInterface
{
    /**
     * Returns module name
     *
     * @param $language
     * @return string
     */
    public function getName($language);

    /**
     * Returns module description
     *
     * @param $language
     * @return string
     */
    public function getDescription($language);

    /**
     * Returns module version
     *
     * @return string
     */
    public function getVersion();

    /**
     * Tells whether the module should be displayed in menu
     *
     * @return bool
     */
    public function isDisplayedInMenu();

    /**
     * Tells whether the module should be displayed in utilities
     *
     * @return bool
     */
    public function isDisplayedInUtilities();

    /**
     * Returns the list of module submenu elements
     *
     * @param $language
     * @return array
     */
    public function getAdminSubmenuElements($language);

    /**
     * Returns the list of module dashboars elements
     *
     * @param $language
     * @return array
     */
    public function getAdminUtilitiesElements($language);

    /**
     * Returns the list of module landing dashboard elements
     *
     * @param $language
     * @return array
     */
    public function getAdminDashboardElements($language);

    /**
     * Returns the array of URLs to be displayed in sitemap
     *
     * @return array
     */
    public function getSitemapURLs();

    /**
     * Returns a list of variables that are used to generate module configuration
     *
     * @return array
     */
    public function getConfigVariables();

    /**
     * Executed on installation
     */
    public function onInstall();

    /**
     * Executed on uninstall
     */
    public function onUninstall();

    /**
     * @return Document|null
     */
    public function handleRequest($uri, $uri_component_one, $uri_component_two);
}
