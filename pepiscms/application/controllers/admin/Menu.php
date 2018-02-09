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
 * Menu management controller
 */
class Menu extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('cms_enable_pages') === FALSE || !$this->config->item('feature_is_enabled_menu')) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }

        $this->load->model('Menu_model');
        $this->load->model('Site_language_model');
        $this->load->language('pages');

        $this->assign('site_language', $this->Site_language_model->getLanguageByCode($this->input->getParam('language_code')));
        $view = $this->input->getParam('view');
        if (!$view) {
            $view = 'simple'; // The default
        }
        $this->assign('view', $view);
    }

    /** Callback * */
    protected function renderMenu()
    {
        $this->load->library('MenuRendor');
        return $this->menurendor->render('menu', $this->getMethodName(), $this->input->getParam('language_code'), 'pages');
    }

    public function edit()
    {
        $site_language = $this->getAttribute('site_language');
        $item_id = $this->input->getParam('item_id');

        $view = $this->input->getParam('view');
        if (!$view) {
            if (!$view) {
                $view = 'simple'; // The default
            }
        }
        $this->assign('view', $view);

        $this->load->library('FormBuilder');
        $this->formbuilder->setId($item_id);
        $this->formbuilder->setTitle($this->lang->line('menuelement_header_edit'));
        $this->formbuilder->setBackLink(admin_url() . 'pages/index/language_code-' . $site_language->code . '/view-' . $view);
        $this->formbuilder->setFeedObject($this->Menu_model);
        $menu = array('0' => $this->lang->line('pages_dialog_main_menu'));
        $this->formbuilder->setDefinition(
            array(
                'parent_item_id' => array(
                    'input_type' => FormBuilder::SELECTBOX,
                    'label' => $this->lang->line('pages_label_location_in_menu'),
                    'values' => $this->Menu_model->getMenuFlat(0, $site_language->code, $item_id, FALSE, $menu),
                ),
                'item_name' => array(
                    'label' => $this->lang->line('pages_label_menu_item_name'),
                    'validation_rules' => 'trim|required|min_length[1]|callback__name_check',
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

        $this->assign('item_id', $item_id);
        $this->assign('form', $this->formbuilder->generate());
        $this->display();
    }

    public function _name_check($str)
    {
        if ($this->formbuilder->getId()) {
            $item = $this->Menu_model->getById($this->formbuilder->getId(), 'item_name, parent_item_id');

            if ($str == $item->item_name && $_POST['parent_item_id'] == $item->parent_item_id) {
                return TRUE; // As nothing changed
            }
        }

        if ($this->Menu_model->itemExists($str, $_POST['parent_item_id'])) {
            $this->form_validation->set_message('_name_check', sprintf($this->lang->line('pages_dialog_item_already_in_selected_menu_branch'), $str));
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
