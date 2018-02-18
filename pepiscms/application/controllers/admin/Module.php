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
 * Module management controller
 */
class Module extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Module_model');
        $this->load->library('SimpleSessionMessage');
        $this->load->library('FormBuilder');
        $this->assign('title', $this->lang->line('label_installed_modules'));
    }

    private function _get_view()
    {
        $view = 'menu';
        if (in_array($this->input->getParam('view'), array('menu', 'utilities'))) {
            $view = $this->input->getParam('view');
        }

        return $view;
    }

    /** Callback * */
    protected function renderMenu()
    {
        $this->load->library('MenuRendor');

        $controller = 'utilities';
        $method = 'index';
        if ($this->input->getMethodName() == 'run') {
            return FALSE;
        }

        return $this->menurendor->render($controller, $method, $this->input->getParam('language_code'));
    }

    public function index()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);

        if ($view == 'utilities') {
            $this->assign('installed_modules_in_utilities', $this->Module_model->getInstalledModulesDisplayedInUtilities());
        } else {
            $this->assign('installed_modules_with_no_parent', $this->Module_model->getInstalledModulesHavingNoParent());
            $this->assign('installed_modules_with_parrent_grouped_by_parent', $this->Module_model->getInstalledModulesDisplayedInMenuHavingParentGroupedByParent());
        }

        $this->display();
    }

    public function move()
    {
        $direction = $this->input->getParam('direction');
        $module = $this->input->getParam('module');

        $view = $this->_get_view();

        $constraint_field = 'is_displayed_in_' . $view;

        $this->Generic_model->move($module, $direction, $this->config->item('database_table_modules'), $constraint_field, 'item_order_' . $view, 'name');

        $this->auth->refreshSession();
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();
        ModuleRunner::flushCache();

        if ($this->input->getParam('json') == 1) {
            die('{ "status": "1", "message" : "OK" }'); // TODO Serialize
        }


        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(admin_url() . 'module');
        }
    }

    public function setup()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);
        $this->assign('title', $this->lang->line('label_module_setup'));

        $this->load->model('Module_model');
        $this->load->library('ModuleRunner');

        $notinstalled_modules = array();
        $modules = ModuleRunner::getAvailableModules();

        $installed_modules = $this->Module_model->getInstalledModulesNames();

        foreach ($modules as $module) {
            if (in_array($module, $installed_modules)) {
                continue;
            }
            $notinstalled_modules[] = $module;
        }

        $this->assign('modules', $notinstalled_modules);

        $this->display();
    }

    public function do_setup()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);
        $this->assign('title', $this->lang->line('label_module_setup'));

        $module = $this->input->getParam('module');
        $is_install = $this->input->getParam('install');

        if (!$module) {
            show_404();
        }

        $this->load->library('FormBuilder');
        if ($is_install) {
            $this->formbuilder->setBackLink(admin_url() . 'module/setup/view-' . $view);
        } else {
            $this->formbuilder->setBackLink(admin_url() . 'module/index/view-' . $view);
        }

        $modules_with_no_parent = array();
        $modules_with_no_parent_tmp = $this->Module_model->getInstalledModulesDisplayedInMenuHavingNoParent();
        $modules_with_no_parent[null] = '--';
        foreach ($modules_with_no_parent_tmp as $module_with_no_parent) {
            if ($module_with_no_parent->name == $module) {
                continue;
            }
            $modules_with_no_parent[$module_with_no_parent->module_id] = $this->Module_model->getModuleLabel($module_with_no_parent->name, $this->lang->getCurrentLanguage());
        }

        $this->formbuilder->setId($module);
        $this->formbuilder->setTitle($this->lang->line('label_module_setup'));
        $this->formbuilder->setCallback(array($this, '_fb_callback_setup_on_save'), FormBuilder::CALLBACK_ON_SAVE);
        $this->formbuilder->setCallback(array($this, '_fb_callback_setup_on_read'), FormBuilder::CALLBACK_ON_READ);
        $this->formbuilder->setDefinition(
            array(
                'module' => array(
                    'label' => $this->lang->line('label_module'),
                    'validation_rules' => 'required',
                    'input_is_editable' => FALSE,
                ),
                'is_displayed_in_menu' => array(
                    'input_type' => FormBuilder::CHECKBOX,
                    'validation_rules' => '',
                    'label' => $this->lang->line('label_display_in_main_menu'),
                ),
                'parent_module_id' => array(
                    'input_type' => FormBuilder::SELECTBOX,
                    'validation_rules' => '',
                    'input_is_editable' => TRUE,
                    'values' => $modules_with_no_parent,
                    'foreign_key_accept_null' => TRUE,
                    'label' => $this->lang->line('label_module_parent_module_id'),
                ),
                'is_displayed_in_utilities' => array(
                    'input_type' => FormBuilder::CHECKBOX,
                    'validation_rules' => '',
                    'label' => $this->lang->line('label_display_in_utilities'),
                ),
            )
        );
        $this->assign('module_label', $this->Module_model->getModuleLabel($module, $this->lang->getCurrentLanguage()));
        $this->assign('module', $module);

        $this->assign('form', $this->formbuilder->generate());
        $this->display();
    }

    /**
     * Must populate object
     * @param object $object
     */
    public function _fb_callback_setup_on_read(&$object)
    {
        $object = $this->Module_model->getInfoByName($this->formbuilder->getId());
        if (!$object) {
            $object = new stdClass;
        }
        $object->module = $this->formbuilder->getId();
        if (!isset($object->label) || !$object->label) {
            $object->label = str_replace('_', ' ', ucfirst($this->formbuilder->getId()));
        }
        // TODO Get descriptor
    }

    /**
     * Must overwrite the save procedure and return true or false
     *
     * @param object $data
     * @return boolean
     */
    public function _fb_callback_setup_on_save(&$data)
    {
        $is_install = ($this->input->getParam('install') == 1);
        $module = $this->formbuilder->getId();

        if (!$data['is_displayed_in_menu']) {
            $data['parent_module_id'] = NULL;
        }

        if ($is_install) {
            $success = $this->Module_model->install($module, $data['is_displayed_in_menu'], $data['is_displayed_in_utilities'], $data['parent_module_id']);
        } else {
            $success = $this->Module_model->update($module, $data['is_displayed_in_menu'], $data['is_displayed_in_utilities'], $data['parent_module_id']);
        }

        $this->auth->refreshSession();
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();
        ModuleRunner::flushCache();

        return $success;
    }

    public function uninstall()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);

        $module = $this->uri->segment(4);

        Logger::info('Uninstalling module ' . $module, 'MODULE');

        $this->load->library('ModuleRunner');
        $this->load->model('Module_model');
        $this->Module_model->uninstall($module);
        $this->auth->refreshSession();
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();
        ModuleRunner::flushCache();

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(admin_url() . 'module/index/view-' . $view);
        }
    }

    public function configure()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);

        $module_name = $this->uri->segment(4);
        $back_to_module = $this->input->getParam('back_to_module');

        $config_path = INSTALLATIONPATH . 'application/config/modules/';
        if (!file_exists($config_path)) {
            mkdir($config_path);
        }
        $config_path .= $module_name . '.php';

        $this->load->library('ConfigBuilder');

        $definition = $this->Module_model->getModuleConfigVariables($module_name);
        $values = $this->configbuilder->readConfig($config_path);

        if (!is_array($definition) || !count($definition)) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_WARNING);
            $this->simplesessionmessage->setMessage('module_not_configurable');

            // Smart redirect
            $this->load->library('User_agent');
            if ($this->agent->referrer()) {
                redirect($this->agent->referrer());
            } else {
                redirect(admin_url() . 'module');
            }
        }

        foreach ($definition as $field_name => &$field_definition) {
            if (!isset($values[$field_name])) {
                continue;
            }
            $field_definition['input_default_value'] = $values[$field_name];
        }

        if ($back_to_module) {
            $this->formbuilder->setBackLink(module_url($module_name));
        } else {
            $this->formbuilder->setBackLink(admin_url() . 'module/index/view-' . $view);
        }

        $this->formbuilder->setApplyButtonEnabled();
        $this->formbuilder->setSubmitButtonEnabled(FALSE);
        $this->formbuilder->setId('NULL');
        $this->formbuilder->setTitle($this->lang->line('label_module_setup'));
        $this->formbuilder->setDefinition($definition);
        $this->formbuilder->setCallback(array($this, '_fb_callback_on_save'), FormBuilder::CALLBACK_ON_SAVE);

        $this->assign('form', $this->formbuilder->generate());
        $this->assign('module', $module_name);
        $this->display();
    }

    /**
     * Must overwrite the save procedure and return true or false
     *
     * @param object $array
     * @return boolean
     */
    public function _fb_callback_on_save(&$array)
    {
        $module_name = $this->uri->segment(4);
        $config_path = INSTALLATIONPATH . 'application/config/modules/';
        if (!file_exists($config_path)) {
            mkdir($config_path);
        }
        $config_path .= $module_name . '.php';

        Logger::info('Configuring module ' . $module_name, 'MODULE');
        return $this->configbuilder->writeConfig($config_path, $array);
    }

    public function run()
    {
        $module_name = $this->uri->segment(4);
        $method = $this->uri->segment(5);

        if (!$method) {
            $method = 'index';
        }

        $this->load->model('Module_model');
        $this->load->library('ModuleRunner');

        if (!$this->modulerunner->runAdminModule($this, $module_name, $method)) {
            show_404();
        }
    }

}