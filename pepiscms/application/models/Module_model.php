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
 * Module model
 *
 * @since 0.1.5
 */
class Module_model extends CI_Model
{
    /**
     * Module_model constructor.
     */
    public function __construct()
    {
        $this->load->helper('inflector');
        $this->load->library('ModulePathResolver');
    }


    /**
     * Returns the list of installed modules
     *
     * @return array
     */
    public function getInstalledModules()
    {
        return $this->db->select('*')
            ->order_by('is_displayed_in_menu DESC')
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();
    }

    /**
     * Tells whether the module is of system type
     *
     * @param string $module_name
     *
     * @return bool
     */
    function isCoreModule($module_name)
    {
        $user_module_directory = 'modules/';
        $core_module_directory = APPPATH . '../modules/';

        // Checks whenever system module directrory exists and if it is not overwritten by user space module
        if (file_exists($core_module_directory . $module_name) && !file_exists($user_module_directory . $module_name)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Returns the list of installed modules names
     *
     * @return array
     */
    public function getInstalledModulesNames()
    {
        $modules = array();

        $result = $this->db->select('name as name')
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();

        foreach ($result as $line) {
            $modules[] = $line->name;
        }
        return $modules;
    }

    /**
     * Tells whether the module is installed
     *
     * Use ModuleRunner::isModuleInstalled( $name ) to get the same result but faster
     *
     * @param string $name
     * @return bool
     */
    public function isInstalled($name)
    {
        $this->db->where('name', $name)
            ->from($this->config->item('database_table_modules'));

        return ($this->db->count_all_results() == 0 ? false : true);
    }

    /**
     * Returns information about module, from database (not descriptor)
     *
     * @param string $name
     * @return Object
     */
    public function getInfoByName($name)
    {
        $this->db->where('name', $name)
            ->limit(1);

        return $this->db->get($this->config->item('database_table_modules'))->row();
    }

    /**
     * Returns information about module, from database (not descriptor)
     *
     * @param string $module_name
     * @return Object
     */
    public function getParentInfoByName($module_name)
    {
        $info = $this->getInfoByName($module_name);
        if (!$info || !$info->parent_module_id) {
            return FALSE;
        }

        return $this->db->where('module_id', $info->parent_module_id)
            ->limit(1)
            ->get($this->config->item('database_table_modules'))
            ->row();
    }

    /**
     * Returns the list of installed modules that are displayed in menu
     *
     * @return array
     */
    public function getInstalledModulesDisplayedInMenu()
    {
        return $this->db->select('*')
            ->where('is_displayed_in_menu', '1')
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();
    }

    /**
     * Returns the list of installed modules that are displayed in menu and have no parents
     *
     * @return array
     */
    public function getInstalledModulesHavingNoParent()
    {
        return $this->db->select('*')
            ->where('parent_module_id', NULL)
            ->order_by('is_displayed_in_menu', 'desc')
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();
    }

    /**
     * Returns the list of installed modules that are displayed in menu and have no parents
     *
     * @return array
     */
    public function getInstalledModulesDisplayedInMenuHavingNoParent()
    {
        return $this->db->select('*')
            ->where('is_displayed_in_menu', '1')
            ->where('parent_module_id', NULL)
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();
    }

    /**
     * Returns the list of installed modules that are attached to other modules, grouped by parent module
     *
     * @return array
     */
    public function getInstalledModulesDisplayedInMenuHavingParentGroupedByParent()
    {
        $result = $this->db->select('*')
            ->where('is_displayed_in_menu', '1')
            ->where('parent_module_id IS NOT NULL', NULL, FALSE)
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();

        $out = array();
        foreach ($result as $line) {
            if (!isset($out[$line->parent_module_id])) {
                $out[$line->parent_module_id] = array();
            }

            $out[$line->parent_module_id][] = $line;
        }

        return $out;
    }

    /**
     * Returns the list of installed modules that are displayed in utilities
     *
     * As 0.2.4 Changed the behavior, now returning array of strings instead of array of objects
     *
     * @return array
     */
    public function getInstalledModulesNamesDisplayedInUtilities()
    {
        $result = $this->db->select('name')
            ->where('is_displayed_in_utilities', '1')
            ->order_by('item_order_utilities')
            ->get($this->config->item('database_table_modules'))
            ->result();

        $modules = array();
        foreach ($result as $line) {
            $modules[] = $line->name;
        }
        return $modules;
    }

    /**
     * Returns the list of installed module names that are installed in menu
     *
     * @since 0.2.4
     * @return array
     */
    public function getInstalledModulesNamesDisplayedInMenu()
    {
        $result = $this->db->select('name')
            ->where('is_displayed_in_menu', '1')
            ->order_by('item_order_menu')
            ->get($this->config->item('database_table_modules'))
            ->result();

        $modules = array();
        foreach ($result as $line) {
            $modules[] = $line->name;
        }
        return $modules;
    }

    /**
     * Returns the list of installed modules that are displayed in utilities
     *
     * @return array
     */
    public function getInstalledModulesDisplayedInUtilities()
    {
        return $this->db->select('*')
            ->where('is_displayed_in_utilities', '1')
            ->order_by('item_order_utilities')
            ->get($this->config->item('database_table_modules'))
            ->result();
    }

    /**
     * Returns localized module description
     *
     * @param string $module_name
     * @param string $language
     * @param string|boolean $default
     * @return string
     */
    public function getModuleDescription($module_name, $language, $default = FALSE)
    {
        $descriptor = $this->getModuleDescriptor($module_name);
        if (!$descriptor) {
            if (!$default) {
                return '';
            }
            return $default;
        }

        return $descriptor->getDescription($language);
    }

    /**
     * Returns localized module label
     *
     * @param string $module_name
     * @param string $language
     * @param string|boolean $default
     * @return string
     */
    public function getModuleLabel($module_name, $language, $default = FALSE)
    {
        $descriptor = $this->getModuleDescriptor($module_name);
        if (!$descriptor) {
            if (!$default) {
                return ucfirst(str_replace('_', ' ', $module_name));
            }
            return $default;
        }

        return $descriptor->getName($language);
    }

    /**
     * Returns localized module submenu elements
     *
     * @param string $module_name
     * @param string $language
     * @return array|bool
     */
    public function getModuleAdminSubmenuElements($module_name, $language)
    {
        $descriptor = $this->getModuleDescriptor($module_name);
        if (!$descriptor) {
            return FALSE;
        }

        return $descriptor->getAdminSubmenuElements($language);
    }

    /**
     * Returns module's config variables
     *
     * @param string $module_name
     * @return array|bool
     */
    public function getModuleConfigVariables($module_name)
    {
        $descriptor = $this->getModuleDescriptor($module_name);
        if (!$descriptor) {
            return FALSE;
        }

        return $descriptor->getConfigVariables();
    }

    /**
     * Returns module's sitemap URLs
     *
     * @param string $module_name
     * @return array|bool
     */
    public function getModuleSitemapURLs($module_name)
    {
        $descriptor = $this->getModuleDescriptor($module_name);
        if (!$descriptor) {
            return FALSE;
        }

        return $descriptor->getSitemapURLs();
    }

    /**
     * Returns module descriptor instance
     *
     * @param string $module_name
     * @return ModuleDescriptor|bool
     */
    public function getModuleDescriptor($module_name)
    {
        $class_name = ucfirst($module_name) . 'Descriptor';

        if (class_exists($class_name)) {
            return new $class_name();
        }

        $descriptor_path = CI_Controller::get_instance()->modulepathresolver->getDescriptorPath($module_name);

        if (!$descriptor_path) {
            return FALSE;
        }

        require_once($descriptor_path);

        if (!class_exists($class_name)) {
            return FALSE;
        }

        return new $class_name();
    }

    /**
     * Tells whether the admin controller is runnable
     *
     * @param string $module_name
     * @return bool
     */
    public function isAdminControllerRunnable($module_name)
    {
        return $this->modulepathresolver->getAdminControllerPath($module_name) !== FALSE;
    }

    /**
     * Tells whether the public controller is runnable
     *
     * @param string $module_name
     * @return bool
     */
    public function isPublicControllerRunnable($module_name)
    {
        return $this->modulepathresolver->getPublicControllerPath($module_name) !== FALSE;
    }

    /**
     * Installs module and executes installation procedures if defined in the descriptor
     *
     * @param string $module_name
     * @param bool $is_displayed_in_menu
     * @param bool $is_displayed_in_utilities
     * @param int|Null $parent_module_id
     * @return bool
     */
    public function install($module_name, $is_displayed_in_menu = FALSE, $is_displayed_in_utilities = TRUE, $parent_module_id = NULL)
    {
        if ($this->isInstalled($module_name)) {
            return FALSE;
        }

        if (!$parent_module_id) {
            $parent_module_id = NULL;
        }

        $success = $this->db->set('is_displayed_in_utilities', $is_displayed_in_utilities ? 1 : 0)
            ->set('is_displayed_in_menu', $is_displayed_in_menu ? 1 : 0)
            ->set('item_order_menu', $this->getMaximumOrder('item_order_menu') + 1)
            ->set('item_order_utilities', $this->getMaximumOrder('item_order_utilities') + 1)
            ->set('name', $module_name)
            ->set('parent_module_id', $parent_module_id)
            ->insert($this->config->item('database_table_modules'));

        $descriptor = $this->getModuleDescriptor($module_name);
        if ($descriptor) {
            $descriptor->onInstall();
        }

        if ($success) {
            $module_directory = $this->load->resolveModuleDirectory($module_name);
            $default_module_config = $module_directory . $module_name . '_config.php';

            if (file_exists($default_module_config)) {
                Logger::info('Installing module ' . $module_name . '. Default config file found.', 'MODULE');
                if (!file_exists(INSTALLATIONPATH . 'application/config/modules/')) {
                    mkdir(INSTALLATIONPATH . 'application/config/modules/');
                }
                copy($default_module_config, INSTALLATIONPATH . 'application/config/modules/' . $module_name . '.php');
            } else {
                Logger::info('Installing module ' . $module_name . '. No default config file found.', 'MODULE');
            }

            return TRUE;
        }

        return FALSE;
    }


    /**
     * Returns maximum item order
     *
     * @param $field
     * @return int
     */
    private function getMaximumOrder($field)
    {
        if (!in_array($field, array('item_order_menu', 'item_order_utilities'))) {
            return FALSE;
        }

        $item_order = 0;

        $row = $this->db->select('MAX(' . $field . ') as item_order')
            ->from($this->config->item('database_table_modules'))
            ->limit(1)
            ->get()
            ->row();

        if ($row) {
            $item_order = $row->item_order;
        }

        return $item_order;
    }

    /**
     * Updates module info
     *
     * @param string $module_name
     * @param bool $is_displayed_in_menu
     * @param bool $is_displayed_in_utilities
     * @param int|boolean $parent_module_id
     * @return bool
     */
    public function update($module_name, $is_displayed_in_menu = FALSE, $is_displayed_in_utilities = TRUE, $parent_module_id = FALSE)
    {
        // Reading module info
        $module_info = $this->getInfoByName($module_name);

        // When attaching back to the menu, place the module at the end
        if (!$module_info->is_displayed_in_menu && $is_displayed_in_menu) {
            // TODO Change field type to int and replace with time as PepisCMS 0.2.5
            $this->db->set('item_order_menu', $this->getMaximumOrder('item_order_menu') + 1);
        }

        // When attaching back to the utilities, place the module at the end
        if (!$module_info->is_displayed_in_utilities && $is_displayed_in_utilities) {
            // TODO Change field type to int and replace with time as PepisCMS 0.2.5
            $this->db->set('item_order_utilities', $this->getMaximumOrder('item_order_utilities') + 1);
        }

        $success = $this->db->set('is_displayed_in_utilities', $is_displayed_in_utilities ? 1 : 0)
            ->set('is_displayed_in_menu', $is_displayed_in_menu ? 1 : 0)
            ->set('parent_module_id', $parent_module_id)
            ->where('name', $module_name)
            ->update($this->config->item('database_table_modules'));

        if ($success) {
            // Change submodules parent id accordingly
            $row = $this->db->select('module_id')
                ->from($this->config->item('database_table_modules'))
                ->where('name', $module_name)
                ->get()
                ->row();

            if ($row) {
                $this->db->set('parent_module_id', $parent_module_id)
                    ->where('parent_module_id', $row->module_id)
                    ->update($this->config->item('database_table_modules'));
            }

            Logger::info('Updating module ' . $module_name, 'MODULE');
        }

        return $success;
    }

    /**
     * Tells whether a module has config variables
     *
     * @param string $module_name
     * @return bool
     */
    public function isModuleConfigurable($module_name)
    {
        $configVariables = $this->getModuleConfigVariables($module_name);
        if (!$configVariables) {
            return FALSE;
        }
        return (count($configVariables) > 0);
    }

    /**
     * Uninstalls module and executes uninstallation procedure
     *
     * @param string $module_name
     * @return bool
     */
    public function uninstall($module_name)
    {
        $descriptor = $this->getModuleDescriptor($module_name);
        if ($descriptor) {
            $descriptor->onUninstall();
        }

        // Change submodules parent id accordingly - set it to null
        $row = $this->db->select('module_id')
            ->from($this->config->item('database_table_modules'))
            ->where('name', $module_name)
            ->get()
            ->row();

        if ($row) {
            $this->db->set('parent_module_id', NULL)
                ->where('parent_module_id', $row->module_id)
                ->update($this->config->item('database_table_modules'));
        }

        return $this->db->where('name', $module_name)
            ->delete($this->config->item('database_table_modules'));
    }
}
