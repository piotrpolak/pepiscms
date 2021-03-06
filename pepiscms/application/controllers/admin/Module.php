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
 * Module management controller
 */
class Module extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Module_model');
        $this->load->library(array(
            'SimpleSessionMessage',
            'FormBuilder',
            'ConfigBuilder',
            'ModuleRunner',
            'Cachedobjectmanager',
            'User_agent'
        ));
        $this->assign('title', $this->lang->line('modules_installed_modules'));
    }

    /** Callback * */
    protected function renderMenu()
    {
        $this->load->library('MenuRendor');

        $controller = 'utilities';
        $method = 'index';
        $module = false;
        if ($this->input->getMethodName() == 'run') {
            return false;
        }

        if ($this->input->getMethodName() == 'configure') {
            if ($this->input->getParam('render_as_module')) {
                $module = $this->input->getParam('module');
                $method = 'index';
                $controller = $module;
            }
        }

        return $this->menurendor->render($controller, $method, $this->input->getParam('language_code'), false, $module);
    }

    public function index()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);

        if ($view == 'utilities') {
            $this->assign('installed_modules_in_utilities',
                $this->Module_model->getInstalledModulesDisplayedInUtilities());
        } else {
            $this->assign('installed_modules_with_no_parent',
                $this->Module_model->getInstalledModulesHavingNoParent())
                ->assign('installed_modules_with_parrent_grouped_by_parent',
                    $this->Module_model->getInstalledModulesDisplayedInMenuHavingParentGroupedByParent());
        }

        $this->display();
    }

    public function move()
    {
        $direction = $this->input->getParam('direction');
        $module = $this->input->getParam('module');

        $view = $this->_get_view();

        $constraint_field = 'is_displayed_in_' . $view;

        $this->Generic_model->move($module, $direction, $this->config->item('database_table_modules'),
            $constraint_field, 'item_order_' . $view, 'name');

        $this->removeAllCache();

        if ($this->input->getParam('json') == 1) {
            die(json_encode(array('status' => 1, 'message' => 'OK')));
        }

        // Smart redirect
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(admin_url() . 'module');
        }
    }

    public function setup()
    {
        $view = $this->_get_view();

        $notinstalled_modules = array();
        $modules = ModuleRunner::getAvailableModules();

        $installed_modules = $this->Module_model->getInstalledModulesNames();

        foreach ($modules as $module) {
            if (in_array($module, $installed_modules)) {
                continue;
            }
            $notinstalled_modules[] = $module;
        }

        $this->assign('view', $view)
            ->assign('title', $this->lang->line('modules_module_setup'))
            ->assign('modules', $notinstalled_modules)
            ->display();
    }

    public function do_setup()
    {
        $view = $this->_get_view();

        $module = $this->input->getParam('module');
        $is_install = $this->input->getParam('install');

        if (!$module) {
            show_404();
        }

        if ($is_install) {
            $back_url = admin_url() . 'module/setup/view-' . $view;
        } else {
            $back_url = admin_url() . 'module/index/view-' . $view;
        }

        $modules_with_no_parent = array();
        $modules_with_no_parent_tmp = $this->Module_model->getInstalledModulesDisplayedInMenuHavingNoParent();
        $modules_with_no_parent['-1'] = '--';
        foreach ($modules_with_no_parent_tmp as $module_with_no_parent) {
            if ($module_with_no_parent->name == $module) {
                continue;
            }
            $modules_with_no_parent[$module_with_no_parent->module_id] = $this->Module_model->getModuleLabel($module_with_no_parent->name, $this->lang->getCurrentLanguage());
        }

        $is_module_admin_controller_runnable = $this->Module_model->isAdminControllerRunnable($module);

        $is_displayed_in_menu = false;
        $is_displayed_in_utilities = false;


        if ($is_module_admin_controller_runnable) {
            $moduleDescriptor = $this->Module_model->getModuleDescriptor($module);
            if ($moduleDescriptor) {
                $is_displayed_in_menu = $moduleDescriptor->isDisplayedInMenu();
                $is_displayed_in_utilities = $moduleDescriptor->isDisplayedInUtilities();
            }
        }

        $definition = CrudDefinitionBuilder::create()
            ->withField('module')
                ->withInputIsEditable(false)
            ->end()
            ->withField('is_displayed_in_menu')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withNoValidationRules()
                ->withInputDefaultValue($is_displayed_in_menu)
                ->withInputIsEditable($is_module_admin_controller_runnable)
            ->end()
            ->withField('parent_module_id')
                ->withInputType(FormBuilder::SELECTBOX)
                ->withNoValidationRules()
                ->withValues($modules_with_no_parent)
                ->withInputDefaultValue('-1')
                ->withForeignKeyAcceptNull(true)
                ->withInputDefaultValue($is_displayed_in_menu)
                ->withInputIsEditable($is_module_admin_controller_runnable)
            ->end()
            ->withField('is_displayed_in_utilities')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withNoValidationRules()
                ->withInputDefaultValue($is_displayed_in_utilities)
                ->withInputIsEditable($is_module_admin_controller_runnable)
            ->end()
            ->withImplicitTranslations('modules', $this->lang)
            ->build();

        $config_definition = $this->Module_model->getModuleConfigVariables($module);

        if ($config_definition) {
            foreach ($config_definition as $key => $config) {
                $config['input_group'] = 'Additional configuration variables';
                $key = 'config_' . $key;
                $definition[$key] = $config;
            }
        }

        $this->formbuilder->setId($module)
            ->setBackLink($back_url)
            ->setTitle($this->lang->line('modules_module_setup'))
            ->setCallback(array($this, '_fb_callback_setup_on_save'), FormBuilder::CALLBACK_ON_SAVE)
            ->setCallback(array($this, '_fb_callback_setup_on_read'), FormBuilder::CALLBACK_ON_READ)
            ->setDefinition($definition);

        $this->assign('view', $view)
            ->assign('is_module_admin_controller_runnable', $is_module_admin_controller_runnable)
            ->assign('title', $this->lang->line('modules_module_setup'))
            ->assign('module_label', $this->Module_model->getModuleLabel($module,
                $this->lang->getCurrentLanguage()))
            ->assign('module', $module)
            ->assign('form', $this->formbuilder->generate())
            ->display();
    }

    public function configure()
    {
        $module = $this->input->getParam('module');

        $render_as_module = $this->input->getParam('render_as_module');

        if (!$module || !$this->Module_model->isInstalled($module)) {
            show_404();
        }

        $config_definition = $this->Module_model->getModuleConfigVariables($module);

        if ($config_definition) {
            foreach ($config_definition as $key => $config) {
                $key = 'config_' . $key;
                $definition[$key] = $config;
            }
        }

        $back_url = false;
        if (!$render_as_module) {
            $back_url = admin_url() . 'utilities/';
        }

        $this->formbuilder->setId($module)
            ->setBackLink($back_url)
            ->setTitle($this->lang->line('modules_module_setup'))
            ->setCallback(array($this, '_fb_callback_setup_on_save_config_only'), FormBuilder::CALLBACK_ON_SAVE)
            ->setCallback(array($this, '_fb_callback_setup_on_read_config_only'), FormBuilder::CALLBACK_ON_READ)
            ->setDefinition($definition)
            ->setApplyButtonEnabled(true)
            ->setSubmitButtonEnabled(!$render_as_module);


        $this
            ->assign('title', $this->lang->line('modules_module_setup'))
            ->assign('module_label', $this->Module_model->getModuleLabel($module,
                $this->lang->getCurrentLanguage()))
            ->assign('module', $module)
            ->assign('render_as_module', $render_as_module)
            ->assign('form', $this->formbuilder->generate())
            ->display();
    }

    public function uninstall()
    {
        $view = $this->_get_view();
        $this->assign('view', $view);

        $module = $this->uri->segment(4);

        Logger::info('Uninstalling module ' . $module, 'MODULE');

        $this->Module_model->uninstall($module);
        $this->removeAllCache();

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');

        // Smart redirect
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(admin_url() . 'module/index/view-' . $view);
        }
    }

    public function run()
    {
        $module_name = $this->uri->segment(4);
        $method = $this->uri->segment(5);

        if (!$method) {
            $method = 'index';
        }

        try {
            if (!$this->modulerunner->runAdminModule($module_name, $method)) {
                show_404();
            }
        } catch (\PiotrPolak\PepisCMS\Security\AccessDeniedException $exception) {
            Logger::warning('Security policy violation for module ' . $module_name . '/' . $method, 'SECURITY');
            $this->display('admin/no_sufficient_priviliges');
        }
    }

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

        $this->_fb_callback_setup_on_read_config_only($object);
    }

    public function _fb_callback_setup_on_read_config_only(&$object)
    {
        $config_variables = $this->Siteconfig_model->getPairsForModule($this->formbuilder->getId());
        $config_variables_fs = $this->configbuilder->readConfig($this->getConfigPath($this->formbuilder->getId()));

        if (is_array($config_variables_fs)) {
            $config_variables = array_merge($config_variables_fs, $config_variables);
        }

        foreach ($config_variables as $key => $value) {
            $key = 'config_' . $key;
            $object->{$key} = $value;
        }
    }

    /**
     * Must overwrite the save procedure and return true or false
     *
     * @param object $data
     * @return boolean
     */
    public function _fb_callback_setup_on_save(&$data)
    {
        $is_install = $this->input->getParam('install') == 1;
        $module_name = $this->formbuilder->getId();

        if (empty($data['is_displayed_in_menu']) || $data['parent_module_id'] < 1) {
            $data['parent_module_id'] = null;
        }

        if ($is_install) {
            $success = $this->Module_model->install($module_name, $data['is_displayed_in_menu'],
                $data['is_displayed_in_utilities'], $data['parent_module_id']);
        } else {
            $success = $this->Module_model->update($module_name, $data['is_displayed_in_menu'],
                $data['is_displayed_in_utilities'], $data['parent_module_id']);
        }

        $this->_fb_callback_setup_on_save_config_only($data);

        return $success;
    }


    public function _fb_callback_setup_on_save_config_only(&$data)
    {
        $module_name = $this->formbuilder->getId();

        $config_variables = $this->filterConfigData($data);

        if (count($config_variables) > 0) {
            $this->saveModuleConfig($config_variables, $module_name);
        }
        $this->removeAllCache();

        return true;
    }

    /**
     * @param $module_name
     * @return string
     */
    private function getConfigPath($module_name)
    {
        $config_path = INSTALLATIONPATH . 'application/config/modules/';
        if (!file_exists($config_path)) {
            mkdir($config_path);
        }
        $config_path .= $module_name . '.php';
        return $config_path;
    }

    /**
     * Returns view type
     *
     * @return string
     */
    private function _get_view()
    {
        if (in_array($this->input->getParam('view'), array('menu', 'utilities'))) {
            return $this->input->getParam('view');
        }

        return 'menu';
    }

    /**
     * @param $array
     * @param $module_name
     * @return mixed
     */
    private function saveModuleConfig($array, $module_name)
    {
        foreach ($array as $key => $value) {
            $this->Siteconfig_model->saveConfigByName($key, $value, $module_name);
        }

        Logger::info('Configuring module ' . $module_name, 'MODULE');
    }

    private function removeAllCache()
    {
        $this->db->cache_delete_all();
        $this->cachedobjectmanager->cleanup();
        $this->auth->refreshSession();
    }

    /**
     * @param $data
     * @return array
     */
    private function filterConfigData(&$data)
    {
        $config_data = array();
        foreach ($data as $key => $value) {
            if (strpos($key, 'config_') !== 0) {
                continue;
            }
            $config_data[substr($key, 7)] = $value;
        }
        return $config_data;
    }
}
