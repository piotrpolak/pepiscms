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
 * @link                http://www.pocd lak.ro/
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Enhanced controller providing some extra features
 *
 * @property CI_DB_driver $db
 * @property Array_model $Array_model
 * @property Generic_model $Generic_model
 * @property Group_model $Group_model
 * @property Log_model $Log_model
 * @property Menu_model $Menu_model
 * @property Module_model $Module_model
 * @property Page_model $Page_model
 * @property Site_language_model $Site_language_model
 * @property Siteconfig_model $Siteconfig_model
 * @property Ssh_model $Ssh_model
 * @property User_model $User_model
 * @property Item_model $Item_model
 * @property Auth $auth
 * @property Backup $backup
 * @property CachedDirectoryReader $cacheddirectoryreader
 * @property Cachedobjectmanager $cachedobjectmanager
 * @property ConfigBuilder $configbuilder
 * @property DataGrid $datagrid
 * @property Document $document
 * @property EmailSender $emailsender
 * @property FormBuilder $formbuilder
 * @property Google_chart_helper $google_chart_helper
 * @property Installer_helper $installer_helper
 * @property Localization $localization
 * @property Logger $logger
 * @property MenuRendor $menurendor
 * @property ModulePathResolver $modulepathresolver
 * @property ModuleRunner $modulerunner
 * @property PEPISCMS_Email $email
 * @property PEPISCMS_Form_validation $form_validation
 * @property PEPISCMS_Upload $upload
 * @property RTFEditor $rtfeditor
 * @property SecurityManager $securitymanager
 * @property SecurityPolicy $securitypolicy
 * @property SecurityPolicyBuilder $securitypolicybuilder
 * @property SimpleSessionMessage $simplesessionmessage
 * @property Spreadsheet $spreadsheet
 * @property Twig $twig
 * @property Widget $widget
 * @property PEPISCMS_Config $config
 * @property PEPISCMS_Hooks $hooks
 * @property PEPISCMS_Input $input
 * @property PEPISCMS_Lang $lang
 * @property PEPISCMS_Loader $load
 * @property PEPISCMS_Output $output
 * @property CI_Calendar $calendar
 * @property CI_Cart $cart
 * @property CI_Encrypt $encrypt
 * @property CI_Encryption $encryption
 * @property CI_Ftp $ftp
 * @property CI_Image_lib $image_lib
 * @property CI_Javascript $javascript
 * @property CI_Migration $migration
 * @property CI_Pagination $pagination
 * @property CI_Parser $parser
 * @property CI_Profiler $profiler
 * @property CI_Table $table
 * @property CI_Trackback $trackback
 * @property CI_Typography $typography
 * @property CI_Unit_test $unit_test
 * @property CI_User_agent $user_agent
 * @property CI_Xmlrpc $xmlrpc
 * @property CI_Xmlrpcs $xmlrpcs
 * @property CI_Zip $zip
 * @property CI_Benchmark $benchmark
 * @property CI_Security $security
 * @property CI_URI $uri
 */
abstract class EnhancedController extends CI_Controller
{
    /**
     * An array containing controller's context.
     *
     * @var array
     */
    protected $response_attributes = array();

    /**
     * Protects the view from being rendered for twice
     *
     * @var bool
     */
    protected $already_displayed = false;

    /**
     * EnhancedController constructor.
     */
    public function __construct()
    {
        if (!get_instance()) { // Very important for modules!!
            parent::__construct();
        }
        $this->output->enable_profiler($this->config->item('enable_profiler'));
    }

    /**
     * Returns current controller name.
     *
     * @return string
     */
    protected function getControllerName()
    {
        return $this->input->getControllerName();
    }

    /**
     * Returns current method name.
     *
     * @return string
     */
    protected function getMethodName()
    {
        return $this->input->getMethodName();
    }

    /**
     * Sets the current controller name.
     *
     * @param string $controller
     * @return mixed
     */
    protected function setControllerName($controller)
    {
        return $this->input->setControllerName($controller);
    }

    /**
     * Sets the current method name.
     *
     * @param string $method
     * @return mixed
     */
    protected function setMethodName($method)
    {
        return $this->input->setMethodName($method);
    }

    /**
     * Assigns a value to a variable.
     *
     * @param string $attributeName
     * @param mixed $attributeValue
     * @return EnhancedController
     */
    public function assign($attributeName, $attributeValue)
    {
        $this->response_attributes[$attributeName] = $attributeValue;
        return $this;
    }

    /**
     * Returns value of the assigned parameter.
     *
     * @param string $attributeName
     * @return mixed
     */
    public function getAttribute($attributeName)
    {
        if (!isset($this->response_attributes[$attributeName])) {
            return null;
        }
        return $this->response_attributes[$attributeName];
    }

    /**
     * Returns associative array representing response attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->response_attributes;
    }

    /**
     * Sets associative array representing response attributes.
     *
     * @param $response_attributes
     */
    public function setAttributes($response_attributes)
    {
        $this->response_attributes = $response_attributes;
    }

    /**
     * Loads and displays view
     *
     * @param string|bool $view
     * @param bool $display_header
     * @param bool $display_footer
     * @return bool
     */
    public function display($view = false, $display_header = true, $display_footer = true)
    {
        if ($this->already_displayed) {
            return false;
        }

        if (!$view) {
            $view = $this->uri->segment(1) . '/' . $this->uri->segment(2) .
                (strlen($this->uri->segment(3)) > 0 ? '_' . $this->uri->segment(3) : '');
        }

        $this->load->view($view, $this->response_attributes);

        // Reseting
        $this->response_attributes = array();
        $this->already_displayed = true;

        return true;
    }
}
