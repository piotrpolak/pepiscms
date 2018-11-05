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
 * CMS setup controller
 */
class Setup extends AdminController
{
    /** Callback * */
    protected function renderMenu()
    {
        $this->load->library('MenuRendor');
        return $this->menurendor->render('utilities', 'index', $this->input->getParam('language_code'));
    }

    public function __construct()
    {
        parent::__construct();
        if (!$this->config->item('feature_is_enabled_setup')) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }

        $this->load->language('setup');
        $this->load->library('SimpleSessionMessage');

        $this->assign('title', $this->lang->line('setup_module_name'));
    }

    public function index()
    {
        $this->load->library('FormBuilder');
        $this->load->model('Siteconfig_model');
        $definition = array();
        $definition['site_name'] = array(
            'label' => $this->lang->line('setup_site_name'),
            'description' => $this->lang->line('setup_site_name_desc'),
            'validation_rules' => 'required',
            'input_group' => 'setup_input_group_pepiscms',
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['site_email'] = array(
            'label' => $this->lang->line('setup_site_email'),
            'description' => $this->lang->line('setup_site_email_desc'),
            'validation_rules' => 'required|valid_email',
            'input_group' => 'setup_input_group_pepiscms',
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['default_language'] = array(
            'label' => $this->lang->line('setup_default_language'),
            'description' => $this->lang->line('setup_default_language_desc'),
            'validation_rules' => 'required',
            'input_group' => 'setup_input_group_pepiscms',
            'input_is_editable' => count($this->Siteconfig_model->getAvailableAdminLanguages()) > 1,
            'values' => $this->Siteconfig_model->getAvailableAdminLanguages(),
            'input_default_value' => $this->Siteconfig_model->getDefaultAdminLanguage(),
            'input_type' => FormBuilder::SELECTBOX,
        );
        $definition['current_theme'] = array(
            'label' => $this->lang->line('setup_current_theme'),
            'description' => $this->lang->line('setup_current_theme_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_pepiscms',
            'values' => $this->Siteconfig_model->getAvailableThemes(),
            'input_type' => FormBuilder::SELECTBOX,
        );
        $definition['timezone'] = array(
            'label' => $this->lang->line('setup_timezone'),
            'description' => $this->lang->line('setup_timezone_desc'),
            'validation_rules' => 'required',
            'input_group' => 'setup_input_group_pepiscms',
            'values' => $this->Siteconfig_model->getAvailableTimezones(),
            'input_type' => FormBuilder::SELECTBOX,
            'input_default_value' => date_default_timezone_get(),
        );
        $definition['cms_enable_frontend'] = array(
            'label' => $this->lang->line('setup_cms_enable_frontend'),
            'description' => $this->lang->line('setup_cms_enable_frontend_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_features',
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['cms_enable_utilities'] = array(
            'label' => $this->lang->line('setup_cms_enable_utilities'),
            'input_group' => 'setup_input_group_features',
            'validation_rules' => '',
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['cms_enable_filemanager'] = array(
            'label' => $this->lang->line('setup_cms_enable_filemanager'),
            'description' => $this->lang->line('setup_cms_enable_filemanager_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_features',
            'input_type' => FormBuilder::CHECKBOX,
        );

        $definition['cache_expires'] = array(
            'label' => $this->lang->line('setup_cache_expires'),
            'description' => $this->lang->line('setup_cache_expires_desc'),
            'validation_rules' => 'required|integer',
            'input_group' => 'setup_input_group_cache',
            'input_type' => FormBuilder::TEXTFIELD,
        );

        $definition['cms_intranet'] = array(
            'label' => $this->lang->line('setup_cms_intranet'),
            'description' => $this->lang->line('setup_cms_intranet_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_intranet',
            'input_type' => FormBuilder::CHECKBOX,
        );


        $definition['cms_customization_logo_predefined'] = array(
            'label' => $this->lang->line('setup_cms_customization_logo_predefined'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
            'input_type' => FormBuilder::SELECTBOX,
            'values' => array(null => '------') + $this->Siteconfig_model->getPredefinedIconsNames(),
        );
        $definition['cms_customization_logo'] = array(
            'label' => $this->lang->line('setup_cms_customization_logo'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
            'input_type' => FormBuilder::IMAGE,
            'upload_path' => $this->config->item('theme_path'),
            'upload_display_path' => $this->config->item('theme_path'),
            'upload_complete_callback' => array($this, '_fb_callback_make_filename_seo_friendly'),
        );
        $definition['cms_customization_site_public_url'] = array(
            'label' => $this->lang->line('setup_cms_customization_site_public_url'),
            'description' => $this->lang->line('setup_cms_customization_site_public_url_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['cms_login_page_description'] = array(
            'label' => $this->lang->line('setup_cms_login_page_description'),
            'description' => $this->lang->line('setup_cms_login_page_description_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
            'input_type' => FormBuilder::TEXTAREA,
            'input_is_editable' => true,
        );
        $definition['cms_customization_support_link'] = array(
            'label' => $this->lang->line('setup_cms_customization_support_link'),
            'description' => $this->lang->line('setup_cms_customization_support_link_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
        );
        $definition['cms_customization_support_line'] = array(
            'label' => $this->lang->line('setup_cms_customization_support_line'),
            'description' => $this->lang->line('setup_cms_customization_support_line_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
        );
        $definition['cms_customization_login_view_path'] = array(
            'label' => $this->lang->line('setup_cms_customization_login_view_path'),
            'description' => $this->lang->line('setup_read_only_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
            'input_is_editable' => false,
        );
        $definition['cms_customization_on_login_redirect_url'] = array(
            'label' => $this->lang->line('setup_cms_customization_on_login_redirect_url'),
            'description' => $this->lang->line('setup_read_only_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_customization',
            'input_is_editable' => false,
        );


        $definition['email_use_smtp'] = array(
            'label' => $this->lang->line('setup_email_use_smtp'),
            'description' => $this->lang->line('setup_email_use_smtp_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_email',
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['email_smtp_host'] = array(
            'label' => $this->lang->line('setup_email_smtp_host'),
            'description' => $this->lang->line('setup_email_smtp_host_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_email',
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['email_smtp_user'] = array(
            'label' => $this->lang->line('setup_email_smtp_user'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_email',
            'description' => '',
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['email_smtp_pass'] = array(
            'label' => $this->lang->line('setup_email_smtp_pass'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_email',
            'description' => '',
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['email_smtp_port'] = array(
            'label' => $this->lang->line('setup_email_smtp_port'),
            'description' => $this->lang->line('setup_email_smtp_port_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_email',
            'input_type' => FormBuilder::TEXTFIELD,
        );


        $definition['debug_log_php_deprecated'] = array(
            'label' => $this->lang->line('setup_debug_log_php_deprecated'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_debug',
            'description' => '',
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['debug_log_php_warning'] = array(
            'label' => $this->lang->line('setup_debug_log_php_warning'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_debug',
            'description' => '',
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['debug_log_php_error'] = array(
            'label' => $this->lang->line('setup_debug_log_php_error'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_debug',
            'description' => '',
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['debug_maintainer_email_address'] = array(
            'label' => $this->lang->line('setup_debug_maintainer_email_address'),
            'description' => $this->lang->line('setup_debug_maintainer_email_address_desc'),
            'validation_rules' => '',
            'input_group' => 'setup_input_group_debug',
            'input_type' => FormBuilder::TEXTFIELD,
        );


        $this->formbuilder->setId('1')
            ->setBackLink(admin_url() . 'utilities')
            ->setApplyButtonEnabled()
            ->setFeedObject($this->Siteconfig_model)
            ->setDefinition($definition)
            ->setCallback(array($this, '_fb_callback_after_save'), FormBuilder::CALLBACK_AFTER_SAVE)
            ->setCallback(array($this, '_fb_callback_on_save'), FormBuilder::CALLBACK_ON_SAVE)
            ->setCallback(array($this, '_fb_callback_on_read'), FormBuilder::CALLBACK_ON_READ);

        $this->assign('form', $this->formbuilder->generate())
            ->display();
    }

    /**
     * Callback function changing the name of the file to SEO friendly
     *
     * @version: 1.2
     * @date: 2013-10-09
     * @param string $filename
     * @param type $base_path
     * @param string $data
     * @param string $current_image_field_name
     * @return boolean
     */
    public function _fb_callback_make_filename_seo_friendly(&$filename, $base_path, &$data, $current_image_field_name)
    {
        $data['name'] = 'customization';

        // List of the fields to be used, if no value is present for a given key
        // then the key will be ignored. By default all values of the keys
        // specified will be concatenated
        $title_field_names = array('name');

        $this->load->helper('string');
        $path = $base_path . $filename;
        $path_parts = pathinfo($path);

        // Attempt to build a name
        $new_name_base = '';
        foreach ($title_field_names as $title_field_name) {
            // Concatenating all the elements
            if (isset($data[$title_field_name]) && $data[$title_field_name]) {
                $new_name_base .= '-' . $data[$title_field_name];
            }
        }

        // Making it web safe
        if ($new_name_base) {
            $new_name_base = niceuri($new_name_base);
        }

        // This should not be an else statement as niceuri can return empty string sometimes
        if (!$new_name_base) {
            $new_name_base = niceuri($path_parts['filename']);
        }

        // This should normally never happen, but who knows - this is bulletproof
        if (!$new_name_base) {
            $new_name_base = md5(time() + rand(1000, 9999));
        }

        $new_base_path = '';
        // We don't like upper case extensions
        $extension = strtolower($path_parts['extension']);
        $new_name = $new_name_base . '.' . $extension;

        // Protection against existing files
        $i = 2;
        while (file_exists($base_path . $new_base_path . $new_name)) {
            $new_name = $new_name_base . '-' . $i . '.' . $extension;
            if ($i++ > 50) {
                // This is ridiculous but who knowss
                $i = md5(time() + rand(1000 - 9999));
            }
        }

        // No need to change filename? Then we are fine
        if ($filename == $new_name) {
            return true;
        }

        // Finally here we go!
        if (rename($path, $base_path . $new_base_path . $new_name)) {
            $data[$current_image_field_name] = $new_base_path . $new_name;
            $path = $base_path . $new_name;
            $filename = $new_base_path . $new_name;

            return true;
        }
        return false;
    }

    public function _fb_callback_after_save(&$data)
    {
        $this->auth->refreshSession();
        $this->load->library('Cachedobjectmanager');
        $this->cachedobjectmanager->cleanup();
        $this->db->cache_delete_all();
        ModuleRunner::flushCache();
    }

    /**
     * Must overwrite the save procedure and return true or false
     * @param array $data_array associative array made of filtered POST variables
     * @return bool
     */
    public function _fb_callback_on_save(&$data_array)
    {
        return $this->Siteconfig_model->saveAllConfigurationVariables($data_array);
    }


    /**
     * Must populate object
     *
     * @param object $object
     */
    public function _fb_callback_on_read(&$object)
    {
        $object = $this->Siteconfig_model->getAllConfigurationVariables();
    }
}
