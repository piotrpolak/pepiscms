<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Class Html_customizationAdmin
 */
class Html_customizationAdmin extends ModuleAdminController
{
    public function index()
    {
        /* Getting module name from class name */
        $module_name = strtolower(str_replace('Admin', '', __CLASS__));

        /* Loading module language, model, libraries, helpers */
        $this->load->language($module_name);
        $this->load->model('Html_customization_model');

        $this->assign('title', $this->lang->line('html_customization_module_name'));
        $this->load->library('FormBuilder');

        $possible_fields = array('head_prepend', 'head_append', 'body_prepend', 'body_append');
        $states = array('logged_in', 'not_logged_in');

        $definition = array();

        foreach ($possible_fields as $possible_field) {
            foreach ($states as $state) {
                $field_name = 'html_customization_' . $state . '_' . $possible_field;
                $definition[$field_name] = array(
                    'label' => $this->lang->line($module_name . '_html_customization_' . $possible_field),
                    'validation_rules' => '',
                    'input_type' => FormBuilder::TEXTAREA,
                    'input_group' => 'html_customization_' . $state,
                );
            }
        }

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

        $this->formbuilder->setId('1');
        $this->formbuilder->setBackLink(admin_url() . 'utilities');
        $this->formbuilder->setApplyButtonEnabled();
        $this->formbuilder->setFeedObject($this->Html_customization_model);
        $this->formbuilder->setDefinition($definition);

        $this->assign('form', $this->formbuilder->generate());
        $this->display();
    }
}
