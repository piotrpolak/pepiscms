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
 * ACL controller
 */
class Acl extends AdminController
{
    function __construct()
    {
        parent::__construct();
        if (!$this->config->item('feature_is_enabled_acl')) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }
        $this->load->language('acl');
        $this->assign('use_extended_utilities_view', true);
        $this->assign('title', $this->lang->line('acl_label_security_policy'));
    }

    /** Callback * */
    protected function renderMenu()
    {
        $this->load->library('MenuRendor');
        return $this->menurendor->render('utilities', 'index', $this->input->getParam('language_code'));
    }

    public function index()
    {
        $this->load->library('ModuleRunner');

        $installed_modules = array('core_modules' => array(), 'userspace_modules' => array());
        $modules = $this->Module_model->getInstalledModules();
        foreach ($modules as $module) {
            if ($this->Module_model->isCoreModule($module->name)) {
                $module_group = 'core_modules';
            } else {
                $module_group = 'userspace_modules';
            }

            $installed_modules[$module_group][] = $module->name;
        }

        $this->assign('installed_modules', $installed_modules);
        $this->display();
    }

    public function edit()
    {
        $this->load->library('SimpleSessionMessage');
        $this->load->library('SecurityPolicy');

        $section = $this->input->getParam('section');
        $is_editable = ($section != 'system' && !$this->Module_model->isCoreModule($section)) || !PEPISCMS_PRODUCTION_RELEASE;

        $this->assign('section', $section);
        $this->assign('is_editable', $is_editable);


        if ($section == 'system') {
            $entities = $this->securitypolicy->getSystemAvailableEntities();
            $controllers = $this->securitypolicy->describeSystemControllers();
            $security_policy = $this->securitypolicy->getSystemSecurityPolicy();
            $policy_save_path = SecurityPolicy::getSystemPolicyPath();
            $section = FALSE;
        } else {
            $entities = $this->securitypolicy->getModuleAvailableEntities($section);
            $controllers = $this->securitypolicy->describeModuleControllers($section);
            $security_policy = $this->securitypolicy->getModuleSecurityPolicy($section);
            $policy_save_path = SecurityPolicy::getModulePolicyPath($section);
        }

        if (isset($_POST) && count($_POST)) {
            $this->load->library('SecurityPolicy');

            $section = $this->input->getParam('section');

            $this->load->library('SecurityPolicyBuilder');


            $policy_entries = array();
            if (isset($_POST['entity'])) {
                foreach ($_POST['entity'] as $controller => $method_to_entity) {
                    foreach ($method_to_entity as $method => $entity) {
                        $access = isset($_POST['access'][$controller][$method]) ? $_POST['access'][$controller][$method] : 'NONE';

                        $policy_entries[] = array('controller' => $controller, 'method' => $method, 'entity' => $entity, 'access' => $access);
                    }
                }
            }

            $xml = $this->securitypolicybuilder->build($section, $policy_entries);

            $is_editable = ($section != 'system' && !$this->Module_model->isCoreModule($section)) || !PEPISCMS_PRODUCTION_RELEASE;
            if (!$is_editable) {
                header('Content-type: application/xml');
                die($xml);
            } else {
                file_put_contents($policy_save_path, $xml);

                SecurityManager::flushCache();
                // Setting the message and redirecting
                $this->load->library('SimpleSessionMessage');
                $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
                $this->simplesessionmessage->setMessage('global_header_success');

                if (!isset($_POST['apply'])) {
                    redirect(admin_url() . 'acl');
                }
                // else simple apply
            }

            // Reading one more time after save
            if ($section == 'system') {
                $entities = $this->securitypolicy->getSystemAvailableEntities();
                $security_policy = $this->securitypolicy->getSystemSecurityPolicy();
            } else {
                $entities = $this->securitypolicy->getModuleAvailableEntities($section);
                $security_policy = $this->securitypolicy->getModuleSecurityPolicy($section);
            }
        }

        $gae = explode(',', $this->input->post('available_entities'));
        foreach ($gae as $entity) {
            $entities[] = trim($entity);
        }

        $entities = array_filter($entities);
        $entities = array_unique($entities);
        $this->assign('entities', $entities);
        $this->assign('security_policy', $security_policy);
        $this->assign('controllers', $controllers);

        $this->assign('title', sprintf($this->lang->line('act_security_policy_for'), $section));
        $this->display();
    }

    public function checkrights()
    {
        $this->load->library('SecurityPolicy');
        $this->assign('controllers', $this->securitypolicy->describeSystemControllers());
        $this->assign('title', $this->lang->line('acl_label_security_policy_check_own_rights'));
        $this->display();
    }
}
