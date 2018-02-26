<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TranslatorAdmin
 */
class TranslatorAdmin extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('LanguageHelper');
        $this->load->helper('string');
        $this->load->language('translator');

        $this->load->library('SimpleSessionMessage');

        $version = $this->Module_model->getModuleDescriptor($this->modulerunner->getRunningModuleName())->getVersion();

        $this->assign('version', $version);

        $this->assign('title', $this->lang->line('translator_module_name') . ' v' . $version);
    }

    public function index()
    {
        $this->load->library('ModuleRunner');
        $modules = ModuleRunner::getAvailableModules();

        $modules = array_flip($modules);

        $available_modules = array('system' => array(), 'core_modules' => array(), 'userspace_modules' => array());
        $available_modules['system']['system'] = $this->languagehelper->getModuleLanguages('system');

        foreach ($modules as $module => $dontcare) {
            $languages = $this->languagehelper->getModuleLanguages($module);
            if (count($languages) == 0) {
                unset($modules[$module]);
                continue;
            }

            if ($this->Module_model->isCoreModule($module)) {
                $module_group = 'core_modules';
            } else {
                $module_group = 'userspace_modules';
            }

            $available_modules[$module_group][$module] = $languages;
        }


        $this->assign('available_modules', $available_modules);

        $this->display('index');
    }

    public function show()
    {
        $module = $this->input->getParam('module');
        $file_name = $this->input->getParam('file_name');
        $is_system = $module == 'system';
        $errors_now_writeable = array();

        if (!$module) {
            show_404();
        }

        $language_files = $this->languagehelper->getModuleLanguageFiles($module);
        if (!$file_name && count($language_files) == 1) {
            redirect(module_url() . 'show/module-' . $module . '/file_name-' . $language_files[0]);
        }

        $languages = $this->languagehelper->getModuleLanguages($module);

        $translations = array();
        $keys = array();
        foreach ($languages as $lang_name) {
            $keys = array_unique(array_merge($keys, $this->languagehelper->getModuleTranslationKeys($module, $languages, $file_name)));
            $translations[$lang_name] = $this->languagehelper->getLanguageByModuleName($module, $lang_name, $file_name);

            if (!$this->languagehelper->isLangFileWritableByModule($module, $lang_name, $file_name)) {
                $errors_now_writeable[] = $lang_name . '/' . $file_name;
            }
        }

        $this->assign('errors_now_writeable', $errors_now_writeable);
        $this->assign('module', $module);
        $this->assign('keys', $keys);
        $this->assign('languages', $languages);
        $this->assign('language_files', $language_files);
        $this->assign('file_name', $file_name);
        $this->assign('translation', $translations);
        $this->display();
    }

    public function edit()
    {
        $module = $this->input->getParam('module');
        $language = $this->input->getParam('language');
        $key = $this->input->getParam('key');
        $file_name = $this->input->getParam('file_name');
        $is_system = $module == 'system';

        if (!$module || !$language || !$key || !$file_name) {
            show_404();
        }

        $lang = $this->languagehelper->getLanguageByModuleName($module, $language, $file_name);
        $value = isset($lang[$key]) ? $lang[$key] : '';

        $en_value = false;

        if ($language != 'english') {
            $en_lang = $this->languagehelper->getLanguageByModuleName($module, 'english', $file_name);
            $en_value = isset($en_lang[$key]) ? $en_lang[$key] : false;
        }

        $languages = $this->languagehelper->getModuleLanguages($module);

        $this->load->library('FormBuilder');

        $this->formbuilder->setTitle($this->lang->line('translator_translate_field'));
        $this->formbuilder->setBackLink(module_url() . 'show/module-' . $module . '/file_name-' . $file_name . '#' . $key);
        $this->formbuilder->setId($key);
        $this->formbuilder->setCallback(array($this, '_fb_callback_on_save'), FormBuilder::CALLBACK_ON_SAVE);
        $this->formbuilder->setDefinition(
            array(
                'value' => array(
                    'label' => $this->lang->line('translator_translation'),
                    'description' => $this->lang->line('translator_translation_description'),
                    'input_type' => FormBuilder::TEXTAREA,
                    'input_default_value' => $value
                )
            )
        );

        $this->assign('form', $this->formbuilder->generate());

        $this->assign('file_name', $file_name);
        $this->assign('module', $module);
        $this->assign('key', $key);
        $this->assign('languages', $languages);
        $this->assign('en_value', $en_value);
        $this->assign('language', $language);
        $this->display('edit');
    }

    public function post_edit()
    {
        redirect(module_url() . 'show/module-' . $this->input->getParam('module') . '/file_name-' . $this->input->getParam('file_name') . '#' . $this->input->getParam('key'));
    }

    public function _fb_callback_on_save(&$data)
    {
        $module = $this->input->getParam('module');
        $language = $this->input->getParam('language');
        $key = $this->input->getParam('key');
        $file_name = $this->input->getParam('file_name');

        $this->languagehelper->setModuleLanguageField($module, $language, $key, $data['value'], $file_name);
        $this->load->library('Auth');
        $this->auth->refreshSession();
        return true;
    }

    public function delete()
    {
        $module = $this->input->getParam('module');
        $key = $this->input->getParam('key');
        $file_name = $this->input->getParam('file_name');

        $this->languagehelper->deleteField($module, $key, $file_name);

        $this->load->library('SimpleSessionMessage');
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');
        redirect(module_url() . 'show/module-' . $module . '/file_name-' . $file_name);
    }
}
