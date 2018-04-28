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

abstract class AbstractDashboardController extends AdminController
{
    /**
     * @return array
     */
    protected function getDashboardElements()
    {
        $module_names = $this->modulerunner->getInstalledModulesNamesCached();
        $dashboard_elements = array();
        foreach ($module_names as $module_name) {
            $descriptior = $this->Module_model->getModuleDescriptor($module_name);
            if (!$descriptior) {
                continue;
            }

            $module_dashboard_elements = $this->getElements($descriptior);
            if (!is_array($module_dashboard_elements)) {
                continue;
            }

            foreach ($module_dashboard_elements as $module_dashboard_element) {
                if (isset($module_dashboard_element['controller'])) {
                    $module_dashboard_element['module'] = $module_dashboard_element['controller'];
                }

                $dashboard_elements[] = $module_dashboard_element;
            }
        }
        return $dashboard_elements;
    }

    /**
     * @param $dashboard_elements
     * @param $default_dashboard_element_group
     * @return array
     */
    protected function getDashboardElementsGrouped($dashboard_elements, $default_dashboard_element_group)
    {
        $dashboard_elements_grouped = array();
        foreach ($dashboard_elements as $dashboard_element) {
            if ($this->auth->isUserRoot() || !isset($dashboard_element['controller'])
                || SecurityManager::hasAccess($dashboard_element['controller'], $dashboard_element['method'],
                    isset($dashboard_element['module']) ? $dashboard_element['module'] : false)) {

                if (isset($dashboard_element['group']) && $dashboard_element['group']) {
                    $group = $dashboard_element['group'];
                } else {
                    $group = $default_dashboard_element_group;
                }

                if (!isset($dashboard_elements_grouped[$group])) {
                    $dashboard_elements_grouped[$group] = array();
                }

                $dashboard_elements_grouped[$group][] = $dashboard_element;
            }
        }
        return $dashboard_elements_grouped;
    }

    /**
     * @param ModuleDescriptableInterface $descriptior
     * @return mixed
     */
    protected abstract function getElements(ModuleDescriptableInterface $descriptior);
}
