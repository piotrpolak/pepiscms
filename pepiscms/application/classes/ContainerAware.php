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
 * ContainerAware makes it possible to "inject" CodeIgniter services (models, libraries) into libraries so that you can
 * seemlesly use them inside your libraries just like if you are coding controllers or models/
 *
 * @since 1.0.0
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
 * @property PDFGenerator $pdfgenerator
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
class ContainerAware
{
    /**
     * Returns service registered inside CodeIgniter container (controller)
     *
     * @param string $var
     * @return mixed
     */
    public function __get($var)
    {
        static $CI;
        isset($CI) OR $CI = CI_Controller::get_instance();
        return $CI->$var;
    }
}