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

defined('BASEPATH') or exit('No direct script access allowed');

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
    public function getPublicControllerPath($module_name)
    {
        return 'controllers/' . ucfirst($module_name) . '.php';
    }

    /**
     * @inheritDoc
     */
    public function getAdminControllerPath($module_name)
    {
        return 'controllers/' . ucfirst($module_name) . 'Admin.php';
    }

    /**
     * @inheritDoc
     */
    public function getDescriptorPath($module_name)
    {
        return ucfirst($module_name) . 'Descriptor.php';
    }

    /**
     * @inheritDoc
     */
    public function getModelPath($module_name, $model_name)
    {
        return 'models/' . ucfirst($model_name) . '.php';
    }
}
