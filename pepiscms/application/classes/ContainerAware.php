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
 * @property PEPISCMS_Loader $load
 * @property User_model $User_model
 * @property PEPISCMS_Config $config
 * @property PEPISCMS_Lang $lang
 * @property Cachedobjectmanager $cachedobjectmanager
 * @property Page_model $Page_model
 * @property Menu_model $Menu_model
 * @property Site_language_model $Site_language_model
 * @property CI_Benchmark $benchmark
 * @property PEPISCMS_Input $input
 * @property PEPISCMS_Email $email
 * @property PEPISCMS_Upload $upload
 * @property Generic_model $Generic_model
 * @property PEPISCMS_Form_validation $form_validation
 * @property PEPISCMS_Output $output
 * @property CI_DB $db
 * @property SimpleSessionMessage $simplesessionmessage
 * @property ModulePathResolver $modulepathresolver
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