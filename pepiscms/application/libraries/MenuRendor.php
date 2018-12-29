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
 * Menu Rendor used in admin panel
 *
 * @since 0.1.0
 */
class MenuRendor extends ContainerAware
{
    const HAS_SUBMENU_CSS_CLASS = 'hasSubmenu';
    const ACTIVE_CSS_CLASS = 'active';

    const PROP_IS_ACTIVE = 'is_active';
    const PROP_EXTRA_CSS_CLASSES = 'extra_css_classes';
    const PROP_SUBMENU = 'submenu';
    const PROP_ICON_URL = 'icon_url';
    const PROP_CONTROLLER = 'controller';
    const PROP_MODULE = 'module';
    const PROP_METHOD = 'method';
    const PROP_PARAMS = 'params';
    const PROP_APPEND_LANGUAGE_CODE = 'append_language_code';
    const PROP_TITLE = 'title';
    const PROP_LABEL = 'label';
    const PROP_URL = 'url';
    const PROP_MAINMENU = 'mainmenu';
    const DEFAULT_METHOD = 'index';
    const PROP_ICON_PATH = 'icon_path';
    const PROP_SHOW_LABEL = 'show_label';
    const PROP_DESCRIPTION = 'description';
    private $use_cache = true;

    /**
     * Default constructor, empty
     */
    public function __construct()
    {
        $this->load->config('menu');
        $this->load->model('Module_model');
        $this->load->library('ModuleRunner');
    }

    /**
     * Returns whether to use cache
     *
     * @return bool
     */
    public function isUseCache()
    {
        return $this->use_cache;
    }

    /**
     * Sets whether to use cache
     *
     * @param bool $use_cache
     */
    public function setUseCache($use_cache)
    {
        $this->use_cache = $use_cache;
    }

    /**
     * Renders admin menu
     *
     * @param string $controller
     * @param string $method
     * @param string $language_code
     * @param bool $pull_submenu_from_controller
     * @param bool $current_module
     * @return string
     */
    public function render($controller, $method, $language_code = '', $pull_submenu_from_controller = false, $current_module = false)
    {
        $menu = $this->config->item('menu');

        $pull_submenu_from_controller = $this->ensurePulledSumbenuFromControllerIsSet($controller, $pull_submenu_from_controller);

        $current_module = $this->ensureCurrentModuleIsSet($current_module);

        $cache_var_name = $this->computeCacheVariableName($controller, $method, $language_code, $pull_submenu_from_controller, $current_module);
        if ($cached_menu = $this->getMenuFromCache($cache_var_name)) {
            return $cached_menu;
        }


        // Template for each of the menu elements
        $menu_map_item_template = array(
            self::PROP_URL => '',
            self::PROP_LABEL => '',
            self::PROP_TITLE => '',
            self::PROP_ICON_URL => '',
            self::PROP_IS_ACTIVE => '',
            self::PROP_EXTRA_CSS_CLASSES => array(),
            self::PROP_SUBMENU => array(),
        );


        // For each one of the main menu items
        $menu_map = array();
        foreach ($menu[self::PROP_MAINMENU] as $item) {
            if ($this->shouldSkipMenuItem($item)) {
                continue;
            }

            $menu_map[] = $this->buildMenuItem($language_code, $pull_submenu_from_controller, $current_module, $menu_map_item_template, $item);
        }

        // Here we are going with the modules

        // Checking if one can run modules
        if (SecurityManager::hasAccess('module', 'run')) {
            // Building array of modules to which the user has granted access
            $modules = $this->auth->getSessionVariable('access_modules');
            if (!$modules) {
                $all_modules = ModuleRunner::getInstalledModulesDisplayedInMenuCached();
                $modules = array(); // Resetting type

                // Checking access to each of the modules
                foreach ($all_modules as $module) {
                    if (!SecurityManager::hasAccess($module->name, self::DEFAULT_METHOD, $module->name)) {
                        continue;
                    }
                    $modules[] = $module;
                }
                // Saving some memory
                unset($all_modules);

                // Persisting variable
                $this->auth->setSessionVariable('access_modules', $modules);
            }

            // Building main menu and submenu modules arrays
            $main_menu_modules = array();
            $sub_menu_modules = array();
            foreach ($modules as $module) {
                if ($module->parent_module_id !== null) {
                    if (!isset($sub_menu_modules[$module->parent_module_id])) {
                        $sub_menu_modules[$module->parent_module_id] = array();
                    }
                    $sub_menu_modules[$module->parent_module_id][] = $module;
                } else {
                    $main_menu_modules[] = $module;
                }
            }

            // For each one of main menu modules
            foreach ($main_menu_modules as $module) {
                // Initialize item out of the template
                $menu_map_item = $menu_map_item_template; // RESET

                // Reading module label
                $module_label = $this->Module_model->getModuleLabel($module->name, $this->lang->getCurrentLanguage());
                if (!$module_label) {
                    $module_label = $module->label;
                }

                // Reading module description
                $module_description = $this->Module_model->getModuleDescription($module->name, $this->lang->getCurrentLanguage());
                if ($module_description) {
                    $menu_map_item[self::PROP_TITLE] = $module_description;
                }

                // Assigning remaining menu item attributes
                $menu_map_item[self::PROP_ICON_URL] = module_icon_small_url($module->name);
                $menu_map_item[self::PROP_IS_ACTIVE] = $current_module == $module->name;
                $menu_map_item[self::PROP_LABEL] = $module_label;
                $menu_map_item[self::PROP_URL] = module_url($module->name);

                if (!$module->name) {
                    $items = &$menu[$pull_submenu_from_controller];
                } else {
                    $items = $this->Module_model->getModuleAdminSubmenuElements($module->name, $this->lang->getCurrentLanguage());
                }

                // For modules that are under the current modules if case
                if (isset($sub_menu_modules[$module->module_id])) {
                    foreach ($sub_menu_modules[$module->module_id] as $element) {
                        $items[] = array(
                            self::PROP_MODULE => $element->name,
                            self::PROP_CONTROLLER => $element->name,
                            self::PROP_METHOD => self::DEFAULT_METHOD,
                            self::PROP_LABEL => $this->Module_model->getModuleLabel($element->name, $this->lang->getCurrentLanguage()),
                            self::PROP_ICON_URL => module_icon_small_url($element->name),
                        );
                    }
                }

                // For every items
                if ($items && count($items) > 0) {
                    foreach ($items as $item) {
                        if (!SecurityManager::hasAccess($item[self::PROP_CONTROLLER], isset($item[self::PROP_METHOD]) ? $item[self::PROP_METHOD] : self::DEFAULT_METHOD, $module->name)) {
                            continue;
                        }

                        $menu_map_item[self::PROP_SUBMENU][] = $this->computeSubmenuItem($controller, $method, $language_code, $pull_submenu_from_controller, $menu_map_item_template, $item, $module, $menu_map_item);
                    }
                }

                $menu_map[] = $menu_map_item;
            }
        }

        $output = $this->renderMenu($menu_map);

        $this->setMenuCache($cache_var_name, $output);
        return $output;
    }


