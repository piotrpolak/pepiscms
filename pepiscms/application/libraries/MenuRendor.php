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
 * Menu Rendor used in admin panel
 *
 * @since               0.1.0
 */
class MenuRendor
{

    private $CI;
    private $use_cache = TRUE;

    /**
     * Default constructor, empty
     * @param array $params
     */
    public function __construct($params = null)
    {
        $this->CI = &get_instance();
        $this->CI->load->config('menu');
        $this->CI->load->model('Module_model');
        $this->CI->load->library('ModuleRunner');
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
     * Returns the list of modules to be displayed in menu
     *
     * @deprecated as PepisCMS 0.2.4
     * @return array
     */
    public static function getMenuModules()
    {
        trigger_error('MenuRendor::getMenuModules() is deprecated. Consider using ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()', E_USER_DEPRECATED);
        $CI = &get_instance();
        $CI->load->library('ModuleRunner');
        return ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached();
    }

    /**
     * Renders admin menu
     *
     * @param string $controller
     * @param string $method
     * @param string $language_code
     * @param bool $pull_submenu_from_controller
     * @return string
     */
    public function render($controller, $method, $language_code = '', $pull_submenu_from_controller = FALSE)
    {
        $menu = $this->CI->config->item('menu');

        if (!$pull_submenu_from_controller) {
            $pull_submenu_from_controller = $controller;
        }

        // Get currently running module
        $current_module = isset($this->CI->modulerunner) ? $this->CI->modulerunner->getRunningModuleName() : FALSE;

        $cache_var_name = 'menu_c:' . $controller . '_m:' . $method . '_lc:' . $language_code . '_psfc:' . $pull_submenu_from_controller . '_cm:' . $current_module . '_lng:' . $this->CI->lang->getCurrentLanguage();

        if ($this->use_cache && $this->CI->auth->getSessionVariable($cache_var_name)) {
            return $this->CI->auth->getSessionVariable($cache_var_name);
        }

        $menu_map = array();
        // Template for each of the menu elements
        $menu_map_item_template = array(
            'url' => '',
            'label' => '',
            'title' => '',
            'icon_url' => '',
            'is_active' => '',
            'extra_css_classes' => array(),
            'submenu' => array(),
        );


        // For each one of the main menu items
        foreach ($menu['mainmenu'] as $item) {
            if (!SecurityManager::hasAccess($item['controller'], isset($item['method']) ? $item['method'] : 'index')) {
                continue;
            }
            if ($item['controller'] == 'pages' && !$this->CI->config->item('cms_enable_pages')) {
                continue;
            } elseif ($item['controller'] == 'utilities' && !$this->CI->config->item('cms_enable_utilities')) {
                continue;
            } elseif ($item['controller'] == 'ajaxfilemanager' && (!$this->CI->config->item('cms_enable_filemanager') || !$this->CI->config->item('feature_is_enabled_filemanager'))) {
                continue;
            }


            // Initialize item out of the template
            $menu_map_item = $menu_map_item_template; // RESET

            // Detecting menu icon
            $menu_map_item['icon_url'] = isset($item['icon_path']) && $item['icon_path'] ? $item['icon_path'] : FALSE;

            if ($item['controller'] == $pull_submenu_from_controller || $item['controller'] == $current_module) {
                $menu_map_item['is_active'] = TRUE;
            }

            if (isset($item['show_label']) && !$item['show_label']) {
                $menu_map_item['extra_css_classes'][] = 'no-label';
            }

            $url = admin_url() . $item['controller'] . '/';
            if (isset($item['method'])) {
                $url .= $item['method'] . '/';
            }
            if (isset($item['append_language_code']) && $item['append_language_code'] && $language_code) {
                $url .= 'language_code-' . $language_code . '/';
            }

            $menu_map_item['url'] = $url;

            if (isset($item['description'])) {
                $menu_map_item['title'] = $item['description'];
            }

            if (isset($item['label']) && $item['label']) {
                $menu_map_item['label'] = $this->CI->lang->line($item['label']);
            }

            $menu_map[] = $menu_map_item;
        }

        // Here we are going with the modules

        // Checking if one can run modules
        if (SecurityManager::hasAccess('module', 'run')) {
            // Building array of modules to which the user has granted access
            $modules = $this->CI->auth->getSessionVariable('access_modules');
            if (!$modules) {
                get_instance()->load->library('ModuleRunner');
                $all_modules = ModuleRunner::getInstalledModulesDisplayedInMenuCached();
                $modules = array(); // Resetting type

                // Checking access to each of the modules
                foreach ($all_modules as $module) {
                    if (!SecurityManager::hasAccess($module->name, 'index', $module->name)) {
                        continue;
                    }
                    $modules[] = $module;
                }
                // Saving some memory
                unset($all_modules);

                // Persisting variable
                $this->CI->auth->setSessionVariable('access_modules', $modules);
            }

            // Building main menu and submenu modules arrays
            $main_menu_modules = array();
            $sub_menu_modules = array();
            foreach ($modules as $module) {
                if ($module->parent_module_id !== NULL) {
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
                $module_label = $this->CI->Module_model->getModuleLabel($module->name, $this->CI->lang->getCurrentLanguage());
                if (!$module_label) {
                    $module_label = $module->label;
                }

                // Reading module description
                $module_description = $this->CI->Module_model->getModuleDescription($module->name, $this->CI->lang->getCurrentLanguage());
                if ($module_description) {
                    $menu_map_item['title'] = $module_description;
                }

                // Assigning remaining menu item attributes
                $menu_map_item['icon_url'] = module_icon_small_url($module->name);
                $menu_map_item['is_active'] = $current_module == $module->name;
                $menu_map_item['label'] = $module_label;
                $menu_map_item['url'] = module_url($module->name);

                if (!$module->name) {
                    $items = &$menu[$pull_submenu_from_controller];
                } else {
                    $items = $this->CI->Module_model->getModuleAdminSubmenuElements($module->name, $this->CI->lang->getCurrentLanguage());
                }

                // For modules that are under the current modules if case
                if (isset($sub_menu_modules[$module->module_id])) {
                    foreach ($sub_menu_modules[$module->module_id] as $element) {
                        $items[] = array(
                            'module' => $element->name,
                            'controller' => $element->name,
                            'method' => 'index',
                            'label' => $this->CI->Module_model->getModuleLabel($element->name, $this->CI->lang->getCurrentLanguage()),
                            'icon_url' => module_icon_small_url($element->name),
                        );
                    }
                }

                // For every items
                if ($items && count($items) > 0) {
                    foreach ($items as $item) {
                        if (!SecurityManager::hasAccess($item['controller'], isset($item['method']) ? $item['method'] : 'index', $module->name)) {
                            continue;
                        }

                        $menu_map_item_submenu = $menu_map_item_template; // RESET

                        // The following if prevents 2 elements to be active when $pull_submenu_from_controller is specified
                        if ($pull_submenu_from_controller && $pull_submenu_from_controller == $item['controller']) {
                            $is_active = TRUE;
                        } elseif ($item['controller'] == $controller) {
                            $is_active = TRUE;
                        } else {
                            $is_active = FALSE;
                        }

                        if ($is_active && $method == $item['method']) {
                            $menu_map_item_submenu['is_active'] = TRUE;
                        }

                        /* BEGIN Setting URL */
                        if (!$module->name) {
                            $menu_map_item_submenu['url'] = admin_url() . $item['controller'] . '/';

                        } elseif (isset($item['module']) && $item['module']) {
                            $menu_map_item_submenu['url'] = module_url($item['module']);
                        } else {
                            $menu_map_item_submenu['url'] = module_url($module->name);
                        }

                        if (isset($item['method'])) {
                            $menu_map_item_submenu['url'] .= $item['method'] . '/';
                        } else {
                            $menu_map_item_submenu['url'] .= 'index/';
                        }

                        if (isset($item['params'])) {
                            $menu_map_item_submenu['url'] .= $item['params'] . '/';
                        }
                        if (isset($item['append_language_code']) && $item['append_language_code']) {
                            $menu_map_item_submenu['url'] .= 'language_code-' . $language_code;
                        }

                        if (isset($item['description'])) {
                            $menu_map_item_submenu['title'] = $this->CI->lang->line($item['description']);
                        }

                        $menu_map_item_submenu['icon_url'] = $menu_map_item['icon_url'];
                        if (isset($item['icon_url']) && $item['icon_url']) {
                            $menu_map_item_submenu['icon_url'] = $item['icon_url'];
                        }
                        /* END Setting URL */

                        $menu_map_item_submenu['label'] = $this->CI->lang->line($item['label']);

                        $menu_map_item['submenu'][] = $menu_map_item_submenu;
                    }
                }

                $menu_map[] = $menu_map_item;
            }
        }

        $output = '<nav id="primary_navigation">' . $this->renderSubmenu($menu_map) . '</nav>';

        // Set cache
        $this->CI->auth->setSessionVariable($cache_var_name, $output);
        return $output;
    }

    private function renderSubmenu($menu_items, $level = 1)
    {
        $prefix = "\t";
        for ($i = 0; $i < $level; $i++) {
            $prefix .= "\t";
        }

        $ul_class = '';
        if ($level > 1) {
            $ul_class = ' class="sub"';
        }
        $out = "\n" . $prefix . '<ul' . $ul_class . '>';
        foreach ($menu_items as $menu_item) {
            $has_submenu = FALSE;
            if (count($menu_item['submenu'])) {
                $has_submenu = TRUE;
                $menu_item['extra_css_classes'][] = 'hasSubmenu';
            }

            if ($menu_item['is_active']) {
                $menu_item['extra_css_classes'][] = 'active';
            } elseif ($has_submenu) {
                foreach ($menu_item['submenu'] as $submenu_item) {
                    if ($submenu_item['is_active']) {
                        $menu_item['extra_css_classes'][] = 'active';
                        break;
                    }
                }
            }

            $classes = implode(' ', $menu_item['extra_css_classes']);
            $out .= "\n" . $prefix . "\t" . '<li class="' . $classes . '">';

            $out .= '<a href="' . $menu_item['url'] . '" title="' . $menu_item['title'] . '"><img src="' . $menu_item['icon_url'] . '" alt=""><span>' . $menu_item['label'] . '</span></a>';
            if ($has_submenu) {
                $out .= $this->renderSubmenu($menu_item['submenu'], $level + 1);
            }
            $out .= '</li>';
        }
        $out .= "\n" . $prefix . '</ul>';

        return $out;
    }

}
