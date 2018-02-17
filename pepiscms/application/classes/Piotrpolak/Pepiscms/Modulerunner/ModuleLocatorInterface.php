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

namespace Piotrpolak\Pepiscms\Modulerunner;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Locates module logic files.
 *
 * @since 1.0.0
 */
interface ModuleLocatorInterface
{
    /**
     * Locates module's public controller
     *
     * @param string $module_name
     * @return string
     */
    public function getPublicControllerRelativePath($module_name);

    /**
     * Locates module's admin controller
     *
     * @param string $module_name
     * @return string
     */
    public function getAdminControllerRelativePath($module_name);

    /**
     * Locates module's descriptor
     *
     * @param string $module_name
     * @return string
     */
    public function getDescriptorRelativePath($module_name);
}