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
 * Default module locator
 *
 * @since 1.0.0
 */
class ModuleLocator implements ModuleLocatorInterface
{

    /**
     * @inheritDoc
     */
    public function getPublicControllerRelativePath($module_name)
    {
        return ucfirst($module_name) . 'Controller.php';
    }

    /**
     * @inheritDoc
     */
    public function getAdminControllerRelativePath($module_name)
    {
        return ucfirst($module_name) . 'AdminController.php';
    }

    /**
     * @inheritDoc
     */
    public function getDescriptorRelativePath($module_name)
    {
        return ucfirst($module_name) . 'Descriptor.php';
    }
}