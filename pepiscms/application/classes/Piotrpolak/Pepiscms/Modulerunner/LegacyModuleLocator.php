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
    public function getPublicControllerRelativePath($module_name)
    {
        return $module_name . '_controller.php';
    }

    /**
     * @inheritDoc
     */
    public function getAdminControllerRelativePath($module_name)
    {
        return $module_name . '_admin_controller.php';
    }

    /**
     * @inheritDoc
     */
    public function getDescriptorRelativePath($module_name)
    {
        return $module_name . '_descriptor.php';
    }
}