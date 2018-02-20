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
 * Utilities for development
 */
class DevelopmentAdmin extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->moduleLanguage('development');
        $this->assign('title', $this->lang->line('development_module_name'));
    }

    public function index()
    {
        $this->display();
    }

    public function fix_autoincrement_on_cms_tables()
    {
        $tables = array(
            'database_table_users' => array('user_id', 'INT UNSIGNED'),
            'database_table_group_to_entity' => array('id', 'INT'),
            'database_table_user_to_group' => array('id', 'INT'),
            'database_table_groups' => array('group_id', 'INT UNSIGNED'),
            'database_table_modules' => array('module_id', 'INT'),
            'database_table_logs' => array('id', 'BIGINT UNSIGNED'),
            'database_table_menu' => array('item_id', 'INT UNSIGNED'),
            'database_table_pages' => array('page_id', 'INT UNSIGNED'),
            'database_table_journal' => array('id', 'INT'),
        );

        $tables_in_database = $this->db->list_tables();

        $errors = array();
        $i = 0;
        foreach ($tables as $table => $key) {
            if ($this->config->item($table) && in_array($this->config->item($table), $tables_in_database)) {
                $sql1 = 'ALTER TABLE ' . ($this->config->item($table)) . ' MODIFY ' . ($key[0]) . ' ' . $key[1] . ' NOT NULL';
                if (!$this->db->query($sql1)) {
                    $error = $this->db->error();
                    $errors[] = $error['code'] . ':' . $error['message'];
                }

                $sql0 = 'ALTER TABLE ' . $this->config->item($table) . ' DROP PRIMARY KEY';
                if (!$this->db->query($sql0)) {
                    $error = $this->db->error();
                    $errors[] = $error['code'] . ':' . $error['message'];
                }
            }
        }

        $this->db->close();
        $this->db->reconnect();

        foreach ($tables as $table => $key) {
            if ($this->config->item($table) && in_array($this->config->item($table), $tables_in_database)) {
                $sql2 = 'ALTER TABLE ' . ($this->config->item($table)) . ' MODIFY ' . ($key[0]) . ' ' . $key[1] . ' NOT NULL AUTO_INCREMENT PRIMARY KEY';
                //echo $sql2."\n";


                if ($this->db->query($sql2)) {
                    $i++;
                } else {
                    $error = $this->db->error();
                    $errors[] = $error['code'] . ':' . $error['message'];
                }
            }
        }

        if ($errors) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR);
            $this->simplesessionmessage->setMessage(implode('<br><br>', $errors));
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            $this->simplesessionmessage->setMessage(sprintf($this->lang->line('development_fixed_tables'), $i));
        }


        redirect(module_url());
    }

    public function generate_header_file()
    {
        $core_libraries_paths = '{' . BASEPATH . 'libraries/*.php,' . BASEPATH . 'core/*.php}'; // Files that should be prefixed with CI_
        $library_paths = '{' . APPPATH . 'libraries/*.php,modules/*/libraries/*.php,application/libraries/*.php}';
        $model_paths = '{' . APPPATH . 'models/*.php,modules/*/models/*.php,application/models/*.php}';

//		$core_libraries_paths	= '{}'; // Files that should be prefixed with CI_
//		$library_paths			= '{modules/*/libraries/*.php}';
//		$model_paths			= '{modules/*/models/*.php}';

        $core_library_files = glob($core_libraries_paths, GLOB_BRACE);
        $library_files = glob($library_paths, GLOB_BRACE);
        $model_files = glob($model_paths, GLOB_BRACE);


        $model_names = $library_names = $core_library_names = array();
        foreach ($model_files as $path) {
            $name = pathinfo($path);
            $model_names[] = ucfirst($name['filename']);
        }
        foreach ($library_files as $path) {
            $name = pathinfo($path);
            $library_names[] = $name['filename'];
        }
        foreach ($core_library_files as $path) {
            $name = pathinfo($path);
            $core_library_names[] = $name['filename'];
        }

        //var_dump( $model_names );
        //die();

        $output = "<?php die('This is automatically generated file and should not be runned nor included');\n/**\n";
        $output .= " * @property CI_DB_active_record \$db\n";
        $output .= " * @property CI_Loader \$load\n";
        foreach ($model_names as $name) {
            if (!$name) {
                continue;
            }
            $output .= " * @property " . ucfirst($name) . ' $' . $name . "\n";
        }

        $output .= " *\n";

        foreach ($core_library_names as $name) {
            if (!$name) {
                continue;
            }

            $first_three = substr($name, 0, 3);
            if ($first_three == 'CI_' || $first_three == 'MY_') // CI 2.0
            {
                $instance_name = strtolower(substr($name, 3));
                $class_name = ucfirst($name);
            } else {
                $instance_name = strtolower($name);
                $class_name = 'CI_' . ucfirst($name);
            }

            $output .= " * @property " . ($class_name) . ' $' . ($instance_name) . "\n";
        }

        $output .= " *\n";

        foreach ($library_names as $name) {
            $first_three = substr($name, 0, 3);
            if ($first_three == 'MY_') // CI 1.7
            {
                $instance_name = strtolower(substr($name, 3));
                $class_name = ucfirst($name);
            } else {
                $instance_name = strtolower($name);
            }
            $class_name = ucfirst($name);

            $output .= " * @property " . ($class_name) . ' $' . ($instance_name) . "\n";
        }
        $output .= " */\n";

        $output .= "class CI_Controller {}\n";
        $output .= "class CI_Model extends CI_Controller {}\n";

        $output .= "/**\n";
        $output .= "* @return CI_Controller\n";
        $output .= "*/\n";
        $output .= "function get_instance() {}\n";

        if (!file_exists(INSTALLATIONPATH . 'application/dev/')) {
            mkdir(INSTALLATIONPATH . 'application/dev/');
        }
        file_put_contents(INSTALLATIONPATH . 'application/dev/_project_headers.php', $output);

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setRawMessage('Headers successfully regenerated! ' . count($library_names) . ' libraries, ' . count($core_library_names) . ' core libraries, ' . count($model_names) . ' models');
        redirect(module_url());
    }

    public function fix_missing_translation_files()
    {
        $languages = array_keys($this->config->item('languages'));
        $modules = $this->Module_model->getInstalledModulesNames();

        $i = 0;
        foreach ($modules as $module_name) {
            $lang_pase_path = $this->load->resolveModuleDirectory($module_name) . 'language/';
            foreach ($languages as $language) {
                if ($language == 'english' || !file_exists($lang_pase_path . 'english/' . $module_name . '_lang.php'))
                    continue;

                if (!file_exists($lang_pase_path . $language)) {
                    mkdir($lang_pase_path . $language);
                    copy($lang_pase_path . 'english/' . $module_name . '_lang.php', $lang_pase_path . $language . '/' . $module_name . '_lang.php');
                    $i++;
                }
            }
        }

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('Regenerated ' . $i . ' translations!');
        redirect(module_url());
    }

    public function send_test_email()
    {
        $this->load->library('EmailSender');

        $from_email = $this->config->item('site_email');
        $from_name = $this->config->item('site_name');
        $to_email = $this->auth->getUserEmail();
        $subject = 'PepisCMS test email ' . date('Y-m-d, H:i:s');

        $this->load->helper('email_html');

        $user = $this->User_model->getById($this->auth->getUserId());

        $message = $this->load->theme(module_path() . 'views/emails/test_email.php', array(
            'email' => $this->auth->getUserEmail(),
            'name' => $user->display_name,
            'date' => date('Y-m-d, H:i:s'),
            'ip' => $this->input->ip_address(),
        ), true);

        //die($message);
        //$this->emailsender->debug();
        $success = $this->emailsender->send($to_email, $from_email, $from_name, $subject, $message, true);

        if ($success) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            $this->simplesessionmessage->setMessage('Email sent!');
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR);
            $this->simplesessionmessage->setMessage('Unable to send email! See system logs.');
        }
        redirect(module_url());
    }


    public function switch_user()
    {
        if (!$this->auth->isUserRoot()) {
            show_error($this->lang->line('development_you_must_be_root'));
            die();
        }

        $this->assign('title', $this->lang->line('development_switch_user'));

        $user_id = $this->input->getParam('user_id');
        if ($user_id) {
            $from = $this->auth->getUserEmail();
            $this->auth->forceLogin($user_id);
            $this->auth->refreshSession();
            $to = $this->auth->getUserEmail();

            Logger::notice('Switching to another account ' . $from . ' to ' . $to, 'AUTH');

            redirect(admin_url());
        }

        $this->assign('users', $this->User_model->getAssocPairs('user_id', 'user_email'));
        $this->display();
    }

    public function module_make()
    {
        $this->assign('title', $this->lang->line('development_make_a_new_module'));
        $this->assign('success', FALSE);

        $database_groups = array('0' => $this->lang->line('development_database_group_implicit'));
        require INSTALLATIONPATH . 'application/config/database.php';
        if (isset($db)) {
            $keys = array_keys($db);
            if (count($keys) > 1) {
                foreach ($keys as $key) {
                    $database_groups[$key] = $key;
                }
            }
        }

        $languages = array();
        foreach ($this->lang->getEnabledAdminLanguages() as $key => $value) {
            $languages[$key] = $value[1];
        }

        $definition = CrudDefinitionBuilder::create()
            ->withField('database_table_name')
            ->end()
            ->withField('module_database_name')
                ->withNoValidationRules()
            ->end()
                ->withField('parse_database_schema')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withInputDefaultValue(1)
                ->withNoValidationRules()
            ->end()
                ->withField('generate_security_policy')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withNoValidationRules()
            ->end()
            ->withField('module_type')
                ->withInputType(FormBuilder::SELECTBOX)
                ->withInputDefaultValue('crud')
                ->withValues(array(
                        'crud' => $this->lang->line('development_module_type_crud'),
                        'default' => $this->lang->line('development_module_type_basic')
                    )
                )
                ->withNoValidationRules()
            ->end()
            ->withField('database_group')
                ->withInputType(FormBuilder::SELECTBOX)
                ->withValues($database_groups)
                ->withNoValidationRules()
            ->end()
                ->withField( 'translations')
                ->withInputType(FormBuilder::MULTIPLECHECKBOX)
                ->withValues($languages)
                ->withInputDefaultValue(array_keys($languages))
                ->withNoValidationRules()
            ->end()
                ->withField('generate_public_controller')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withNoValidationRules()
            ->end()
            ->withImplicitTranslations('development', $this->lang)
            ->build();

        $this->formbuilder->setTitle($this->lang->line('development_make_a_new_module'));
        $this->formbuilder->setBackLink(module_url());
        $this->formbuilder->setCallback(array($this, '_fb_callback_on_make_save'), FormBuilder::CALLBACK_ON_SAVE);
        $this->formbuilder->setRedirectOnSaveSuccess(FALSE);
        $this->formbuilder->setDefinition($definition);

        $this->assign('form', $this->formbuilder->generate());
        $this->display();
    }

    /**
     * Must overwrite the save procedure and return true or false
     *
     * @param array $save_array associative array made of filtered POST variables
     * @return boolean
     * @throws ReflectionException
     */
    public function _fb_callback_on_make_save(&$save_array)
    {
        $module_databse_table_name = trim($save_array['database_table_name']);
        $module_database_name = trim($save_array['module_database_name']);
        if (!$module_database_name) {
            $module_database_name = $module_databse_table_name;
        }

        if ($module_databse_table_name) {
            $database_group = ($save_array['database_group'] ? $save_array['database_group'] : FALSE);

            $this->load->library('Module_generator');
            $success = $this->module_generator->makeUserSpaceModule($module_databse_table_name, $module_database_name, TRUE,
                ($save_array['parse_database_schema'] == 1), $database_group, array_keys($save_array['translations']),
                ($save_array['generate_public_controller'] == 1), ($save_array['module_type'] == 'crud'),
                $save_array['generate_security_policy']);

            if (!$success) {
                $this->formbuilder->setValidationErrorMessage(
                    sprintf($this->lang->line('development_unable_to_generate_module_for_table'), $module_databse_table_name));
                return FALSE;
            }

            $this->assign('adminmenu', $this->renderMenu());
            $this->assign('success', TRUE);
            $this->assign('module_database_name', $module_database_name);
        } else {
            return FALSE;
        }

        return TRUE;
    }

}
