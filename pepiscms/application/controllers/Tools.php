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
 * CLI tools
 */
class Tools extends EnhancedController
{

    private $ignored_methods = array(
        'get_instance',
        'getAttribute',
        'getAttributes',
        'setAttributes',
        'display',
        'assign'
    );

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }


        $this->load->model('User_model');
        $this->load->library('Logger');

        global $argv;
        $number_of_arguments = count($argv) - 3;
        $method_name = $this->router->method;

        if ($this->isMethodNameNotAllowed($method_name)) {
            show_404();
        }

        $method = new ReflectionMethod($this, $method_name);

        if ($number_of_arguments < $method->getNumberOfRequiredParameters()) {
            $parameter_names = array();

            $parameters = $method->getParameters();
            foreach ($parameters as $parameter) {
                $parameter_names[] = '<' . $parameter->getName() . '>';
            }

            $this->_throw_exception(99, 'Method ' . $method_name . ' expects ' . $method->getNumberOfRequiredParameters() . ' parameters.' . PHP_EOL . PHP_EOL . 'Usage: php index.php tools ' . $method_name . ' ' . implode(' ', $parameter_names));
        }
    }

    private function _throw_exception($code, $message)
    {
        echo PHP_EOL;
        echo $message . PHP_EOL;
        exit($code);
    }

    private function _exit_with_success($message)
    {
        echo PHP_EOL;
        echo $message . PHP_EOL;
        exit(0);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Displays list of methods
     *
     * @usage php index.php tools
     */
    public function index()
    {
        $reflection = new ReflectionClass($this);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        echo PHP_EOL;
        echo "PepisCMS CLI tools" . PHP_EOL . PHP_EOL;
        foreach ($methods as $method) {
            $method_name = $method->getName();
            if ($this->isMethodNameNotAllowed($method_name)) {
                continue;
            }

            $lines = array();
            preg_match_all('/\* ([^\*|\/]+[a-z].)\n/', $method->getDocComment(), $matches);
            foreach ($matches[1] as $line) {
                $line = trim($line);
                if ($line) {
                    $lines[] = $line;
                }
            }

            echo str_pad($method_name, 15, ' ') . $lines[0] . PHP_EOL;
        }
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Changes user password
     *
     * @usage php index.php tools passwd <email> <password>
     * @param string $user_email
     * @param string $new_password
     */
    public function passwd($user_email, $new_password)
    {
        $user_id = $this->User_model->getUserIdByEmail($user_email);
        if ($user_id) {
            if ($this->User_model->changePasswordByUserId($user_id, $new_password)) {
                $this->_exit_with_success("Successfully changed password for {$user_email}!");
            }
        }

        $this->_throw_exception(4, "User email does not exist {$user_email}!");
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Inactivates user
     *
     * @usage php index.php tools inactivate <email>
     * @param string $user_email
     */
    public function inactivate($user_email)
    {
        $user_id = $this->User_model->getUserIdByEmail($user_email);
        if ($user_id) {
            if ($this->User_model->inactivateById($user_id)) {
                $this->_exit_with_success("Successfully inactivated user {$user_email}!");
            }
        }

        $this->_throw_exception(4, "User email does not exist {$user_email}!");
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Activates user
     *
     * @usage php index.php tools activate <email>
     * @param string $user_email
     */
    public function activate($user_email)
    {
        $user_id = $this->User_model->getUserIdByEmail($user_email);
        if ($user_id) {
            if ($this->User_model->activateById($user_id)) {
                $this->_exit_with_success("Successfully activated user {$user_email}!");
            }
        }

        $this->_throw_exception(4, "User email does not exist {$user_email}!");
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Installs PepisCMS
     *
     * @usage php index.php tools install
     * @param bool $clean_database
     * @throws Exception
     */
    public function install($clean_database = false)
    {
        $this->load->library('Installer_helper');

        $data = $this->installer_helper->getDefaultInstallationValues();

        $this->installer_helper->buildFileStructure(INSTALLATIONPATH);
        $this->installer_helper->writeConfigFiles($data, INSTALLATIONPATH);

        if ($clean_database === true || strtolower($clean_database) === 'true') {
            $this->clean_database();
        }

        $this->installer_helper->writeDatabase($data);

        $this->_exit_with_success("Successfully installed");
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Activates user
     *
     * @usage php index.php tools register_admin <email> <password>
     * @param string $user_email
     */
    public function register_admin($user_email, $password)
    {
        $this->load->library('Installer_helper');
        if ($this->installer_helper->registerAdmin($user_email, $password)) {
            $this->_exit_with_success("Successfully activated root {$user_email}!");
        } else {
            $this->_throw_exception(1, "Successfully activated root {$user_email}!");
        }
    }

    /**
     * Sets a config value.
     *
     * @usage php index.php tools set_config <name> <value>
     * @param $name
     * @param $value
     */
    public function set_config($name, $value)
    {
        $value_mapped = $value;
        if (in_array(trim(strtolower($value)), array('true', 'false'))) {
            settype($value_mapped, 'boolean');
        }

        if ($this->Siteconfig_model->saveConfigByName($name, $value_mapped)) {
            $this->_exit_with_success("{$name} set to {$value}");
        } else {
            $this->_throw_exception(1, "Unable to set {$name}");
        }
    }

    /**
     * Returns a config value
     * @usage php index.php tools get_config <name>
     * @param $name
     */
    public function get_config($name)
    {
        echo $this->config->item($name) . PHP_EOL;
        exit(0);
    }

    /**
     * Removes all tables from database.
     */
    private function clean_database()
    {
        $this->load->dbforge();
        $tables = $this->db->list_tables();

        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            $this->dbforge->drop_table($table);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $tables_count = count($tables);

        echo "Successfully removed {$tables_count} tables!" . PHP_EOL;
    }

    /**
     * @param $method_name
     * @return bool
     */
    private function isMethodNameNotAllowed($method_name)
    {
        return $method_name[0] == '_' || in_array($method_name, $this->ignored_methods);
    }
}
