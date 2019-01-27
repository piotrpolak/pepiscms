<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * siteconfig admin controller
 *
 * @date 2018-11-05
 * @see AdminCRUDController for the list of the methods you can use in your constructor
 * @classTemplateVersion 20181105
 *
 * @property Siteconfig_model $Siteconfig_model
 */
class SiteconfigAdmin extends AdminCRUDController
{
    /**
     * Base path for file uploads
     *
     * @var String
     */
    private $uploads_base_path = './application/cache/tmp/'; // Overwritten by constructor

    /**
     * Default constructor containing all necessary definitions
     */
    public function __construct()
    {
        parent::__construct();

        // Overwriting uploads base path
        $this->uploads_base_path = $this->config->item('uploads_path') . 'siteconfig/';

        // Getting module and model name from class name
        $module_name = $this->getModuleName();

        $this->load->moduleLanguage($module_name, $module_name);
        $this->load->moduleModel($module_name, 'Siteconfig_model');

        $this->setFeedObject($this->Siteconfig_model)
            ->setPageTitle($this->lang->line($module_name . '_module_name'))
            ->setAddNewItemLabel($this->lang->line($module_name . '_add'));

        // Setting crud properties, these are optional. Default true all
        $this->setDeletable(true)
            ->setAddable(true)
            ->setEditable(true)
            ->setPreviewable(false)
            ->setPopupEnabled(false)
            ->setOrderable(false);


        $this->setMetaOrderField('name', $this->lang->line($module_name . '_name'));
        $this->setMetaTitlePattern('{name}'); // Use field names as {field_name}


        $this->setOrderable(false);


        $this->datagrid->setItemsPerPage(300)
            ->datagrid->setDefaultOrder('updated_datetime', 'DESC');

        // If not set, then DefaultFormRenderer is used
        // You can even use your own form templates, see views/templates
        $this->formbuilder->setRenderer(new FloatingFormRenderer())
            ->setApplyButtonEnabled(true);


        // Formbuilder callbacks
        $callbacks = array(
            '_fb_callback_after_save' => FormBuilder::CALLBACK_AFTER_SAVE,
        );
        // Assigning every single callback
        foreach ($callbacks as $callback_method_name => $callback_type) {
            // Attaching only when are callable
            if (is_callable(array($this, $callback_method_name))) {
                $this->formbuilder->setCallback(array($this, $callback_method_name), $callback_type);
            }
        }

        $definition = CrudDefinitionBuilder::create()
            ->withField(Siteconfig_model::NAME_FIELD_NAME)
            ->withFilterType(DataGrid::FILTER_BASIC)
            ->withShowInGrid(false)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::TEXTFIELD)
            ->withValidationRules('required|max_length[512]')
            ->end()
            ->withField(Siteconfig_model::VALUE_FIELD_NAME)
            ->withFilterType(DataGrid::FILTER_BASIC)
            ->withShowInGrid(true)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::TEXTAREA)
            ->withValidationRules('required')
            ->end()
            ->withField(Siteconfig_model::MODULE_FIELD_NAME)
            ->withFilterType(DataGrid::FILTER_BASIC)
            ->withShowInGrid(true)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::TEXTFIELD)
            ->withValidationRules('max_length[32]')
            ->end()
            ->withField(Siteconfig_model::IS_BOOLEAN_FIELD_NAME)
            ->withFilterType(DataGrid::FILTER_SELECT)
            ->withShowInGrid(true)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::CHECKBOX)
            ->withValidationRules('numeric')
            ->withValues(array(0 => $this->lang->line('global_dialog_no'), 1 => $this->lang->line('global_dialog_yes')))
            ->withFilterValues(array(0 => $this->lang->line('global_dialog_no'), 1 => $this->lang->line('global_dialog_yes')))
            ->end()
            ->withField(Siteconfig_model::IS_SERIALIZED_FIELD_NAME)
            ->withFilterType(DataGrid::FILTER_SELECT)
            ->withShowInGrid(true)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::CHECKBOX)
            ->withValidationRules('numeric')
            ->withValues(array(0 => $this->lang->line('global_dialog_no'), 1 => $this->lang->line('global_dialog_yes')))
            ->withFilterValues(array(0 => $this->lang->line('global_dialog_no'), 1 => $this->lang->line('global_dialog_yes')))
            ->end()
            ->withField(Siteconfig_model::UPDATED_DATETIME_FIELD_NAME)
            ->withShowInGrid(true)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::TIMESTAMP)
            ->withValidationRules('required')
            ->withInputIsEditable(false)
            ->withInputDefaultValue(date('Y-m-d H:i:s'))
            ->end()
            ->withField(Siteconfig_model::CREATED_DATETIME_FIELD_NAME)
            ->withShowInGrid(true)
            ->withShowInForm(true)
            ->withInputType(FormBuilder::TIMESTAMP)
            ->withValidationRules('required')
            ->withInputIsEditable(false)
            ->withInputDefaultValue(date('Y-m-d H:i:s'))
            ->end()
            ->withImplicitTranslations($module_name, $this->lang)
            ->build();

        // Here we go!
        $this->setDefinition($definition);
    }

    /**
     * Some logs or statistics maybe?
     * @param array $data_array associative array made of filtered POST variables
     */
    public function _fb_callback_after_save(&$data_array)
    {
        $title = $this->getCompiledTitle('', (object)$data_array);
        Logger::info('Editing element id:' . $this->formbuilder->getId() . ' (' . $title . ')',
            strtoupper(str_replace('Admin', '', __CLASS__)), $this->formbuilder->getId());
    }

    /**
     * This function is called on delete
     * You should remove any external resources such as images here
     *
     * @param mixed $id
     * @param object $item
     */
    public function _onDelete($id, $item)
    {
        // Logging action
        $title = $this->getCompiledTitle('', $item);
        Logger::info('Deleting element id:' . $id . ' (' . $title . ') ',
            strtoupper(str_replace('Admin', '', __CLASS__)), $id);
    }
}