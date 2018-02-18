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
 * Module locator for modules created before PepisCMS 1.0.0
 *
 * @since 1.0.0
 */
class LegacyModuleLocator implements ModuleLocatorInterface
{

    /**
     * @inheritDoc
     */
    public function getPublicControllerPath($module_name)
    {
        return strtolower($module_name) . '_controller.php';
    }

    /**
     * @inheritDoc
     */
    public function getAdminControllerPath($module_name)
    {
        return strtolower($module_name) . '_admin_controller.php';
    }

    /**
     * @inheritDoc
     */
    public function getDescriptorPath($module_name)
    {
        return strtolower($module_name) . '_descriptor.php';
    }

    /**
     * @inheritDoc
     */
    public function getModelPath($module_name, $model_name)
    {
        return 'models/' . strtolower($model_name) . '.php';
    }
}