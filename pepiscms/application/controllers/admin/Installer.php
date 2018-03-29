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
 * System installer controller
 */
class Installer extends AdminController
{
    /**
     * GUI steps
     *
     * @var array
     */
    private $steps = array();
    private $application_types = array();

    private static $default_modules_installed_in_utilities = array(
        'sqlconsole',
        'cms_groups',
        'cms_users',
        'development',
        'logs',
        'symfony2_bridge',
        'translator'
    );

    /**
     * Path to Symfony2 configuration file
     * @var string
     */
    private $symfony_database_file_path = '../../app/config/parameters.yml';

    /**
     * Default constructor
     */
    public function __construct()
    {
        // Do not render menu and skip authorization
        parent::__construct(false, true);

        $this->load->language('installer');
        $this->load->library('Installer_helper');

        if (self::is_already_configured(INSTALLATIONPATH) && !isset($_SESSION['cms_installation'])) {
            show_error($this->lang->line('installer_application_already_configured'));
        }

        $this->auth->unsetSession();

        $this->assign('title', $this->lang->line('installer_module_name'));

        $this->load->library('FormBuilder');
        $this->formbuilder->setSubmitLabel($this->lang->line('global_button_next'));

        $this->application_types = array(
            Installer_helper::DEFAULT_INTRANET_APPLICATION_NO_FRONTEND => $this->lang->line('installer_cms_type_default_intranet_application_no_frontend'),
            Installer_helper::INTRANET_APPLICATION_WITH_FRONTEND_SUPPORT => $this->lang->line('installer_cms_type_intranet_application_with_frontend_support'),
            Installer_helper::FULL_FEATURED_WEB_CMS => $this->lang->line('installer_cms_type_full_cms'),
        );

        $this->steps = array(
            'database' => array(
                'active' => false,
                'description' => '',
                'name' => $this->lang->line('installer_database_connection')
            ),
            'auth' => array(
                'active' => false,
                'description' => '',
                'name' => $this->lang->line('installer_auth_driver')
            ),
            'account' => array(
                'active' => false,
                'description' => '',
                'name' => $this->lang->line('installer_administrator_account')
            ),
            'features' => array(
                'active' => false,
                'description' => '',
                'name' => $this->lang->line('installer_features')
            ),
            'installed_modules' => array(
                'active' => false,
                'description' => '',
                'name' => $this->lang->line('installer_installed_modules')
            ),
            'build_success' => array(
                'active' => false,
                'description' => '',
                'name' => $this->lang->line('installer_build_success')
            ),
        );

        // Initializes default values
        $this->merge_installation_data(array());
    }

    /**
     * Helper returning project name based on URL
     *
     * @return string
     */
    private function _get_project_name()
    {
        $url_string = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($url_string[1]) && $url_string[1]) {
            $url_string = explode('.', $url_string[1]);
            if (isset($url_string[0]) && $url_string[0]) {
                return ucwords(str_replace(array('_', '-'), ' ', $url_string[0]));
            }
        }

