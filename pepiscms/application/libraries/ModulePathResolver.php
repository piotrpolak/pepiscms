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
 * Resolves module absolute paths.
 */
class ModulePathResolver implements \Piotrpolak\Pepiscms\Modulerunner\ModuleLocatorInterface
{
    /**
     * @var \Piotrpolak\Pepiscms\Modulerunner\ModuleLocatorInterface[]
     */
    private $moduleLocators = array();

    /**
     * Default constructor, empty
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->moduleLocators[] = new \Piotrpolak\Pepiscms\Modulerunner\ModuleLocator();
        $this->moduleLocators[] = new \Piotrpolak\Pepiscms\Modulerunner\LegacyModuleLocator();
    }

    /**
     * @inheritdoc
     */
    public function getPublicControllerPath($module_name)
    {
        $module_directory = CI_Controller::get_instance()->load->resolveModuleDirectory($module_name);

        $path = FALSE;
        foreach ($this->moduleLocators as $moduleLocator) {
            $resolved_file = $moduleLocator->getPublicControllerPath($module_name);
            $resolved_path = $module_directory . '/' . $resolved_file;
            if (file_exists($resolved_path)) {
                $path = $resolved_path;
                break;
            }
        }

        return $path;
    }

    /**
     * /**
     * @inheritdoc
     */
    public function getAdminControllerPath($module_name)
    {
        $module_directory = CI_Controller::get_instance()->load->resolveModuleDirectory($module_name);

        $path = FALSE;
        foreach ($this->moduleLocators as $moduleLocator) {
            $resolved_file = $moduleLocator->getAdminControllerPath($module_name);
            $resolved_path = $module_directory . '/' . $resolved_file;
            if (file_exists($resolved_path)) {
                $path = $resolved_path;
                break;
            }
        }

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getDescriptorPath($module_name)
    {
        $module_directory = CI_Controller::get_instance()->load->resolveModuleDirectory($module_name);

        $path = FALSE;
        foreach ($this->moduleLocators as $moduleLocator) {
            $resolved_file = $moduleLocator->getDescriptorPath($module_name);
            $resolved_path = $module_directory . '/' . $resolved_file;
            if (file_exists($resolved_path)) {
                $path = $resolved_path;
                break;
            }
        }

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getModelPath($module_name, $model_name)
    {
        $module_directory = CI_Controller::get_instance()->load->resolveModuleDirectory($module_name);
        return $this->getModelPathUsingBaseDir($module_name, $model_name, $module_directory);
    }

    /**
     * Finds module model's path
     *
     * @param $module_name
     * @param $model_name
     * @param $module_directory
     * @return bool|string
     */
    public function getModelPathUsingBaseDir($module_name, $model_name, $module_directory)
    {
        $path = FALSE;
        foreach ($this->moduleLocators as $moduleLocator) {
            $resolved_file = $moduleLocator->getModelPath($module_name, $model_name);
            $resolved_path = $module_directory . '/' . $resolved_file;
            if (file_exists($resolved_path)) {
                $path = $resolved_path;
                break;
            }
        }

        return $path;
    }

}