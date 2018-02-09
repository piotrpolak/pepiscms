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
 * User groups management Controller
 */
class Cms_groupsAdmin extends ModuleAdminController
{
    private $module_name = 'cms_groups';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Group_model');
        $this->load->moduleLanguage($this->module_name);
        $this->load->language('acl');
        $this->load->moduleLanguage('crud');

        $this->assign('title', $this->lang->line($this->module_name . '_module_name'));
    }

    public function index()
    {
        $this->load->library('SimpleSessionMessage');
        $this->load->library('DataGrid');

        $this->datagrid->setTitle($this->lang->line($this->module_name . '_module_name'));
        $this->datagrid->setFeedObject($this->Group_model);
        $this->datagrid->setBaseUrl(module_url());
        $this->datagrid->addColumn($this->lang->line('cms_groups_group_name'), 'group_name', array($this, '_datagrid_format_name_column'));

        $this->assign('datagrid', $this->datagrid->generate());
        $this->display();
    }

    public function _datagrid_format_name_column($content, $line)
    {
        $out = '';
        $out .= '<div class="details">';
        $out .= '<span class="title">' . $content . '</span>';

        $out .= '<span class="description">';
        $out .= '<span class="separable">';

        if (SecurityManager::hasAccess('cms_groups', 'edit')) {
            $out .= '<a href="' . module_url() . 'edit/id-' . $line->group_id . '">' . $this->lang->line('crud_label_modify') . '</a>';
        }

        if (SecurityManager::hasAccess('cms_groups', 'delete')) {
            $out .= '<a href="' . module_url() . 'delete/id-' . $line->group_id . '" class="delete ask_for_confirmation">' . $this->lang->line('global_button_delete') . '</a>';
        }

        $out .= ' </span>
		</span>' .
            '</div>';

        return $out;
    }

    public function edit()
    {
        $group_id = $this->input->getParam('id');

        $add_new = !($group_id > 0);
        $this->assign('add_new', $add_new);

        if ($add_new) {
            $_POST['initial_group_name'] = FALSE;
        }

        $this->load->library('form_validation');

        $config = array(
            array(
                'field' => 'display_name',
                'label' => $this->lang->line($this->module_name . '_label_group_name'),
                'rules' => 'required|min_length[3]|trim|callback__display_name_check'
            )
        );
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters(get_warning_begin(), get_warning_end());

        // On form submit
        if (isset($_POST['confirm'])) {
            $access = array();
            if (isset($_POST['access'])) {
                foreach ($_POST['access'] as $entity => $value) {
                    if (!$entity) {
                        continue;
                    }

                    if ($value == 'FULL_CONTROL' || $value == 'FULL_CONTROLL') {
                        $access[$entity] = SecurityPolicy::FULL_CONTROL;
                    } elseif ($value == 'WRITE') {
                        $access[$entity] = SecurityPolicy::WRITE;
                    } elseif ($value == 'READ') {
                        $access[$entity] = SecurityPolicy::READ;
                    } else {
                        $access[$entity] = SecurityPolicy::NONE;
                    }
                }
            }

            if ($this->form_validation->run() == true) {
                $this->load->library('SimpleSessionMessage');
                $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);

                if (!$add_new) {
                    $this->Group_model->update($group_id, $_POST['display_name'], $access);
                    $this->simplesessionmessage->setMessage('global_header_success');
                } else {
                    // Validation ok
                    $id = $this->Group_model->insertGroup($_POST['display_name'], $access);
                    $this->simplesessionmessage->setMessage($this->module_name . '_dialog_group_added_success');
                }
                if (!isset($_POST['apply'])) {
                    redirect(module_url());
                }
            }
        }

        $group = new stdClass();
        $group->group_name = ''; // When adding a new item

        if (!$add_new) {
            $group = $this->Group_model->getById($group_id);
        }

        $entities_grouped = array('system' => array(), 'core_modules' => array(), 'userspace_modules' => array());
        $entities = $this->securitypolicy->getAllAvailableEntities();
        foreach ($entities as $module_name => $entitiesssss) {
            if ($module_name == 'system') {
                $module_group = 'system';
            } elseif ($this->Module_model->isCoreModule($module_name)) {
                $module_group = 'core_modules';
            } else {
                $module_group = 'userspace_modules';
            }

            $entities_grouped[$module_group][$module_name] = $entitiesssss;
        }

        $this->assign('group', $group);
        $this->assign('entities', $entities_grouped);
        $this->display();
    }

    public function _display_name_check($str)
    {
        if ($_POST['initial_group_name'] != $str && $this->Group_model->isGroupNameTaken($str)) {
            $this->form_validation->set_message('_display_name_check', $this->lang->line($this->module_name . '_dialog_group_already_exists'));
            return FALSE;
        }
        return TRUE;
    }

    public function delete()
    {
        $group_id = $this->input->getParam('id');

        $this->Group_model->deleteById($group_id);

        $this->load->library('SimpleSessionMessage');
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage($this->module_name . '_dialog_delete_group_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }
}
