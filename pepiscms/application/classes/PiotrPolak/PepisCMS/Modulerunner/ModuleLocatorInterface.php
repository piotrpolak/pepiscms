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

namespace PiotrPolak\PepisCMS\Modulerunner;

defined('BASEPATH') or exit('No direct script access allowed');

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
    public function getPublicControllerPath($module_name);

    /**
     * Locates module's admin controller
     *
     * @param string $module_name
     * @return string
     */
    public function getAdminControllerPath($module_name);

    /**
     * Locates module's widget controller
     *
     * @param string $module_name
     * @return string
     */
    public function getWidgetControllerPath($module_name);

    /**
     * Locates module's descriptor
     *
     * @param string $module_name
     * @return string
     */
    public function getDescriptorPath($module_name);

    /**
     * Locates module's model
     *
     * @param string $module_name
     * @param $model_name
     * @return string
     */
    public function getModelPath($module_name, $model_name);
}
