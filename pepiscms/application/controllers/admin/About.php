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
 * CMS about page controller
 */
class About extends AbstractDashboardController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('SimpleSessionMessage');
        $this->load->language('utilities');
        $this->load->model('Siteconfig_model');
    }

    public function index()
    {
        $parsedown = new Parsedown();
        $changelog = $parsedown->text(file_get_contents(APPPATH . '../../CHANGELOG.md'));
        $this->assign('changelog', $changelog)->display();
    }

    public function dashboard()
    {
        $user_manual_path = false;
        if (file_exists('uploads/user-manual.pdf')) {
            $user_manual_path = 'uploads/user-manual.pdf';
        }

        $default_dashboard_element_group = 'dashboard_group_default';

        $dashboard_elements = $this->getDashboardElements();

        $dashboard_elements_builder = SubmenuBuilder::create();
        if ($user_manual_path) {
            $dashboard_elements_builder->addItem()
                ->withLabel($this->lang->line('global_download_user_manual'))
                ->withUrl($user_manual_path)
                ->withTarget('_blank')
                ->withIconUrl('pepiscms/theme/img/about/manual_32.png');
        }

        $dashboard_elements_builder->addItem()
            ->withLabel($this->lang->line('global_reload_privileges'))
                ->withController('login')
                ->withMethod('refresh_session')
                ->withIconUrl('pepiscms/theme/img/utilities/flush_privileges_32.png')
            ->end()
            ->addItem()
                ->withLabel($this->lang->line('global_logout'))
                ->withController('logout')
                ->withMethod('')
                ->withIconUrl('pepiscms/theme/img/about/logout_32.png')
            ->end();

        $dashboard_elements = array_merge($dashboard_elements, $dashboard_elements_builder->build());

        $dashboard_elements_grouped = $this->getDashboardElementsGrouped($dashboard_elements, $default_dashboard_element_group);

        // Doing system tests
        $failed_configuration_tests = array();
        if ($this->auth->isUserRoot()) {
            $failed_configuration_tests = $this->Siteconfig_model->makeConfigurationTestsAngGetFailedTests();
        }

        $this->assign('failed_configuration_tests', $failed_configuration_tests)
            ->assign('dashboard_elements_grouped', $dashboard_elements_grouped)
            ->assign('user_manual_path', $user_manual_path)
            ->assign('widgets', $this->getDashboardWidgetMap())
            ->display();
    }

    /**
     * @return array
     */
    private function getDashboardWidgetMap()
    {
        $this->benchmark->mark('computing_dashboard_widget_map_start');
        $module_names = $this->modulerunner->getInstalledModulesNamesCached();

        $dashboard_widgets = array();
        foreach ($module_names as $module_name) {
            $descriptor = $this->Module_model->getModuleDescriptor($module_name);
            if (!$descriptor) {
                continue;
            }

            $module_dashboard_widgets = $descriptor->getAdminDashboardWidgetsMap($this->lang->getCurrentLanguage());
            if (!is_array($module_dashboard_widgets)) {
                continue;
            }

            $dashboard_widgets = array_merge($dashboard_widgets, $module_dashboard_widgets);
        }

        $this->benchmark->mark('computing_dashboard_widget_map_end');
        return $dashboard_widgets;
    }

    public function configuration_tests()
    {
        $this->load->model('Siteconfig_model');
        $this->assign('adminmenu', '')
            ->assign('failed_configuration_tests', $this->Siteconfig_model->makeConfigurationTestsAngGetFailedTests())
            ->assign('title', $this->lang->line('utilities_label_configuration_tests'))
            ->display();
    }

    public function theme()
    {
        $this->load->library('Google_chart_helper');

        $this->load->library('FormBuilder');
        $this->formbuilder->setTitle('FormBuilder');
        $this->formbuilder->setDefinition(
            array(
                'rtf' => array(
                    'label' => 'RTF',
                    'input_type' => FormBuilder::RTF,
                    'input_default_value' => '<h1>Hello world!</h1><p>Example</p>',
                ),
                'rtf_full' => array(
                    'label' => 'RTF full',
                    'input_type' => FormBuilder::RTF,
                    'input_default_value' => '<h1>Hello world!</h1><p>Example</p>',
                ),
                'min' => array(
                    'label' => 'Minimum value 1',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|numeric|min[1]',
                    'input_default_value' => 'Dummy input',
                ),
                'min' => array(
                    'label' => 'Maximum value 1',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|numeric|max[1]',
                    'input_default_value' => '2',
                ),
                'odd' => array(
                    'label' => 'Odd value',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|numeric|odd',
                    'input_default_value' => '2',
                ),
                'even' => array(
                    'label' => 'Even value',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|numeric|even',
                    'input_default_value' => '1',
                ),
                'phone_number' => array(
                    'label' => 'Phone number',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_phone_number',
                    'input_default_value' => 'Dummy input',
                ),
                'iban' => array(
                    'label' => 'IBAN',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_iban',
                    'input_default_value' => 'Dummy input',
                ),
                'polish_bank_number' => array(
                    'label' => 'Polish bank number',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_polish_bank_number',
                    'input_default_value' => 'Dummy input',
                ),
                'swift_code' => array(
                    'label' => 'SWIFT code',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_swift_code',
                    'input_default_value' => 'Dummy input',
                ),
                'pesel' => array(
                    'label' => 'PESEL',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_pesel',
                    'input_default_value' => 'Dummy input',
                ),
                'bank_number_simple' => array(
                    'label' => 'Bank number (simple)',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_bank_number_simple',
                    'input_default_value' => 'Dummy input',
                ),
                'date' => array(
                    'label' => 'Date',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_date',
                    'input_default_value' => 'Dummy input',
                ),
                'timestamp' => array(
                    'label' => 'Timestamp',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_timestamp',
                    'input_default_value' => 'Dummy input',
                ),
                'imei' => array(
                    'label' => 'IMEI',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|valid_imei',
                    'input_default_value' => 'Dummy input',
                ),
                'no_uppercase' => array(
                    'label' => 'No uppercase',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|no_uppercase',
                    'input_default_value' => 'Dummy input',
                ),
                'no_lowercase' => array(
                    'label' => 'No lowercase',
                    'input_type' => FormBuilder::TEXTFIELD,
                    'validation_rules' => 'required|no_lowercase',
                    'input_default_value' => 'Dummy input',
                ),
            )
        );
        $this->assign('formbuilder', $this->formbuilder->generate())
            ->display();
    }

    public function theme_404()
    {
        show_404("Don't worry, it is just a dummy 404 error page");
    }

    public function theme_error()
    {
        show_error("Don't worry, it is just a dummy error page");
    }

    /**
     * @inheritdoc
     */
    protected function getElements(ModuleDescriptableInterface $descriptor)
    {
        return $descriptor->getAdminDashboardElements($this->lang->getCurrentLanguage());
    }
}
