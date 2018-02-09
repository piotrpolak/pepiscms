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
 * CLI tools
 */
class Tools extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }

        echo PHP_EOL;


        $this->load->model('User_model');
        $this->load->library('Logger');

        global $argv;
        $number_of_arguments = count($argv) - 3;
        $method_name = $this->router->method;
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
        echo $message . PHP_EOL;
        exit($code);
    }

    private function _exit_with_success($message)
    {
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

        echo "PepisCMS CLI tools" . PHP_EOL . PHP_EOL;
        foreach ($methods as $method) {
            $method_name = $method->getName();
            if ($method_name{0} == '_' || $method_name == 'get_instance') {
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
     */
    public function install()
    {
        $this->load->library('Installer_helper');

        $data = $this->installer_helper->getDefaultInstallationValues();

        $this->installer_helper->buildFileStructure(INSTALLATIONPATH);
        $this->installer_helper->writeConfigFiles($data, INSTALLATIONPATH);
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
}