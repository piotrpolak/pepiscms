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

        $path = $this->load->resolveModuleDirectory($module_name) . $module_name . '_descriptor.php';

        if (!file_exists($path)) {
            return FALSE;
        }

        require_once($path);

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
        $path = $this->load->resolveModuleDirectory($module_name) . $module_name . '_admin_controller.php';
        return file_exists($path);
    }

    /**
     * Tells whether the public controller is runnable
     *
     * @param string $module_name
     * @return bool
     */
    public function isPublicControllerRunnable($module_name)
    {
        $path = $this->load->resolveModuleDirectory($module_name) . $module_name . '_controller.php';
        return file_exists($path);
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

    /**
     * Compiles pattern, if keys to be replaced are specified, then the script will not parse pattern (faster)
     *
     * @param $pattern
     * @param $object_with_data
     * @param array $keys_to_be_replaced
     * @return string
     */
    private static function compilePattern($pattern, $object_with_data, $keys_to_be_replaced = array())
    {
        // TODO Move to StringCompiler, original method from AdminCRUDController
        // V2

        if (count($keys_to_be_replaced) == 0) {
            preg_match_all('/{([a-z_0-9]+)}/', $pattern, $matches);
            $keys_to_be_replaced = $matches[1];
        }

        $is_object = is_object($object_with_data);

        foreach ($keys_to_be_replaced as $key) {
            if ($is_object) {
                if (!isset($object_with_data->$key)) {
                    continue;
                }
                $pattern = trim(str_replace('{' . $key . '}', $object_with_data->$key, $pattern));
            } else // Array
            {
                if (!isset($object_with_data[$key])) {
                    continue;
                }
                $pattern = trim(str_replace('{' . $key . '}', $object_with_data[$key], $pattern));
            }
        }

        return $pattern;
    }

    /**
     * Creates and installs user space module
     *
     * @param $module_database_table_name
     * @param $module_name
     * @param bool $auto_install
     * @param bool $parse_database_schema
     * @param bool $database_group
     * @param bool $translations
     * @param bool $generate_public_controller
     * @param bool $is_crud
     * @param bool $generate_security_policy
     * @return bool
     */
    public function makeUserSpaceModule($module_database_table_name, $module_name, $auto_install = TRUE, $parse_database_schema = TRUE, $database_group = FALSE, $translations = FALSE, $generate_public_controller = TRUE, $is_crud = TRUE, $generate_security_policy = FALSE)
    {
        if ($parse_database_schema) {
            $this->load->moduleLibrary('crud', 'TableUtility', array('database_group' => $database_group));

            if (!$this->tableutility->tableExists($module_database_table_name)) {
                return FALSE;
            }

            $definition = $this->tableutility->getDefinitionFromTable($module_database_table_name);
            if (!$definition) {
                return FALSE;
            }
        }

        $template_base_path = APPPATH . '../resources/module_template/';

        $this->load->helper('inflector');
        $module_name_singular = singular($module_name);
        $module_name_lower_case = strtolower($module_name);

        $label_field_name = 'id';
        $image_field_name = FALSE;
        $description_field_name = FALSE;
        $order_field_name = FALSE;
        $created_at_field_name = FALSE;
        $updated_at_field_name = FALSE;
        $filters_element = '';

        // Setting default translations when no translations are selected
        if (!$translations || count($translations) == 0) {
            $translations = array('polish', 'english');
        }


        // Building directory structure
        $directory = INSTALLATIONPATH . 'modules/' . $module_name_lower_case . '/';
        @mkdir($directory);
        @mkdir($directory . 'models');
        @mkdir($directory . 'views');
        @mkdir($directory . 'resources');
        @mkdir($directory . 'language');

        // Building translations directory structure
        foreach ($translations as $translation) {
            @mkdir($directory . 'language/' . $translation);
        }

        // Used for builder, contains only valid table fields
        $coma_separated_list_of_fields = array();
        // Raw Datagrid & Formbuilder definition output
        $definition_output = 'CrudDefinitionBuilder::create()' . "\n";
        // Raw Language definition output
        $language_pairs = array();

        if ($parse_database_schema) {
            // Getting constants to be used instead of their numerical values
            $refl = new ReflectionClass('DataGrid');
            $datagrid_constants = array_flip($refl->getConstants());

            // Getting constants to be used instead of their numerical values
            $refl = new ReflectionClass('FormBuilder');
            $formbuilder_constants = array_flip($refl->getConstants());

            // If there is any definition
            if ($definition) {
                // NOTE this also includes FKs that are not writeable!!!
                $available_field_names = array_keys($definition);

                // Finding out the main picture field
                $possible_image_field_names = array('image', 'img', 'picture', 'image_path', 'img_path', 'picture_path', 'thumb');
                foreach ($possible_image_field_names as $possible_image_field_name) {
                    if (in_array($possible_image_field_name, $available_field_names)) {
                        $image_field_name = $possible_image_field_name;
                        break;
                    }
                }

                // Trying regular expression..
                // TODO Avoid picking up many-to-many keys
                if (!$image_field_name) {
                    foreach ($available_field_names as $available_field_name) {
                        foreach ($possible_image_field_names as $possible_image_field_name) {
                            if (strpos($available_field_name, $possible_image_field_name) !== FALSE) {
                                $image_field_name = $available_field_name;
                                break;
                            }
                        }
                        if ($image_field_name) {
                            break;
                        }
                    }
                }

                // Finding out the main label field
                $possible_label_field_names = array('name', 'label', 'title', 'first_name', 'last_name', 'firstName', 'lastName', 'message', 'question', 'answer', 'code');
                foreach ($possible_label_field_names as $possible_label_field_name) {
                    if (in_array($possible_label_field_name, $available_field_names)) {
                        $label_field_name = $possible_label_field_name;
                        break;
                    }
                }

                // Finding out description field
                $possible_description_field_names = array('description', 'desc', 'lead', 'introduction', 'intro', 'answer', 'message', 'code', 'address', 'street_address', 'state', 'voievodship', 'city', 'street');
                foreach ($possible_description_field_names as $possible_description_field_name) {
                    if (in_array($possible_description_field_name, $available_field_names) && $possible_description_field_name !== $label_field_name) {
                        $description_field_name = $possible_description_field_name;
                        break;
                    }
                }

                // Finding out description field
                $possible_order_field_names = array('item_order', 'position', 'pos');
                foreach ($possible_order_field_names as $possible_order_field_name) {
                    if (in_array($possible_order_field_name, $available_field_names)) {
                        // Numeric type check
                        if (strpos($definition[$possible_order_field_name]['validation_rules'], 'numeric') >= 0) {
                            $order_field_name = $possible_order_field_name;
                            break;
                        }

                    }
                }

                // Determine created at field name
                foreach ($available_field_names as $available_field_name) {
                    if (strpos($available_field_name, 'create') !== FALSE) {
                        // Input type check
                        if ($definition[$available_field_name]['input_type'] == FormBuilder::TIMESTAMP) {
                            $created_at_field_name = $available_field_name;
                            break;
                        }
                    }
                }

                // Determine updated at field name
                foreach ($available_field_names as $available_field_name) {
                    if (strpos($available_field_name, 'update') !== FALSE) {
                        // Input type check
                        if ($definition[$available_field_name]['input_type'] == FormBuilder::TIMESTAMP) {
                            $updated_at_field_name = $available_field_name;
                            break;
                        }
                    }
                }

                // Default ON checkbox fields
                $boolean_default_true_field_names = array('is_active', 'is_enabled', 'is_on');


                $tabs = "            ";
                foreach ($definition as $key => $value) {
                    // Skip ID fields
                    if ($key == 'id') {
                        continue;
                    }

                    // Do not show label field
                    if ($key == $label_field_name) {
                        $value['show_in_grid'] = FALSE;
                    }

                    // Do not show description field
                    if ($key == $description_field_name) {
                        $value['show_in_grid'] = FALSE;
                    }

                    // Order field - hide and set default value
                    if ($key == $order_field_name) {
                        $value['show_in_grid'] = FALSE;
                        $value['input_type'] = FormBuilder::HIDDEN;
                    }

                    // Make the time marking fields non editable on purpose
                    if ($key == $updated_at_field_name || $key == $created_at_field_name) {
                        $value['input_is_editable'] = FALSE;
                    }

                    // Default ON checkbox fields
                    if ($value['input_type'] == FormBuilder::CHECKBOX && !isset($value['input_default_value']) && in_array($key, $boolean_default_true_field_names)) {
                        $value['input_default_value'] = 1;
                    }

                    // Only for table fields
                    if (!isset($value['foreign_key_table']) || (isset($value['foreign_key_relationship_type']) && $value['foreign_key_relationship_type'] != FormBuilder::FOREIGN_KEY_MANY_TO_MANY)) {
                        $coma_separated_list_of_fields[] = '\'' . $key . '\'';
                    }


                    // Generating label, removing _id if present, for FKs
                    $language_label = $key;
                    $language_label = preg_replace('/' . preg_quote('_id', '/') . '$/', '', $language_label);
                    $language_label = ucfirst(trim(str_replace('_', ' ', $language_label)));

                    // Generating line
                    $language_pairs[$module_name . '_' . $key] = $language_label;

                    $definition_output .= $tabs . '->withField(\'' . $key . '\')' . "\n";

                    // For every definition pair
                    foreach ($value as $v_key => $v_value) {
                        if ($v_key == 'filter_type') {
                            // for date filters we define extra filters
                            if ($v_value == DataGrid::FILTER_DATE) {
                                $filters_element .=
                                    '        $this->datagrid->addFilter($this->lang->line($module_name.\'_' . $key . '\').\' (\'.$this->lang->line(\'crud_label_from\').\')\', \'' . $key . '\', DataGrid::FILTER_DATE, FALSE, DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL);
        $this->datagrid->addFilter($this->lang->line($module_name.\'_' . $key . '\').\' (\'.$this->lang->line(\'crud_label_to\').\')\', \'' . $key . '\', DataGrid::FILTER_DATE, FALSE, DataGrid::FILTER_CONDITION_LESS_OR_EQUAL);
';
                                continue;
                            }

                            // else
                            $v_value = 'DataGrid::' . $datagrid_constants[$v_value];
                        } // For these fields we want to keep constansts instead of numerical values
                        elseif ($v_key == 'input_type' || $v_key == 'foreign_key_relationship_type') {
                            $v_value = 'FormBuilder::' . $formbuilder_constants[$v_value];
                        } // To keep the uploads path in a single place
                        elseif ($v_key == 'upload_path' || $v_key == 'upload_display_path') {
                            $v_value = '$this->uploads_base_path';
                        } // Resolve boolan variables
                        elseif (is_bool($v_value)) {
                            $v_value = ($v_value ? 'TRUE' : 'FALSE');
                        } // Wrap non numeric values
                        elseif (!is_numeric($v_value)) {
                            $v_value = '\'' . str_replace('\'', '\\\'', $v_value) . '\'';
                        }

                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall($v_key, $v_value) . "\n";
                    }


                    // Adding SEO friendly callback for images and files
                    if ($value['input_type'] == FormBuilder::IMAGE || $value['input_type'] == FormBuilder::FILE) {
                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall('upload_complete_callback', 'array($this, \'_fb_callback_make_filename_seo_friendly\')') . "\n";
                    }

                    // Adding values and filter values for checkboxes to look human friendly
                    if ($value['input_type'] == FormBuilder::CHECKBOX) {
                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall('values', 'array(0 => $this->lang->line(\'global_dialog_no\'), 1 =>  $this->lang->line(\'global_dialog_yes\'))') . "\n"; // Only needed if you change input type to FormBuilder::SELECTBOX
                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall('filter_values', 'array(0 => $this->lang->line(\'global_dialog_no\'), 1 =>  $this->lang->line(\'global_dialog_yes\'))') . "\n";
                    }

                    // Setting default value for a timestamp
                    if ($value['input_type'] == FormBuilder::TIMESTAMP && !isset($definition[$key]['input_default_value'])) {
                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall('input_default_value', 'date(\'Y-m-d H:i:s\')') . "\n";
                    }

                    // Setting default value for a date
                    if ($value['input_type'] == FormBuilder::DATE && !isset($definition[$key]['input_default_value'])) {
                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall('input_default_value', 'date(\'Y-m-d\')') . "\n";
                    }

                    // Order field - hide and set default value
                    if ($key == $order_field_name && !isset($definition[$key]['input_default_value'])) {
                        $definition_output .= $tabs . '    ' . $this->generateBuilderMethodCall('input_default_value', 'time()') . "\n";
                    }

                    $definition_output .= $tabs . '->end()' . "\n";
                }
            }

            $definition_output .= $tabs . '->withImplicitTranslations($module_name, $this->lang)' . "\n";
            $definition_output .= $tabs . '->build();' . "\n";

            //die( $definition_output );
        }

        // Variables passed to pattern compiler
        $module_label = ucfirst(str_replace('_', ' ', $module_name));
        $data = array(
            'module_name' => $module_name,
            'module_databse_table_name' => $module_database_table_name,
            'module_class_name' => ucfirst($module_name),
            'module_label' => $module_label,
            'model_class_name' => ucfirst($module_name_singular) . '_model',
            'mudule_singular_name' => $module_name_singular,
            'definition_output' => $definition_output,
            'label_field_name' => $label_field_name,
            'coma_separated_list_of_fields' => implode(', ', $coma_separated_list_of_fields),
            'author' => $this->auth->getUserEmail(),
            'date' => date('Y-m-d'),
            'image_meta_code_element' => $image_field_name ? '$this->setMetaImageField(\'' . $image_field_name . '\', $this->uploads_base_path);' : '',
            'description_meta_code_element' => $description_field_name ? '$this->setMetaDescriptionPattern(\'{' . $description_field_name . '}\', array($this, \'_fb_format_meta_description\'));' : '',
            'order_meta_code_element' => $order_field_name ? '$this->setOrderable(TRUE, \'' . $order_field_name . '\');' : '$this->setOrderable(FALSE);',
            'updated_at_code_element' => $updated_at_field_name ? '$data_array[\'' . $updated_at_field_name . '\'] = date(\'Y-m-d H:i:s\');' : '',
            'filters_element' => $filters_element,
        );

        // Making admin controller
        $file_admin_controller = $directory . '' . $module_name_lower_case . '_admin_controller.php';
        if (!file_exists($file_admin_controller)) {
            if ($is_crud) {
                $template_file_admin_controller_path = $template_base_path . '_admin_controller.php';
            } else {
                $template_file_admin_controller_path = $template_base_path . '_admin_controller_non_crud.php';
            }
            file_put_contents($file_admin_controller, $this->compilePattern(file_get_contents($template_file_admin_controller_path), $data));
        }

        // Some default language data
        $language_pairs[$module_name . '_module_name'] = $module_label;
        $language_pairs[$module_name . '_add'] = 'Add a new element';

        // Building and writing translation file
        $this->load->moduleLibrary('translator', 'LanguageHelper');
        foreach ($translations as $translation) {
            $translation_file_path = $directory . 'language/' . $translation . '/' . $module_name_lower_case . '_lang.php';

            // Merging existing translations with the new pairs
            if (file_exists($translation_file_path)) {
                $existing_language_pairs = $this->languagehelper->getLanguageByPath($translation_file_path);
                if (count($existing_language_pairs) > 0) {
                    foreach ($existing_language_pairs as $key => $value) {
                        $language_pairs[$key] = $value;
                    }
                }
            }

            // Serializing translations
            $this->languagehelper->dumpFile($translation_file_path, $language_pairs);
        }

        // Building and writing model
        $file_model_path = $directory . 'models/' . $module_name_singular . '_model.php';
        if (!file_exists($file_model_path)) {
            file_put_contents($file_model_path, $this->compilePattern(file_get_contents($template_base_path . '_model.php'), $data));
        } else {
            // Replace acceptable fields
            $model_file_contents_exploded = file($file_model_path, FILE_IGNORE_NEW_LINES);
            if (count($model_file_contents_exploded) > 0) {
                foreach ($model_file_contents_exploded as &$model_file_contents_exploded_item) {
                    if (strpos($model_file_contents_exploded_item, 'setAcceptedPostFields') !== FALSE && strpos($model_file_contents_exploded_item, ';') !== FALSE) {
                        $model_file_contents_exploded_item = '        $this->setAcceptedPostFields(array(' . $data['coma_separated_list_of_fields'] . ')); /* line generated at ' . date('Y-m-d H:i:s') . ' */';
                    }
                }
                file_put_contents($file_model_path, implode("\n", $model_file_contents_exploded));
            }
        }

        // Making descriptor
        $file_descriptor = $directory . '' . $module_name_lower_case . '_descriptor.php';
        if (!file_exists($file_descriptor)) {
            file_put_contents($file_descriptor, $this->compilePattern(file_get_contents($template_base_path . '_descriptor.php'), $data));
        }


        // Making non-crud admin views
        if (!$is_crud) {
            @mkdir($directory . 'views/admin');
            $file_view_index = $directory . 'views/admin/index.php';
            if (!file_exists($file_view_index)) {
                file_put_contents($file_view_index, $this->compilePattern(file_get_contents($template_base_path . 'views/admin/index_no_crud.php'), $data));
            }
        }

        // Making public controller
        if ($generate_public_controller) {
            @mkdir($directory . 'views/public');
            $file_controller = $directory . '' . $module_name_lower_case . '_controller.php';
            if (!file_exists($file_controller)) {
                file_put_contents($file_controller, $this->compilePattern(file_get_contents($template_base_path . '_controller.php'), $data));
            }

            // Making views
            $file_view_index = $directory . 'views/public/index.php';
            if (!file_exists($file_view_index)) {
                file_put_contents($file_view_index, $this->compilePattern(file_get_contents($template_base_path . 'views/public/index.php'), $data));
            }

            $file_view_index = $directory . 'views/public/item.php';
            if (!file_exists($file_view_index)) {
                file_put_contents($file_view_index, $this->compilePattern(file_get_contents($template_base_path . 'views/public/item.php'), $data));
            }
        }


        // Generating security policy
        if ($generate_security_policy) {
            $this->load->library('SecurityPolicy');
            $this->load->library('SecurityPolicyBuilder');

            $policy_save_path = SecurityPolicy::getModulePolicyPath($module_name);
            if (!file_exists($policy_save_path)) {
                $method_default_access = array(
                    'index' => 'READ',
                    'edit' => 'WRITE',
                    'preview' => 'READ',
                    'revisionrestorefield' => 'FULL_CONTROL',
                    'revision' => 'READ',
                    'revisions' => 'READ',
                    'export' => 'FULL_CONTROL',
                    'import' => 'FULL_CONTROL',
                    'move' => 'WRITE',
                    'delete' => 'WRITE',
                    'star' => 'WRITE',
                );

                $controllers = $this->securitypolicy->describeModuleControllers($module_name);

                if (isset($controllers[0]->methods)) {
                    // Preparing description
                    $policy_entries = array();
                    foreach ($controllers[0]->methods as $method) {
                        $policy_entries[] = array(
                            'controller' => $module_name,
                            'method' => $method->name,
                            'entity' => $module_name_singular,
                            'access' => isset($method_default_access[$method->name]) ? $method_default_access[$method->name] : 'NONE'
                        );
                    }

                    // Generate and write XML file
                    $xml = $this->securitypolicybuilder->build($module_name, $policy_entries);
                    file_put_contents($policy_save_path, $xml);
                }
            }
        } // END Generating security policy


        // Copy 16px icon
        if (!file_exists($directory . 'resources/icon_16.png')) {
            copy($template_base_path . 'resources/icon_16.png', $directory . 'resources/icon_16.png');
        }

        // Copy 32px icon
        if (!file_exists($directory . 'resources/icon_32.png')) {
            copy($template_base_path . 'resources/icon_32.png', $directory . 'resources/icon_32.png');
        }

        // Installing module in PepisCMS and removing cache
        if ($auto_install) {
            $this->install($module_name, TRUE, FALSE);
            $this->auth->refreshSession();
            $this->load->library('Cachedobjectmanager');
            $this->cachedobjectmanager->cleanup();
            $this->db->cache_delete_all();
            ModuleRunner::flushCache();
        }

        return TRUE;
    }

    private function generateBuilderMethodCall($key, $value)
    {
        $map = array(
            'foreign_key_field' => 'withForeignKeyIdField'
        );

        if (isset($map[$key])) {
            $method = $map[$key];
        } else {
            $method = 'with' . ucfirst(camelize($key));
        }
        return '->' . $method . '(' . $value . ')';
    }
}
