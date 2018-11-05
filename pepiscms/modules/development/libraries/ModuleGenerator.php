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
 * An utility for generating CRUD modules.
 *
 * @since 1.0.0
 */
class ModuleGenerator extends ContainerAware
{
    /**
     * @var \PiotrPolak\PepisCMS\Modulerunner\ModuleLocatorInterface
     */
    private $moduleLocator;

    /**
     * Module_generator constructor.
     * @param null $params
     */
    public function __construct($params = null)
    {
        $this->moduleLocator = new \PiotrPolak\PepisCMS\Modulerunner\ModuleLocator();
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
     * @throws ReflectionException
     */
    public function makeUserSpaceModule($module_database_table_name,
                                        $module_name,
                                        $auto_install = true,
                                        $parse_database_schema = true,
                                        $database_group = false,
                                        $translations = false,
                                        $generate_public_controller = true,
                                        $is_crud = true,
                                        $generate_security_policy = false)
    {
        $this->load->library('SecurityPolicy');
        $this->load->library('SecurityPolicyBuilder');
        $this->load->moduleLibrary('translator', 'LanguageHelper');
        $this->load->helper('inflector');
        $this->load->library('Cachedobjectmanager');

        $definition = false;

        if ($parse_database_schema) {
            $this->load->moduleLibrary('crud', 'TableUtility', array('database_group' => $database_group));

            if (!$this->tableutility->tableExists($module_database_table_name)) {
                return false;
            }

            $definition = $this->tableutility->getDefinitionFromTable($module_database_table_name);
            if (!$definition) {
                return false;
            }

            $definition = $this->tableutility->orderFieldsByImportance($definition);
        }

        $template_base_path = APPPATH . '../resources/module_template/';

        $module_name_singular = singular($module_name);
        $module_name_lower_case = strtolower($module_name);

        $label_field_name = 'id';
        $image_field_name = false;
        $description_field_name = false;
        $order_field_name = false;
        $created_at_field_name = false;
        $updated_at_field_name = false;
        $filters_element = '';

        // Setting default translations when no translations are selected
        if (!$translations || count($translations) == 0) {
            $translations = array('polish', 'english');
        }


        // Building directory structure
        $directory = $this->makeDirectories($translations, $module_name_lower_case);

        // Used for builder, contains only valid table fields
        $list_of_fields = array();
        // Raw Datagrid & Formbuilder definition output
        $definition_output = 'CrudDefinitionBuilder::create()';
        // Raw Language definition output
        $language_pairs = array();

        $module_model_name = ucfirst($module_name_singular) . '_model';

        if ($parse_database_schema) {
            // Getting constants to be used instead of their numerical values
            $refl = new ReflectionClass('DataGrid');
            $datagrid_constants = array_flip($refl->getConstants());

            // Getting constants to be used instead of their numerical values
            $refl = new ReflectionClass('FormBuilder');
            $formbuilder_constants = array_flip($refl->getConstants());


            if ($definition) {
                // NOTE this also includes FKs that are not writable!!!
                $available_field_names = array_keys($definition);

                $image_field_name = $this->getImageFieldName($available_field_names);
                $label_field_name = $this->getLabelFieldName($available_field_names);
                $description_field_name = $this->getDescriptionFieldName($available_field_names, $label_field_name);
                $order_field_name = $this->getOrderFieldName($available_field_names, $definition);
                $created_at_field_name = $this->getCreatedAtFieldName($available_field_names, $definition);
                $updated_at_field_name = $this->getUpdatedAtFieldName($available_field_names, $definition);

                $was_last_field_of_upload_type = false;

                $definition_output .= "\n";

                $tabs = "            ";
                foreach ($definition as $key => $value) {
                    // Skip ID fields
                    if ($key == 'id') {
                        continue;
                    }
                    $value = $this->hideMetadataField($key, $label_field_name, $value, $description_field_name);
                    $value = $this->hidePasswordField($value);
                    $value = $this->hideOrderField($key, $order_field_name, $value);
                    $value = $this->makeTimestampStatisticsDate($key, $updated_at_field_name, $created_at_field_name, $value);
                    $value = $this->setDefaultOnForCheckboxFields($value, $key);

                    $is_field_of_upload_type = in_array($value['input_type'], array(FormBuilder::IMAGE, FormBuilder::FILE));

                    // Only for table fields
                    if (!isset($value['foreign_key_table']) || (isset($value['foreign_key_relationship_type'])
                            && $value['foreign_key_relationship_type'] != FormBuilder::FOREIGN_KEY_MANY_TO_MANY)) {
                        $list_of_fields[] = $key;
                    }

                    // Generating line
                    $language_pairs[$module_name . '_' . $key] = $this->generateLanguageLabel($key);

                    if ($is_field_of_upload_type != $was_last_field_of_upload_type) {
                        $definition_output .= $tabs . '->withLineBreak()' . "\n\n";
                    }

                    $key_representation = $this->getCrudDefinitionLabelKeyRepresentation($key, $list_of_fields, $module_model_name);
                    $definition_output .= $tabs . '->withField(' . $key_representation . ')' . "\n";

                    $definition_output .= $this->generateCall($value, $datagrid_constants, $formbuilder_constants, $tabs);
                    $filters_element .= $this->generateFilterElement($value, $key);

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


                    $was_last_field_of_upload_type = $is_field_of_upload_type;
                }
            }

            $definition_output .= $tabs . '->withImplicitTranslations($module_name, $this->lang)' . "\n";
            $definition_output .= $tabs . '->build();' . "\n";

            //die( $definition_output );
        } else {
            $definition_output .= ';';
        }

        // Variables passed to pattern compiler
        $module_label = ucfirst(str_replace('_', ' ', $module_name));
        $data = $this->prepareReplacementTokens($module_database_table_name, $module_name, $module_label,
            $module_name_singular, $definition_output, $label_field_name, $list_of_fields,
            $image_field_name, $description_field_name, $order_field_name, $updated_at_field_name, $filters_element,
            $module_model_name);

        // Making admin controller
        $file_admin_controller = $directory . $this->moduleLocator->getAdminControllerPath($module_name_lower_case);
        if (!file_exists($file_admin_controller)) {
            $this->generateAdminCrudController($is_crud, $template_base_path, $file_admin_controller, $data);
        }

        $this->buildTranslations($module_name, $translations, $module_label, $language_pairs, $directory, $module_name_lower_case);


        // Building and writing model
        $file_model_path = $directory . $this->moduleLocator->getModelPath($module_name_lower_case, $module_name_singular . '_model');
        if (!file_exists($file_model_path)) {
            file_put_contents($file_model_path, PatternCompiler::compile(file_get_contents($template_base_path . '_model.php'), $data));
        } else {
            $this->generateModuleModel($file_model_path, $data);
        }

        $this->generateModuleDescriptor($directory, $module_name_lower_case, $template_base_path, $data);

        if (!$is_crud) {
            $this->generateAdminNonCrudController($directory, $template_base_path, $data);
        }

        if ($generate_public_controller) {
            $this->generatePublicController($directory, $module_name_lower_case, $template_base_path, $data);
            $this->generatePublicIndexView($directory, $template_base_path, $data);
            $this->generatePublicItemView($directory, $template_base_path, $data);
        }


        if ($generate_security_policy) {
            $policy_save_path = SecurityPolicy::getModulePolicyPath($module_name);
            if (!file_exists($policy_save_path)) {
                $this->generateSecurityPolicy($module_name, $module_name_singular, $policy_save_path);
            }
        }

        $this->copyIcons($directory, $template_base_path);

        if ($auto_install) {
            $this->Module_model->install($module_name, true, false);
            $this->cleanupCache();
        }

        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return string
     */
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

    /**
     * @param $possible_image_field_names
     * @param $available_field_names
     * @return string|bool
     */
    private function getImageFieldNameExact($possible_image_field_names, $available_field_names)
    {
        $image_field_name = false;
        foreach ($possible_image_field_names as $possible_image_field_name) {
            if (in_array($possible_image_field_name, $available_field_names)) {
                $image_field_name = $possible_image_field_name;
                break;
            }
        }
        return $image_field_name;
    }

    /**
     * @param $available_field_names
     * @param $possible_image_field_names
     * @return string|bool
     */
    private function getImageFieldNameBestMatch($available_field_names, $possible_image_field_names)
    {
        foreach ($available_field_names as $available_field_name) {
            foreach ($possible_image_field_names as $possible_image_field_name) {
                if (strpos($available_field_name, $possible_image_field_name) !== false) {
                    return $available_field_name;
                }
            }
        }
        return false;
    }

    /**
     * @param $available_field_names
     * @return string|bool
     */
    private function getLabelFieldName($available_field_names)
    {
        $possible_label_field_names = array('name', 'label', 'title', 'first_name', 'last_name', 'firstName', 'lastName', 'message', 'question', 'answer', 'code');

        foreach ($possible_label_field_names as $possible_label_field_name) {
            if (in_array($possible_label_field_name, $available_field_names)) {
                return $possible_label_field_name;
            }
        }
        return false;
    }

    /**
     * @param $available_field_names
     * @param $label_field_name
     * @return string|bool
     */
    private function getDescriptionFieldName($available_field_names, $label_field_name)
    {
        $possible_description_field_names = array('description', 'desc', 'lead', 'introduction', 'intro', 'answer',
            'message', 'code', 'address', 'street_address', 'state', 'voievodship', 'city', 'street');

        foreach ($possible_description_field_names as $possible_description_field_name) {
            if (in_array($possible_description_field_name, $available_field_names)
                && $possible_description_field_name !== $label_field_name) {
                return $possible_description_field_name;
            }
        }
        return false;
    }

    /**
     * @param $available_field_names
     * @param $definition
     * @return string|bool
     */
    private function getOrderFieldName($available_field_names, $definition)
    {
        $possible_order_field_names = array('item_order', 'position', 'pos');

        foreach ($possible_order_field_names as $possible_order_field_name) {
            if (in_array($possible_order_field_name, $available_field_names)) {
                // Numeric type check
                if (strpos($definition[$possible_order_field_name]['validation_rules'], 'numeric') >= 0) {
                    return $possible_order_field_name;
                }
            }
        }
        return false;
    }

    /**
     * @param $available_field_names
     * @param $definition
     * @return string|bool
     */
    private function getCreatedAtFieldName($available_field_names, $definition)
    {
        foreach ($available_field_names as $available_field_name) {
            if (strpos($available_field_name, 'create') !== false) {
                // Input type check
                if ($definition[$available_field_name]['input_type'] == FormBuilder::TIMESTAMP) {
                    return $available_field_name;
                }
            }
        }

        return false;
    }

    /**
     * @param $available_field_names
     * @param $definition
     * @return string|bool
     */
    private function getUpdatedAtFieldName($available_field_names, $definition)
    {
        foreach ($available_field_names as $available_field_name) {
            if (strpos($available_field_name, 'update') !== false) {
                // Input type check
                if ($definition[$available_field_name]['input_type'] == FormBuilder::TIMESTAMP) {
                    return $available_field_name;
                }
            }
        }

        return false;
    }

    /**
     * @param $translations
     * @param $module_name_lower_case
     * @return string
     */
    private function makeDirectories($translations, $module_name_lower_case)
    {
        $directory = INSTALLATIONPATH . 'modules/' . $module_name_lower_case . '/';
        @mkdir($directory);
        @mkdir($directory . 'models');
        @mkdir($directory . 'controllers');
        @mkdir($directory . 'views');
        @mkdir($directory . 'resources');
        @mkdir($directory . 'language');

        // Building translations directory structure
        foreach ($translations as $translation) {
            @mkdir($directory . 'language/' . $translation);
        }
        return $directory;
    }

    /**
     * @param $directory
     * @param $template_base_path
     */
    private function copyIcons($directory, $template_base_path)
    {
        // Copy 16px icon
        if (!file_exists($directory . 'resources/icon_16.png')) {
            copy($template_base_path . 'resources/icon_16.png', $directory . 'resources/icon_16.png');
        }

        // Copy 32px icon
        if (!file_exists($directory . 'resources/icon_32.png')) {
            copy($template_base_path . 'resources/icon_32.png', $directory . 'resources/icon_32.png');
        }
    }

    private function cleanupCache()
    {
        $this->auth->refreshSession();
        $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();
        ModuleRunner::flushCache();
    }

    /**
     * @param $module_name
     * @param $module_name_singular
     * @param $policy_save_path
     * @throws ReflectionException
     */
    private function generateSecurityPolicy($module_name, $module_name_singular, $policy_save_path)
    {
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

    /**
     * @param $directory
     * @param $template_base_path
     * @param $data
     */
    private function generatePublicItemView($directory, $template_base_path, $data)
    {
        $file_view_index = $directory . 'views/public/item.php';
        if (!file_exists($file_view_index)) {
            file_put_contents($file_view_index,
                PatternCompiler::compile(file_get_contents($template_base_path . 'views/public/item.php'), $data));
        }
    }

    /**
     * @param $directory
     * @param $template_base_path
     * @param $data
     */
    private function generatePublicIndexView($directory, $template_base_path, $data)
    {
        $file_view_index = $directory . 'views/public/index.php';
        if (!file_exists($file_view_index)) {
            file_put_contents($file_view_index,
                PatternCompiler::compile(file_get_contents($template_base_path . 'views/public/index.php'), $data));
        }
    }

    /**
     * @param $directory
     * @param $module_name_lower_case
     * @param $template_base_path
     * @param $data
     */
    private function generatePublicController($directory, $module_name_lower_case, $template_base_path, $data)
    {
        @mkdir($directory . 'views/public');
        $file_controller = $directory . $this->moduleLocator->getPublicControllerPath($module_name_lower_case);
        if (!file_exists($file_controller)) {
            file_put_contents($file_controller,
                PatternCompiler::compile(file_get_contents($template_base_path . '_controller.php'), $data));
        }
    }

    /**
     * @param $directory
     * @param $template_base_path
     * @param $data
     */
    private function generateAdminNonCrudController($directory, $template_base_path, $data)
    {
        @mkdir($directory . 'views/admin');
        $file_view_index = $directory . 'views/admin/index.php';
        if (!file_exists($file_view_index)) {
            file_put_contents($file_view_index,
                PatternCompiler::compile(file_get_contents($template_base_path . 'views/admin/index_no_crud.php'), $data));
        }
    }

    /**
     * @param $directory
     * @param $module_name_lower_case
     * @param $template_base_path
     * @param $data
     */
    private function generateModuleDescriptor($directory, $module_name_lower_case, $template_base_path, $data)
    {
        $file_descriptor = $directory . $this->moduleLocator->getDescriptorPath($module_name_lower_case);
        if (!file_exists($file_descriptor)) {
            file_put_contents($file_descriptor,
                PatternCompiler::compile(file_get_contents($template_base_path . '_descriptor.php'), $data));
        }
    }

    /**
     * @param $file_model_path
     * @param $data
     * @return string
     */
    private function generateModuleModel($file_model_path, $data)
    {
        // Replace acceptable fields
        $model_file_contents_exploded = file($file_model_path, FILE_IGNORE_NEW_LINES);
        if (count($model_file_contents_exploded) > 0) {
            foreach ($model_file_contents_exploded as &$model_file_contents_exploded_item) {
                if (strpos($model_file_contents_exploded_item, 'setAcceptedPostFields') !== false
                    && strpos($model_file_contents_exploded_item, ';') !== false) {
                    $model_file_contents_exploded_item = '        $this->setAcceptedPostFields(array(' . $data['coma_separated_list_of_fields'] . ')); /* line generated at ' . date('Y-m-d H:i:s') . ' */';
                }
            }
            file_put_contents($file_model_path, implode("\n", $model_file_contents_exploded));
        }
    }

    /**
     * @param $translation_file_path
     * @param $language_pairs
     * @return mixed
     */
    private function mergeLanguageTranslationPairs($translation_file_path, $language_pairs)
    {
        // Merging existing translations with the new pairs
        if (file_exists($translation_file_path)) {
            $existing_language_pairs = $this->languagehelper->getLanguageByPath($translation_file_path);
            if (count($existing_language_pairs) > 0) {
                foreach ($existing_language_pairs as $key => $value) {
                    $language_pairs[$key] = $value;
                }
            }
        }
        return $language_pairs;
    }

    /**
     * @param $module_name
     * @param $translations
     * @param $module_label
     * @param $language_pairs
     * @param $directory
     * @param $module_name_lower_case
     */
    private function buildTranslations($module_name, $translations, $module_label, $language_pairs, $directory, $module_name_lower_case)
    {
        // Some default language data
        $language_pairs[$module_name . '_module_name'] = $module_label;
        $language_pairs[$module_name . '_add'] = 'Add a new element';

        // Building and writing translation file
        foreach ($translations as $translation) {
            $translation_file_path = $directory . 'language/' . $translation . '/' . $module_name_lower_case . '_lang.php';
            $language_pairs = $this->mergeLanguageTranslationPairs($translation_file_path, $language_pairs);

            // Serializing translations
            $this->languagehelper->dumpFile($translation_file_path, $language_pairs);
        }
    }

    /**
     * @param $is_crud
     * @param $template_base_path
     * @param $file_admin_controller
     * @param $data
     */
    private function generateAdminCrudController($is_crud, $template_base_path, $file_admin_controller, $data)
    {
        if ($is_crud) {
            $template_file_admin_controller_path = $template_base_path . '_admin_controller.php';
        } else {
            $template_file_admin_controller_path = $template_base_path . '_admin_controller_non_crud.php';
        }
        file_put_contents($file_admin_controller, PatternCompiler::compile(file_get_contents($template_file_admin_controller_path), $data));
    }

    /**
     * @param $module_database_table_name
     * @param $module_name
     * @param $module_label
     * @param $module_name_singular
     * @param $definition_output
     * @param $label_field_name
     * @param $list_of_fields
     * @param $image_field_name
     * @param $description_field_name
     * @param $order_field_name
     * @param $updated_at_field_name
     * @param $filters_element
     * @param $module_model_name
     * @return array
     */
    private function prepareReplacementTokens($module_database_table_name,
                                              $module_name,
                                              $module_label,
                                              $module_name_singular,
                                              $definition_output,
                                              $label_field_name,
                                              $list_of_fields,
                                              $image_field_name,
                                              $description_field_name,
                                              $order_field_name,
                                              $updated_at_field_name,
                                              $filters_element,
                                              $module_model_name)
    {
        $data = array(
            'module_name' => $module_name,
            'module_databse_table_name' => $module_database_table_name,
            'module_class_name' => ucfirst($module_name),
            'module_label' => $module_label,
            'model_class_name' => $module_model_name,
            'mudule_singular_name' => $module_name_singular,
            'definition_output' => $definition_output,
            'label_field_name' => $label_field_name,
            'fields_list_output' => $this->generateFieldsListOutputDefinition($list_of_fields),
            'coma_separated_list_of_fields' => $this->generateFieldsListOutputModelUsageDefinition($list_of_fields),
            'author' => $this->auth->getUserEmail(),
            'date' => date('Y-m-d'),
            'image_meta_code_element' => $image_field_name ? '$this->setMetaImageField(\'' . $image_field_name . '\', $this->uploads_base_path);' : '',
            'description_meta_code_element' => $description_field_name ? '$this->setMetaDescriptionPattern(\'{' . $description_field_name . '}\', array($this, \'_fb_format_meta_description\'));' : '',
            'order_meta_code_element' => $order_field_name ? '$this->setOrderable(TRUE, \'' . $order_field_name . '\');' : '$this->setOrderable(FALSE);',
            'updated_at_code_element' => $updated_at_field_name ? '$data_array[\'' . $updated_at_field_name . '\'] = date(\'Y-m-d H:i:s\');' : '',
            'filters_element' => $filters_element,
        );
        return $data;
    }

    /**
     * @param $available_field_names
     * @return bool|string
     */
    private function getImageFieldName($available_field_names)
    {
        $possible_image_field_names = array('image', 'img', 'picture', 'image_path', 'img_path', 'picture_path', 'thumb');
        $image_field_name = $this->getImageFieldNameExact($possible_image_field_names, $available_field_names);
        if (!$image_field_name) {
            $image_field_name = $this->getImageFieldNameBestMatch($available_field_names, $possible_image_field_names);
        }
        return $image_field_name;
    }

    /**
     * @param $module_model_name
     * @param $key
     * @return string
     */
    private function getFieldModelConstantFullName($module_model_name, $key)
    {
        return $module_model_name . '::' . $this->getFieldModelConstantShortName($key);
    }

    /**
     * @param $key
     * @return string
     */
    private function getFieldModelConstantShortName($key)
    {
        return strtoupper($key) . '_FIELD_NAME';
    }

    /**
     * @param $coma_separated_list_of_fields
     * @return string
     */
    private function generateFieldsListOutputDefinition($coma_separated_list_of_fields)
    {
        $output = '';
        foreach ($coma_separated_list_of_fields as $field_name) {
            $output .= "\t" . 'const ' . $this->getFieldModelConstantShortName($field_name) . " = '" . $field_name . "';\n";
        }
        return $output;
    }

    /**
     * @param $coma_separated_list_of_fields
     * @return string
     */
    private function generateFieldsListOutputModelUsageDefinition($coma_separated_list_of_fields)
    {
        $keys = array();
        foreach ($coma_separated_list_of_fields as $field_name) {
            $keys[] = 'self::' . $this->getFieldModelConstantShortName($field_name);
        }
        return implode(",\n\t\t\t\t\t", $keys);
    }

    /**
     * @param $key
     * @param $list_of_fields
     * @param $module_model_name
     * @return string
     */
    private function getCrudDefinitionLabelKeyRepresentation($key, $list_of_fields, $module_model_name)
    {
        if (in_array($key, $list_of_fields)) {
            $key_constant_name = $this->getFieldModelConstantFullName($module_model_name, $key);
        } else {
            $key_constant_name = "'" . $key . "'";
        }
        return $key_constant_name;
    }

    /**
     * @param $key
     * @param $label_field_name
     * @param $value
     * @param $description_field_name
     * @return mixed
     */
    private function hideMetadataField($key, $label_field_name, $value, $description_field_name)
    {
        // Do not show label field
        if ($key == $label_field_name) {
            $value['show_in_grid'] = false;
        }

        // Do not show description field
        if ($key == $description_field_name) {
            $value['show_in_grid'] = false;
        }
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    private function hidePasswordField($value)
    {
        // Do not show password fields
        if ($value['input_type'] == FormBuilder::PASSWORD) {
            $value['show_in_grid'] = false;
        }
        return $value;
    }

    /**
     * @param $key
     * @param $order_field_name
     * @param $value
     * @return mixed
     */
    private function hideOrderField($key, $order_field_name, $value)
    {
        // Order field - hide and set default value
        if ($key == $order_field_name) {
            $value['show_in_grid'] = false;
            $value['input_type'] = FormBuilder::HIDDEN;
        }
        return $value;
    }

    /**
     * @param $key
     * @param $updated_at_field_name
     * @param $created_at_field_name
     * @param $value
     * @return mixed
     */
    private function makeTimestampStatisticsDate($key, $updated_at_field_name, $created_at_field_name, $value)
    {
        // Make the time marking fields non editable on purpose
        if ($key == $updated_at_field_name || $key == $created_at_field_name) {
            $value['input_is_editable'] = false;
        }
        return $value;
    }

    /**
     * @param $value
     * @param $key
     * @return mixed
     */
    private function setDefaultOnForCheckboxFields($value, $key)
    {
        // Default ON checkbox fields
        $boolean_default_true_field_names = array('is_active', 'is_enabled', 'is_on');
        // Default ON checkbox fields
        if ($value['input_type'] == FormBuilder::CHECKBOX && !isset($value['input_default_value'])
            && in_array($key, $boolean_default_true_field_names)) {
            $value['input_default_value'] = 1;
        }
        return $value;
    }

    /**
     * @param $key
     * @return null|string|string[]
     */
    private function generateLanguageLabel($key)
    {
        // Generating label, removing _id if present, for FKs
        $language_label = $key;
        $language_label = preg_replace('/' . preg_quote('_id', '/') . '$/', '', $language_label);
        $language_label = ucfirst(trim(str_replace('_', ' ', $language_label)));
        return $language_label;
    }

    /**
     * @param $value
     * @param $key
     * @return string
     */
    private function generateFilterElement($value, $key)
    {
        $filters_element = '';
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
            }
        }
        return $filters_element;
    }

    /**
     * @param $value
     * @param $datagrid_constants
     * @param $formbuilder_constants
     * @param $tabs
     * @return string
     */
    private function generateCall($value, $datagrid_constants, $formbuilder_constants, $tabs)
    {
        $definition_output = '';
        foreach ($value as $v_key => $v_value) {
            if ($v_key == 'filter_type') {
                // for date filters we define extra filters
                if ($v_value == DataGrid::FILTER_DATE) {
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
        return $definition_output;
    }
}