    /**
     * @param $module
     * @param $item
     * @return string
     */
    private function buildSubmenuUrl($module, $item, $language_code)
    {
        if (!$module->name) {
            $submenu_url = admin_url() . $item[self::PROP_CONTROLLER] . '/';
        } elseif (isset($item[self::PROP_MODULE]) && $item[self::PROP_MODULE]) {
            $submenu_url = module_url($item[self::PROP_MODULE]);
        } else {
            $submenu_url = module_url($module->name);
        }

        if (isset($item[self::PROP_METHOD])) {
            $submenu_url .= $item[self::PROP_METHOD] . '/';
        } else {
            $submenu_url .= self::DEFAULT_METHOD . '/';
        }

        if (isset($item[self::PROP_PARAMS])) {
            $submenu_url .= $item[self::PROP_PARAMS] . '/';
        }

        if (isset($item[self::PROP_APPEND_LANGUAGE_CODE]) && $item[self::PROP_APPEND_LANGUAGE_CODE]) {
            $submenu_url .= 'language_code-' . $language_code;
        }
        return $submenu_url;
    }

    /**
     * @param $controller
     * @param $pull_submenu_from_controller
     * @param $item
     * @param $method
     * @return bool
     */
    private function isSubmenuActive($controller, $pull_submenu_from_controller, $item, $method)
    {
        if ($method == $item[self::PROP_METHOD]) {
            if ($pull_submenu_from_controller && $pull_submenu_from_controller == $item[self::PROP_CONTROLLER]) {
                return true;
            } elseif ($item[self::PROP_CONTROLLER] == $controller) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $item
     * @param $default
     * @return bool
     */
    private function getMenuIconPath($item, $default)
    {
        return isset($item[self::PROP_ICON_PATH]) && $item[self::PROP_ICON_PATH] ? $item[self::PROP_ICON_PATH] : $default;
    }

    /**
     * @param $language_code
     * @param $item
     * @return string
     */
    private function getMenuUrl($language_code, $item)
    {
        $url = admin_url() . $item[self::PROP_CONTROLLER] . '/';
        if (isset($item[self::PROP_METHOD])) {
            $url .= $item[self::PROP_METHOD] . '/';
        }
        if (isset($item[self::PROP_APPEND_LANGUAGE_CODE]) && $item[self::PROP_APPEND_LANGUAGE_CODE] && $language_code) {
            $url .= 'language_code-' . $language_code . '/';
        }
        return $url;
    }

    /**
     * @param $item
     * @return bool
     */
    private function shouldSkipMenuItem($item)
    {
        if (!SecurityManager::hasAccess($item[self::PROP_CONTROLLER], isset($item[self::PROP_METHOD]) ? $item[self::PROP_METHOD] : self::DEFAULT_METHOD)) {
            return true;
        }
        if ($item[self::PROP_CONTROLLER] == 'utilities' && !$this->config->item('cms_enable_utilities')) {
            return true;
        }
        if ($item[self::PROP_CONTROLLER] == 'ajaxfilemanager' && (!$this->config->item('cms_enable_filemanager') || !$this->config->item('feature_is_enabled_filemanager'))) {
            return true;
        }
        return false;
    }

    /**
     * @param $cache_var_name
     * @return bool|mixed
     */
    private function getMenuFromCache($cache_var_name)
    {
        if ($this->use_cache && $this->auth->getSessionVariable($cache_var_name)) {
            return $this->auth->getSessionVariable($cache_var_name);
        }
        return false;
    }

    /**
     * @param $current_module
     * @return bool|mixed
     */
    private function ensureCurrentModuleIsSet($current_module)
    {
        if (!$current_module) {
            $current_module = is_object($this->modulerunner) ? $this->modulerunner->getRunningModuleName() : false;
        }
        return $current_module;
    }

    /**
     * @param $controller
     * @param $pull_submenu_from_controller
     * @return mixed
     */
    private function ensurePulledSumbenuFromControllerIsSet($controller, $pull_submenu_from_controller)
    {
        if (!$pull_submenu_from_controller) {
            $pull_submenu_from_controller = $controller;
        }
        return $pull_submenu_from_controller;
    }

    /**
     * @param $language_code
     * @param $pull_submenu_from_controller
     * @param $current_module
     * @param array $menu_map_item_template
     * @param $item
     * @return array
     */
    private function buildMenuItem($language_code, $pull_submenu_from_controller, $current_module, array $menu_map_item_template, $item)
    {
        $new_item = $menu_map_item_template; // RESET

        if (isset($item[self::PROP_SHOW_LABEL]) && !$item[self::PROP_SHOW_LABEL]) {
            $new_item[self::PROP_EXTRA_CSS_CLASSES][] = 'no-label';
        }

        $new_item[self::PROP_IS_ACTIVE] = $this->isMenuActive($pull_submenu_from_controller, $current_module, $item);
        $new_item[self::PROP_ICON_URL] = $this->getMenuIconPath($item, false);
        $new_item[self::PROP_URL] = $this->getMenuUrl($language_code, $item);
        $new_item[self::PROP_TITLE] = $this->getMenuTitle($item);
        $new_item[self::PROP_LABEL] = $this->getMenuLabel($item);

        return $new_item;
    }

    /**
     * @param $controller
     * @param $method
     * @param $language_code
     * @param $pull_submenu_from_controller
     * @param $current_module
     * @return string
     */
    private function computeCacheVariableName($controller, $method, $language_code, $pull_submenu_from_controller, $current_module)
    {
        return 'menu_c:' . $controller . '_m:' . $method . '_lc:' . $language_code . '_psfc:' . $pull_submenu_from_controller . '_cm:' . $current_module . '_lng:' . $this->lang->getCurrentLanguage();
    }

    /**
     * @param $cache_var_name
     * @param $output
     */
    private function setMenuCache($cache_var_name, $output)
    {
        if ($this->use_cache) {
            $this->auth->setSessionVariable($cache_var_name, $output);
        }
    }

    /**
     * @param $controller
     * @param $method
     * @param $language_code
     * @param $pull_submenu_from_controller
     * @param array $menu_map_item_template
     * @param $item
     * @param $module
     * @param array $menu_map_item
     * @return array
     */
    private function computeSubmenuItem($controller, $method, $language_code, $pull_submenu_from_controller,
                                        array $menu_map_item_template, $item, $module, array $menu_map_item)
    {
        $new_item = $menu_map_item_template; // RESET

        // The following if prevents 2 elements to be active when $pull_submenu_from_controller is specified
        $new_item[self::PROP_IS_ACTIVE] = $this->isSubmenuActive($controller, $pull_submenu_from_controller, $item, $method);
        $new_item[self::PROP_URL] = $this->buildSubmenuUrl($module, $item, $new_item, $language_code);
        $new_item[self::PROP_TITLE] = $this->getMenuTitle($item);
        $new_item[self::PROP_ICON_URL] = $this->getMenuIconPath($item, $menu_map_item[self::PROP_ICON_URL]);
        $new_item[self::PROP_LABEL] = $this->getMenuLabel($item);

        return $new_item;
    }

    /**
     * @param $item
     * @return string|null
     */
    private function getMenuTitle($item)
    {
        if (isset($item[self::PROP_DESCRIPTION])) {
            return $this->lang->line($item[self::PROP_DESCRIPTION]);
        }
        return '';
    }

    /**
     * @param $item
     * @return string|null
     */
    private function getMenuLabel($item)
    {
        if (isset($item[self::PROP_LABEL]) && $item[self::PROP_LABEL]) {
            return $this->lang->line($item[self::PROP_LABEL]);
        }
        return '';
    }

    /**
     * @param $pull_submenu_from_controller
     * @param $current_module
     * @param $item
     * @return bool
     */
    private function isMenuActive($pull_submenu_from_controller, $current_module, $item)
    {
        return $item[self::PROP_CONTROLLER] == $pull_submenu_from_controller || $item[self::PROP_CONTROLLER] == $current_module;
    }

    /**
     * @param array $menu_items
     * @return string
     */
    private function renderMenu(array $menu_items)
    {
        return get_instance()->load->view('templates/menurendor_menu.php', array('menu_items' => $menu_items), true);
    }
}