        return 'PepisCMS website';
    }

    /**
     * Helper returning project top domain
     *
     * @return string
     */
    private function _get_top_domain()
    {
        $domain_elements = explode('.', $_SERVER['SERVER_NAME']);
        if (count($domain_elements) <= 2) {
            return $_SERVER['SERVER_NAME'];
        } else {
            $count_domain_elements = count($domain_elements);
            return $domain_elements[$count_domain_elements - 2] . '.' . $domain_elements[$count_domain_elements - 1];
        }
    }


    /**
     * Index page
     */
    public function index()
    {
        $this->installer_helper->buildFileStructure(INSTALLATIONPATH);

        // When the Symfony2 configuration is not present, display the native connection only
        $database_config_types = array(
            'native' => $this->lang->line('installer_database_config_type_native'),
            'symfony_import' => $this->lang->line('installer_database_config_type_symfony_import')
        );

        if (!$this->is_symfony_config_present()) {
            unset($database_config_types['symfony_import']);
        }

        $this->formbuilder->setTitle($this->lang->line('installer_database_connection'));
        $that = $this;

        $this->formbuilder->setCallback(function ($data_array) use ($that) {
            $that->merge_installation_data($data_array);

            if ($data_array['database_config_type'] != 'symfony_import') {
                // Default connection
                list($hostname, $port) = $that->installer_helper->explodeHostname($that->get_installation_data('hostname'));
                $username = $that->get_installation_data('username');
                $password = $that->get_installation_data('password');
                $database = $that->get_installation_data('database');
            } else {
                // Symfony connection
                $conf = array();
                if (!file_exists($that->symfony_database_file_path)) {
                    show_error(sprintf($that->lang->line('installer_database_config_file_not_found'), $that->symfony_database_file_path));
                }
                $values = file_get_contents($that->symfony_database_file_path);
                $values = explode("\n", $values);
                array_walk($values, 'trim');
                foreach ($values as $row) {
                    @list($key, $value) = explode(':', $row);
                    $key = trim($key);
                    $value = trim($value);
                    if (!$key) {
                        continue;
                    }
                    $conf[$key] = $value;
                }

                $hostname = $conf['database_host'];
                $username = $conf['database_user'];
                $password = trim($conf['database_password'], "'\"");
                $database = $conf['database_name'];
                $port = '';
            }

            $link = @mysqli_connect($hostname, $username, $password, $database, $port);
            if (mysqli_connect_errno($link)) {
                $that->formbuilder->setValidationErrorMessage(sprintf($that->lang->line('installer_unable_to_establish_connection_to_database'), mysqli_connect_error()));
                return false;
            }

            redirect(admin_url() . 'installer/auth_driver');
            return true;
        }, FormBuilder::CALLBACK_ON_SAVE);

        $validation_rules = isset($_POST['database_config_type']) && $_POST['database_config_type'] != 'cas' ? '' : 'required';

        $definition = array(
            'database_config_type' => array(
                'input_type' => FormBuilder::SELECTBOX,
                'values' => $database_config_types,
            ),
            'hostname' => array(
                'validation_rules' => $validation_rules,
            ),
            'username' => array(
                'validation_rules' => $validation_rules,
            ),
            'password' => array(
                'validation_rules' => '',
            ),
            'database' => array(
                'validation_rules' => $validation_rules,
            ),
        );
        $definition = $this->merge_definition_with_session_data($definition);
        $this->formbuilder->setDefinition($definition);

        $this->steps['database']['active'] = true;
        $this->assign('steps', $this->steps);
        $this->display();
    }


    /**
     * Auth driver select
     */
    public function auth_driver()
    {
        $auth_drivers = array(
            'native' => $this->lang->line('installer_auth_driver_native')
        );

        if ($this->auth->isAuthDriverEnabled('cas')) {
            $auth_drivers['cas'] = $this->lang->line('installer_auth_driver_cas');
        }

        $this->formbuilder->setTitle($this->lang->line('installer_auth_driver'));
        $that = $this;
        $this->formbuilder->setCallback(function ($data_array) use ($that) {
            $that->merge_installation_data($data_array);
            redirect(admin_url() . 'installer/admin_account');
        }, FormBuilder::CALLBACK_ON_SAVE);

        $validation_rules = isset($_POST['auth_driver']) && $_POST['auth_driver'] != 'cas' ? '' : 'required';

        $definition = array(
            'authentification_driver' => array(
                'input_type' => FormBuilder::SELECTBOX,
                'values' => $auth_drivers,
            ),
            'cas_server' => array(
                'input_default_value' => 'logowanie.' . $this->_get_top_domain(),
                'validation_rules' => $validation_rules,
            ),
            'cas_port' => array(
                'input_default_value' => '443',
                'validation_rules' => $validation_rules,
            ),
            'cas_path' => array(
                'input_default_value' => '/cas',
                'validation_rules' => $validation_rules,
            ),
        );
        $definition = $this->merge_definition_with_session_data($definition);
        $this->formbuilder->setDefinition($definition);

        $this->steps['auth']['active'] = true;
        $this->assign('steps', $this->steps);
        $this->display();
    }


    /**
     * User account (for native auth driver only)
     */
    public function admin_account()
    {
        if ($this->get_installation_data('authentification_driver') != 'native') {
            redirect(admin_url() . 'installer/features');
        }

        $this->formbuilder->setTitle($this->lang->line('installer_administrator_account'));
        $that = $this;
        $this->formbuilder->setCallback(function ($data_array) use ($that) {
            if ($data_array['admin_password'] != $data_array['admin_password_confirm']) {
                $that->formbuilder->setValidationErrorMessage($this->lang->line('installer_passwords_must_match'));
                return false;
            }

            $that->merge_installation_data($data_array);
            redirect(admin_url() . 'installer/features');

            return true;
        }, FormBuilder::CALLBACK_ON_SAVE);

        $definition = array(
            'admin_email' => array(),
            'admin_password' => array(
                'input_type' => FormBuilder::PASSWORD,
                'validation_rules' => 'required|min_length[6]'
            ),
            'admin_password_confirm' => array(
                'input_type' => FormBuilder::PASSWORD,
            ),
        );

        $definition = $this->merge_definition_with_session_data($definition);
        $this->formbuilder->setDefinition($definition);

        $this->steps['account']['active'] = true;
        $this->assign('steps', $this->steps);
        $this->display();
    }


    /**
     * CMS features and language
     */
    public function features()
    {
        $_LANG_DIR = dirname(realpath(__FILE__)) . '/../../language/';
        $_THEMES_DIR = INSTALLATIONPATH . 'theme/';

        $files = scandir($_LANG_DIR);
        $languages = array();
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || !is_dir($_LANG_DIR . $file) || $file[0] == '.') {
                continue;
            }
            $languages[$file] = $file;
        }

        $files = scandir($_THEMES_DIR);
        $themes = array();
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || !is_dir($_THEMES_DIR . $file) || $file[0] == '.') {
                continue;
            }
            $themes[$file] = $file;
        }

        $this->formbuilder->setTitle($this->lang->line('installer_features'));

        $that = $this;
        $this->formbuilder->setCallback(function ($data_array) use ($that) {
            $that->merge_installation_data($data_array);
            redirect(admin_url() . 'installer/build');
        }, FormBuilder::CALLBACK_ON_SAVE);

        $definition = array(
            'site_name' => array(
                'input_default_value' => $this->_get_project_name(),
            ),
            'site_email' => array(
                'validation_rules' => 'required|valid_email',
                'input_default_value' => 'noreply@' . $this->_get_top_domain(),
            ),
            'cms_instance_type' => array(
                'input_type' => FormBuilder::SELECTBOX,
                'values' => $this->application_types,
            ),
            'available_languages' => array(
                'input_type' => FormBuilder::MULTIPLECHECKBOX,
                'values' => $languages,
                'input_default_value' => $languages
            ),
            'default_language' => array(
                'input_type' => FormBuilder::SELECTBOX,
                'values' => $languages
            ),
            'default_theme' => array(
                'input_type' => FormBuilder::SELECTBOX,
                'values' => $themes
            ),
        );

        $definition = $this->merge_definition_with_session_data($definition);
        $this->formbuilder->setDefinition($definition);

        $this->steps['features']['active'] = true;
        $this->assign('steps', $this->steps);
        $this->display();
    }

    /**
     * Build action
     */
    public function build()
    {
        $error = $this->installer_helper->writeConfigFiles($_SESSION['cms_installation'], INSTALLATIONPATH);

        if (!$error) {
            $error = $this->installer_helper->writeDatabase($_SESSION['cms_installation']);
        }

        if ($this->get_installation_data('admin_email')) {
            $this->installer_helper->registerAdmin($this->get_installation_data('admin_email'), $this->get_installation_data('admin_password'));
        }

        if ($error) {
            show_error($error);
        }

        redirect(admin_url() . 'installer/installed_modules');
    }


    /**
     * Specify default modules
     */
    public function installed_modules()
    {
        $this->formbuilder->setTitle($this->lang->line('installer_installed_modules'));
        $that = $this;
        $this->formbuilder->setCallback(function ($data_array) use ($that) {
            $that->load->model('Module_model');
            $modules = ModuleRunner::getAvailableModules();
            foreach ($modules as $module) {
                if (!$that->input->post($module . '__is_installed_menu') && !$that->input->post($module . '__is_installed_utilities')) {
                    continue;
                }

                $that->Module_model->install($module, $that->input->post($module . '__is_installed_menu') == 1, $that->input->post($module . '__is_installed_utilities') == 1);
            }

            redirect(admin_url() . 'installer/build_success');
        }, FormBuilder::CALLBACK_ON_SAVE);

        $modules = ModuleRunner::getAvailableModules();

        $definition = array();
        foreach ($modules as $module) {
            $input_group = str_replace('_', ' ', ucfirst($module));

            $definition[$module . '__is_installed_menu'] = array(
                'input_type' => FormBuilder::CHECKBOX,
                'input_group' => $input_group,
                'label' => $this->lang->line('installer_installed_in_menu'),
                'validation_rules' => '',
                'input_default_value' => 0,
            );

            $definition[$module . '__is_installed_utilities'] = array(
                'input_type' => FormBuilder::CHECKBOX,
                'input_group' => $input_group,
                'label' => $this->lang->line('installer_installed_in_utilities'),
                'validation_rules' => '',
                'input_default_value' => in_array($module, self::$default_modules_installed_in_utilities),
            );
        }

        $definition = $this->merge_definition_with_session_data($definition);
        $this->formbuilder->setDefinition($definition);

        $this->steps['installed_modules']['active'] = true;
        $this->assign('steps', $this->steps);
        $this->display();
    }

    /**
     * Success message page
     */
    public function build_success()
    {
        $this->steps['build_success']['active'] = true;
        $this->assign('steps', $this->steps);
        $this->display();
    }

    /**
     * Automatically authenticates the user
     */
    public function go_to_admin()
    {
        $this->load->library('Auth');
        $user_id = $this->User_model->getUserIdByEmail($this->get_installation_data('admin_email'));
        $this->auth->forceLogin($user_id);

        unset($_SESSION['cms_installation']);

        redirect(admin_url());
    }

    /**
     * Tells whether Symfony2 config file is present
     *
     * @return bool
     */
    private function is_symfony_config_present()
    {
        return file_exists($this->symfony_database_file_path);
    }

    /**
     * Merges existing session data with the new values
     *
     * @param $data
     */
    private function merge_installation_data($data)
    {
        if (!isset($_SESSION['cms_installation'])) {
            $_SESSION['cms_installation'] = $this->installer_helper->getDefaultInstallationValues();
        }

        foreach ($data as $key => $value) {
            $_SESSION['cms_installation'][$key] = $value;
        }
    }

    /**
     * Returns installation key value
     *
     * @param $key
     * @return null
     */
    private function get_installation_data($key)
    {
        if (isset($_SESSION['cms_installation'][$key])) {
            return $_SESSION['cms_installation'][$key];
        }

        return null;
    }

    /**
     * Merges FileBuilder definition with session data (input_default_value)
     *
     * @param $definition
     * @return mixed
     */
    private function merge_definition_with_session_data($definition)
    {
        foreach ($definition as $key => $value) {
            $session_value = $this->get_installation_data($key);
            if (!$session_value) {
                continue;
            }
            $definition[$key]['input_default_value'] = $session_value;
        }

        return $definition;
    }

    /**
     * Tells whether the application is already configured
     *
     * @param $base_path
     * @return bool
     */
    private static function is_already_configured($base_path)
    {
        return file_exists($base_path . 'application/config/config.php');
    }
}
