<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2017, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * See AdminCRUDController for the list of the methods you can use in your constructor
 * Some methods such as _onDelete should be overwritten if you use external resources such as images
 */
class Remote_applicationsAdmin extends AdminCRUDController
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->config->item('feature_is_enabled_remote_applications')) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }

        $module_name = 'remote_applications';

        $lang_field_prefix = $module_name . '_';
        $this->load->moduleLanguage($module_name);
        $this->setPageTitle($this->lang->line($lang_field_prefix . 'title'));
        $this->setAddNewItemLabel($this->lang->line($lang_field_prefix . 'add_new_item'));
        $this->assign('title', $this->lang->line($lang_field_prefix . 'title'));

        if (!$this->Remote_application_model->isInstalled()) {
            show_error($this->lang->line('remote_applications_not_available'));
        } else {
            $this->setPopupEnabled(FALSE);

            $this->load->helper('string');

            $this->load->model('Remote_application_model');
            $this->setFeedObject($this->Remote_application_model);
            $this->datagrid->setDefaultOrder('name', 'DESC');

            $this->setDeletable(TRUE);
            $this->setAddable(TRUE);
            $this->setEditable(TRUE);
            $this->setPreviewable(FALSE);

            $this->formbuilder->setRenderer(new FloatingFormRenderer());

            $definition = array(
                'name' => array(
                    'filter_type' => DataGrid::FILTER_BASIC,
                    'validation_rules' => 'required',
                    'show_in_grid' => FALSE,
                ),
                'description' => array(
                    'input_type' => FormBuilder::TEXTAREA,
                    'validation_rules' => 'required',
                ),
                'maintainer_email' => array(
                    'validation_rules' => 'required|valid_email',
                    'input_default_value' => $this->auth->getUserEmail()
                ),
                'api_key' => array(
                    'filter_type' => DataGrid::FILTER_BASIC,
                    'validation_rules' => 'required',
                    'input_is_editable' => FALSE,
                    'input_default_value' => md5(time() . rand(10, 10000) . 'aa')
                ),
                'api_secret' => array(
                    'show_in_grid' => FALSE,
                    'validation_rules' => 'required',
                    'input_is_editable' => FALSE,
                    'input_default_value' => md5(time() . 'secret' . rand(10, 10000))
                ),
                'status' => array(
                    'validation_rules' => 'required',
                    'input_type' => FormBuilder::SELECTBOX,
                    'values' => array('0' => 'Inactive', '1' => 'Active'),
                    'input_default_value' => '1',
                ),
            );


            // Getting translations and setting input groups
            foreach ($definition as $field => &$def) {
                $key = isset($def['field']) ? $def['field'] : $field;

                // Getting label
                if (!isset($def['label'])) {
                    $def['label'] = $this->lang->line($module_name . '_' . $key);
                }

                // Getting description
                if (!isset($def['description'])) {
                    $description = $this->lang->line($module_name . '_' . $key . '_description', FALSE);
                    if ($description !== FALSE) {
                        $def['description'] = $description;
                    }
                }

                // Setting default input group
                if (!isset($def['input_group']) || !$def['input_group']) {
                    $def['input_group'] = 'default';
                }
            }

            $this->setDefinition($definition);
            $this->setMetaOrderField('name', $this->lang->line($lang_field_prefix . 'name'));
            $this->setMetaTitlePattern('{name}');
        }
    }

}
