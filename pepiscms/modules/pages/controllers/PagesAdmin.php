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
 * Pages admin module
 */
class PagesAdmin extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('SimpleSessionMessage');
        $this->load->library('Cachedobjectmanager');

        $this->load->library('FormBuilder');
        $this->load->model('Menu_model');
        $this->load->model('Page_model');
        $this->load->model('Site_language_model');
        $this->load->model('User_model');
        $this->load->helper('date');
        $this->load->moduleLanguage('pages');
        $this->load->helper('string');

        if (!$this->db->table_exists($this->Page_model->getTable())) {
            show_error($this->lang->line('pages_pages_database_not_configured'));
        }

        if (count($this->Site_language_model->getLanguageCodes()) == 0) {
            $this->Site_language_model->saveById(false, array(
                'code' => 'en', 'label' => 'English', 'is_default' => true, 'ci_language' => 'english'
            ));
        }

        $this->assign('site_language', $this->Site_language_model->getLanguageByCode($this->input->getParam('language_code')))
            ->assign('view', $this->_getView());
    }

    public function index()
    {
        $site_language = $this->getAttribute('site_language');
        $pages = $menu = array();

        if ($site_language) {
            $menu = $this->Menu_model->getMenu(0, $site_language->code);
            $pages = $this->Page_model->getNoMenuPages($site_language->code);
        }

        $this->assign('simple_session_message', $this->simplesessionmessage->getLocalizedMessage())
            ->assign('url_suffix', $this->config->item('url_suffix'))
            ->assign('menu', $menu)
            ->assign('pages', $pages)
            ->assign('site_languages', $this->Site_language_model->getLanguages());

        $this->display($this->getAttribute('view') == 'tree' ? false : 'index_simple');
    }

    public function setviewtype()
    {
        $view = $this->input->getParam('view');
        $this->auth->setSessionVariable('pages_view', $view);

        $this->redirectBack($view);
    }

    public function edit()
    {
        $url_suffix = $this->config->item('url_suffix');
        $site_language = $this->getAttribute('site_language');
        $view = $this->input->getParam('view');
        $page_id = $this->input->getParam('page_id');


        $input_groups = array('document' => $this->lang->line('pages_label_document_contents'),
            'info' => $this->lang->line('pages_label_document_info'));

        list($definition, $menu_item) = $this->_getPageEditDefinition($input_groups, $page_id, $site_language);


        $this->formbuilder->setTable('pages', false, 'page_id')
            ->setId($page_id)
            ->setBackLink(module_url() . 'index/language_code-' . $site_language->code . ($view ? '/view-' . $view : ''))
            ->setCallback(array($this, '_on_page_save_callback'), FormBuilder::CALLBACK_ON_SAVE)
            ->setCallback(array($this, '_on_page_read_callback'), FormBuilder::CALLBACK_ON_READ)
            ->setDefinition($definition)
            ->setTitle($input_groups['document'])
            ->getRenderer()->setErrorDelimiters(get_warning_begin(), get_warning_end());


        if ($this->formbuilder->getId()) {
            $this->formbuilder->setApplyButtonEnabled();
        }

        $this->assign('menu_item', $menu_item)
            ->assign('form', $this->formbuilder->generate())
            ->assign('url_suffix', $url_suffix)
            ->assign('view', $view)
            ->display();
    }

    public function _on_page_read_callback(&$object)
    {
        $page = $this->Page_model->getById($this->formbuilder->getId());
        $menuitem = $this->getAttribute('menu_item');
        $object = (object)array_merge((array)$page, (array)$menuitem);

        $this->assign('page', $page);
    }

    public function _on_page_save_callback(&$data)
    {
        /*
         * TESTS
         * Update page contents
         * Write new page
         * Attach new page to menu
         * Attach existing page to menu
         * Unpin page to menu
         * OK Try to attach page and duplicate menu item name
         * Try to unpin element that has children
         * Try to duplicate URL
         */

        $site_language = $this->getAttribute('site_language');
        $current_menu_item = $this->formbuilder->getId() ? $this->Menu_model->getElementByPageId($this->formbuilder->getId()) : false;
        $was_page_attached_to_menu = ($current_menu_item) ? true : false;
        $is_page_attached_to_menu = false;
        $is_new_page = !$this->formbuilder->getId();

        if (strlen($data['page_uri']) == 0) {
            $data['page_uri'] = niceuri($data['page_title']);
        }


        if (strlen($data['page_uri']) == 0) {
            $this->formbuilder->setValidationErrorMessage($this->lang->line('pages_dialog_page_uri_cannot_be_empty'));
            return false;
        }


        if ($is_new_page) {
            // New pages
            if ($this->Page_model->isUriTaken($data['page_uri'], $site_language->code)) {
                $this->formbuilder->setValidationErrorMessage($this->lang->line('pages_dialog_uri_already_exists'));
                return false;
            }
        } else {
            // Existing pages
            $page = $this->Page_model->getById($this->formbuilder->getId(), 'page_uri');
            if ($page->page_uri != $data['page_uri'] && $this->Page_model->isUriTaken($data['page_uri'], $site_language->code)) {
                $this->formbuilder->setValidationErrorMessage($this->lang->line('pages_dialog_uri_already_exists'));
                return false;
            }
        }


        /* Replacing \r and \n */
        $data['page_description'] = str_replace(array("\n", "\n"), " ", $data['page_description']);


        /*
         * Is the page attached to a menu element?
         * -1 indicates that no
         */
        if ($data['parent_item_id'] != -1) {
            $is_page_attached_to_menu = true;

            // For for pages that were not attached to menu
            // and for pages that were attached to another parent
            if (!$was_page_attached_to_menu || $data['parent_item_id'] != $current_menu_item->parent_item_id || $data['item_name'] != $current_menu_item->item_name) {
                // Pages attached to menu element first
                if ($this->Menu_model->itemExists($data['item_name'], $data['parent_item_id'], $site_language->code)) {
                    $this->formbuilder->setValidationErrorMessage(sprintf($this->lang->line('pages_dialog_item_already_in_selected_menu_branch'), $data['item_name']));
                    return false;
                }
            }
        }

        // For OLD pages that were previously attached to menu and now they are not
        if (!$is_new_page && !$is_page_attached_to_menu && $was_page_attached_to_menu) {
            if ($this->Menu_model->hasChildren($current_menu_item->item_id)) {
                $this->formbuilder->setValidationErrorMessage(sprintf($this->lang->line('has_children'), $data['item_name']));
                return false;
            } else {
                // Unpining
                $this->Menu_model->deleteById($current_menu_item->item_id);
            }
        }


        $data['user_id_modified'] = $this->auth->getUserId();
        $data['timestamp_modified'] = utc_timestamp();
        if ($is_new_page) {
            $data['user_id_created'] = $data['user_id_modified'];
            $data['timestamp_created'] = $data['timestamp_modified'];
        }
        $data['language_code'] = $site_language->code;

        if ($is_new_page) {
            LOGGER::info('Writing a new page: ' . $data['page_title'], 'PAGES', $this->formbuilder->getId());
        } else {
            LOGGER::info('Updating page: ' . $data['page_title'], 'PAGES', $this->formbuilder->getId());
        }

        $this->Page_model->saveById($this->formbuilder->getId(), $data);

        // If it is a new page, then we need to get DB ID
        $page_id = $this->formbuilder->getId() ? $this->formbuilder->getId() : $this->db->insert_id();


        if ($is_page_attached_to_menu) {
            if ($was_page_attached_to_menu) {
                $item_id = $current_menu_item->item_id;
                $this->Menu_model->saveById($item_id, $data); // Updating
            } else {
                $data['page_id'] = $page_id;
                $this->Menu_model->saveById(false, $data); // Inserting
                $item_id = $this->db->insert_id();
            }
        }

        $this->_clear_cache();
        $this->formbuilder->setUseSimpleSessionMessageOnSuccess(false);

        $view = $this->getAttribute('view');
        $url_suffix = $this->getAttribute('url_suffix');

        if ($is_new_page) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->setMessage('pages_dialog_write_new_success', '<a href="' . module_url() . 'edit/page_id-' . $page_id . '/language_code-' . $site_language->code . '">', '</a>', '<a href="' . ($site_language->is_default == 1 ? '' : $site_language->code . '/') . $data['page_uri'] . $url_suffix . '">', '</a>');

        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->setMessage('pages_dialog_page_updated', '<a href="' . ($site_language->is_default == 1 ? '' : $site_language->code . '/') . $data['page_uri'] . $url_suffix . '">', '</a>', '<a href="' . module_url() . 'edit/page_id-' . $page_id . '/language_code-' . $site_language->code . '">', '</a>');

        }
        return true;
    }

    public function delete()
    {
        $page_id = $this->input->getParam('page_id');

        LOGGER::info('Deleting page', 'PAGES', $page_id);

        $success = $this->Page_model->deleteById($page_id);
        $this->_clear_cache();

        // Setting the message and redirecting
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('pages_dialog_delete_page_success');

        if ($this->input->getParam('json') == 1) {
            if ($success) {
                $this->jsonResponse(true, 'OK');
            } else {
                $this->jsonResponse(false, 'Unable to page element, it might contain a submenu');
            }
        }

        $this->redirectBack();
    }

    public function deletemenuelement()
    {
        $item_id = $this->input->getParam('item_id');

        $success = false;

        if (!$this->Menu_model->hasChildren($item_id)) {
            $page_id = $this->Menu_model->getPageIdByItemId($item_id); // Must before the deletion code
            $this->Menu_model->deleteById($item_id);
            if ($page_id) {
                $success = $this->Page_model->deleteById($page_id);
                LOGGER::info('Deleting page', 'PAGES', $page_id);
            }

            $this->_clear_cache();

            if ($this->input->getParam('json') == 1) {
                if ($success) {
                    $this->jsonResponse(true, 'OK');
                } else {
                    $this->jsonResponse(false, 'Unable to delete menu element, it might contain a submenu');
                }
            }
        } else {
            $menuelement = $this->Menu_model->getById($item_id);

            if ($this->input->getParam('json') == 1) {
                if ($success) {
                    $this->jsonResponse(true, 'OK');
                } else {
                    $this->jsonResponse(false, sprintf($this->lang->line('pages_dialog_menu_contains_submenu_error'), $menuelement->item_name));
                }
            }

            // Setting the message and redirecting
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR);
            $this->simplesessionmessage->setMessage('pages_dialog_menu_contains_submenu_error', $menuelement->item_name);
        }

        $this->redirectBack();
    }

    public function move()
    {
        $direction = $this->input->getParam('direction');
        $id = $this->input->getParam('item_id');

        $this->Generic_model->move($id, $direction, $this->Menu_model->getTable(), 'parent_item_id', 'item_order', 'item_id');
        $this->_clear_cache();

        if ($this->input->getParam('json') == 1) {
            $this->jsonResponse(true, 'OK');
        }

        $this->redirectBack();
    }

    public function setdefault()
    {
        $site_language = $this->getAttribute('site_language');
        $this->Page_model->setDefault($this->input->getParam('page_id'), $site_language->code);
        $this->_clear_cache();

        $this->redirectBack();
    }

    private function _clear_cache()
    {
        try {
            $this->Page_model->clean_pages_cache();
        } catch (Exception $e) {
        }
        $this->cachedobjectmanager->cleanup('pages');
    }

    public function menuedit()
    {
        $site_language = $this->getAttribute('site_language');
        $item_id = $this->input->getParam('item_id');

        $menu = array('0' => $this->lang->line('pages_dialog_main_menu'));

        $this->formbuilder->setId($item_id)
            ->setTitle($this->lang->line('pages_menuedit_header'))
            ->setBackLink(module_url() . 'index/language_code-' . $site_language->code . '/view-' . $this->getAttribute('view'))
            ->setFeedObject($this->Menu_model)
            ->setDefinition(
                array(
                    'parent_item_id' => array(
                        'input_type' => FormBuilder::SELECTBOX,
                        'label' => $this->lang->line('pages_label_location_in_menu'),
                        'values' => $this->Menu_model->getMenuFlat(0, $site_language->code, $item_id, false, $menu),
                    ),
                    'item_name' => array(
                        'label' => $this->lang->line('pages_label_menu_item_name'),
                        'validation_rules' => 'trim|required|min_length[1]|callback__menu_item_name_check',
                    ),
                    'item_url' => array(
                        'label' => $this->lang->line('pages_label_element_uri'),
                        'validation_rules' => 'trim|required',
                    ),
                    'language_code' => array(
                        'input_type' => FormBuilder::HIDDEN,
                        'input_default_value' => $site_language->code
                    )
                )
            );

        $this->assign('item_id', $item_id)
            ->assign('form', $this->formbuilder->generate())
            ->display();
    }

    public function flush_html_cache()
    {
        $this->load->helper('number');
        $this->load->language('utilities');
        $this->auth->refreshSession();
        try {
            $stats = $this->Page_model->clean_pages_cache();

            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->setMessage('utilities_cache_successfully_cleaned', $stats['count'], byte_format($stats['size']));
        } catch (Exception $e) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR)
                ->setMessage('utilities_label_cache_unable_to_open_directory_might_be_empty');
        }

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }

    /**
     * Callback
     *
     * @param $str
     * @return bool
     */
    public function _menu_item_name_check($str)
    {
        if ($this->formbuilder->getId()) {
            $item = $this->Menu_model->getById($this->formbuilder->getId(), 'item_name, parent_item_id');

            if ($str == $item->item_name && $_POST['parent_item_id'] == $item->parent_item_id) {
                return true; // As nothing changed
            }
        }

        if ($this->Menu_model->itemExists($str, $_POST['parent_item_id'])) {
            $this->form_validation->set_message('_name_check', sprintf($this->lang->line('pages_dialog_item_already_in_selected_menu_branch'), $str));
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return mixed|string
     */
    private function _getView()
    {
        $view = $this->input->getParam('view');
        if (!$view) {
            $view = $this->auth->getSessionVariable('pages_view');
            if (!$view) {
                $view = 'simple'; // The default
            }
        }
        return $view;
    }

    /**
     * @param $input_groups
     * @param $page_id
     * @param $site_language
     * @return array
     */
    private function _getPageEditDefinition($input_groups, $page_id, $site_language)
    {
        $definition = array();
        $definition['page_title'] = array(
            'input_group' => $input_groups['document'],
            'validation_rules' => 'trim|required|min_length[1]',
            'label' => $this->lang->line('pages_label_document_title'),
            'description' => $this->lang->line('pages_label_document_title_desc'),
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['page_description'] = array(
            'input_group' => $input_groups['document'],
            'validation_rules' => '',
            'label' => $this->lang->line('pages_label_description'),
            'description' => $this->lang->line('pages_label_description_desc'),
            'input_type' => FormBuilder::TEXTAREA,
        );
        $definition['page_contents'] = array(
            'input_group' => $input_groups['document'],
            'validation_rules' => '',
            'label' => $this->lang->line('pages_label_contents'),
            'description' => $this->lang->line('pages_label_contents_desc'),
            'input_type' => FormBuilder::RTF,
        );
        $definition['page_keywords'] = array(
            'input_group' => $input_groups['document'],
            'validation_rules' => '',
            'label' => $this->lang->line('pages_label_keywords'),
            'description' => $this->lang->line('pages_label_keywords_desc'),
            'input_type' => FormBuilder::TEXTFIELD,
        );
        $definition['page_is_displayed_in_sitemap'] = array(
            'input_group' => $input_groups['document'],
            'validation_rules' => '',
            'input_default_value' => 1,
            'label' => $this->lang->line('pages_label_display_in_sitemap'),
            'description' => $this->lang->line('pages_label_display_in_sitemap_desc'),
            'input_type' => FormBuilder::CHECKBOX,
        );
        $definition['page_uri'] = array(
            'input_group' => $input_groups['document'],
            'validation_rules' => 'trim|niceuri',
            'label' => $this->lang->line('pages_label_document_uri'),
            'description' => $this->lang->line('pages_label_document_uri_desc'),
        );

        $menu_item = false;
        $menu_item = $page_id ? $this->Menu_model->getElementByPageId($page_id) : false;

        $menu = array('-1' => $this->lang->line('pages_dialog_hidden_menu'), '0' => $this->lang->line('pages_dialog_main_menu'));

        $menu_values = $this->Menu_model->getMenuFlat(0, $site_language->code, false, false, $menu);
        if ($menu_item) {
            foreach ($menu_values as $key => &$dontcare) {
                if ($key == $menu_item->item_id) {
                    unset($menu_values[$key]);
                    break;
                }
            }
        }

        $definition['parent_item_id'] = array(
            'input_group' => $input_groups['document'],
            'label' => $this->lang->line('pages_label_location_in_menu'),
            'values' => $menu_values,
            'input_type' => FormBuilder::SELECTBOX,
            'input_default_value' => ($this->input->getParam('parent_item_id') ? $this->input->getParam('parent_item_id') : -1) // Default -1 but if there is a get param set, use the get param
        );
        $definition['item_name'] = array(
            'input_group' => $input_groups['document'],
            'label' => $this->lang->line('pages_label_menu_item_name'),
            'input_type' => FormBuilder::TEXTFIELD,
            'validation_rules' => '',
        );

        // Only if the page is attached to menu
        if ($this->input->post('parent_item_id') != '-1') {
            $definition['item_name']['validation_rules'] = 'trim|required|min_length[1]';
        }

        if ($this->formbuilder->getId()) {
            $definition['user_id_created'] = array(
                'input_group' => $input_groups['info'],
                'label' => $this->lang->line('pages_label_user_id_created'),
                'input_is_editable' => false,
                'foreign_key_table' => $this->User_model->getTable(),
                'foreign_key_field' => 'user_id',
                'foreign_key_label_field' => 'user_email',
                'validation_rules' => '',
            );
            $definition['timestamp_created'] = array(
                'input_group' => $input_groups['info'],
                'label' => $this->lang->line('pages_label_timestamp_created'),
                'input_is_editable' => false,
                'validation_rules' => '',
            );
            $definition['user_id_modified'] = array(
                'input_group' => $input_groups['info'],
                'label' => $this->lang->line('pages_label_user_id_modified'),
                'input_is_editable' => false,
                'foreign_key_table' => $this->User_model->getTable(),
                'foreign_key_field' => 'user_id',
                'foreign_key_label_field' => 'user_email',
                'validation_rules' => '',
            );
            $definition['timestamp_modified'] = array(
                'input_group' => $input_groups['info'],
                'label' => $this->lang->line('pages_label_timestamp_modified'),
                'input_is_editable' => false,
                'validation_rules' => '',
            );
        }
        return array($definition, $menu_item);
    }

    /**
     * @param $status
     * @param $message
     */
    private function jsonResponse($status, $message)
    {
        $data = array();
        $data['status'] = $status ? '1' : '0';
        $data['message'] = $message;

        $this->output->set_header('Content-Type: application/json');
        die(json_encode($data));
    }

    /**
     * @param $view
     */
    private function redirectBack($view = false)
    {
        $site_language = $this->getAttribute('site_language');
        $language_code = $site_language->code;

        if (!$view) {
            $view = $this->input->getParam('view');
        }

        redirect(module_url() . 'index/language_code-' . $language_code . ($view ? '/view-' . $view : ''));
    }
}
