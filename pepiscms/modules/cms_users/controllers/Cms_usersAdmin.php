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
 * See AdminCRUDController for the list of the methods you can use in your constructor
 * Some methods such as _onDelete should be overwritten if you use external resources such as images
 */
class Cms_usersAdmin extends AdminCRUDController
{
    private $module_name = 'cms_users';

    public function __construct()
    {
        parent::__construct();

        $lang_field_prefix = $this->module_name . '_';
        $module_name = $this->module_name;
        $this->load->moduleLanguage('cms_users');

        $this->setPageTitle($this->lang->line($lang_field_prefix . 'module_name'));
        $this->setAddNewItemLabel($this->lang->line($lang_field_prefix . 'add'));

        $this->setPopupEnabled(false);

        $this->load->helper('string');
        $this->load->model('Group_model');
        $this->load->model('User_model');
        $this->setFeedObject($this->User_model);
        $this->datagrid->setDefaultOrder('display_name', 'ASC');

        $this->setDeletable(false);
        $this->setAddable(true);
        $this->setEditable(true);
        $this->setPreviewable(false);

        if (!$this->auth->getDriver()->isPasswordChangeSupported()) {
            $this->setAddable(false);
            $driver_name = strtoupper(str_replace('_Auth_Driver', '', get_class($this->auth->getDriver())));
            $this->setTooltipTextForIndex(sprintf($this->lang->line($lang_field_prefix . 'index_tip_password_change_not_supported'), $driver_name, $driver_name));
        }

        $this->formbuilder->setCallback(array($this, '_fb_callback_on_save'), FormBuilder::CALLBACK_ON_SAVE)
            ->setCallback(array($this, '_fb_callback_before_render'), FormBuilder::CALLBACK_BEFORE_RENDER)
            ->formbuilder->setApplyButtonEnabled(true);

        $this->load->helper('string');

        $definition = CrudDefinitionBuilder::create()
            ->withField('display_name')
                ->withShowInGrid(false)
                ->withFilterType( DataGrid::FILTER_BASIC)
                ->withValidationRules('required')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('user_email') //  $this->lang->line('cms_users_user_email_description'),
                ->withShowInGrid(false)
                ->withFilterType( DataGrid::FILTER_BASIC)
                ->withValidationRules('strtolower|trim|required|valid_email')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('password')
                ->withInputType(FormBuilder::PASSWORD)
                ->withShowInGrid(false)
                ->withValidationRules('trim|min_length[4]')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('groups_label')
                ->withLabel('cms_users_groups')
                ->withShowInForm(false)
                ->withGridIsOrderable(false)
                ->withGridFormattingCallback(array($this, '_datagrid_format_groups_label_column'))
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('is_root') //  $this->lang->line('cms_users_is_root_description')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withShowInGrid(false)
                ->withValidationRules('')
                ->withInputDefaultValue(1)
                ->withValues(array(0 => $this->lang->line('global_dialog_no'), 1 => $this->lang->line('global_dialog_yes')))
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('groups')
                ->withInputType(FormBuilder::MULTIPLECHECKBOX)
                ->withShowInGrid(false)
                ->withForeignKeyRelationshipType(FormBuilder::FOREIGN_KEY_MANY_TO_MANY)
                ->withForeignKeyTable($this->config->item('database_table_groups'))
                ->withForeignKeyIdField('group_id')
                ->withForeignKeyLabelField('group_name')
                ->withForeignKeyJunctionIdFieldRight('group_id')
                ->withForeignKeyJunctionIdFieldLeft('user_id')
                ->withForeignKeyJunctionTable($this->config->item('database_table_user_to_group'))
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('status') //  $this->lang->line('cms_users_status_description')
                ->withInputType(FormBuilder::SELECTBOX)
                ->withInputDefaultValue(1)
                ->withValues(array(0 => $this->lang->line('cms_users_status_inactive'), 1 => $this->lang->line('cms_users_status_active')))
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('is_locked')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withInputIsEditable(false)
                ->withInputDefaultValue(0)
                ->withValues(array(0 => $this->lang->line('cms_users_status_is_locked_no'), 1 => $this->lang->line('cms_users_status_is_locked_yes')))
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('send_email_notification') // $this->lang->line('cms_users_send_email_notification_description')
                ->withInputType(FormBuilder::CHECKBOX)
                ->withShowInGrid(false)
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('user_login')
                ->withFilterType( DataGrid::FILTER_BASIC)
                ->withValidationRules('strtolower|trim|min_length[4]|alpha_numeric|max_length[128]')
                ->withInputGroup('cms_users_input_group_secondary')
            ->end()
            ->withField('title')
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_secondary')
            ->end()
            ->withField('phone_number')
                ->withFilterType( DataGrid::FILTER_BASIC)
                ->withValidationRules('remove_separators|valid_phone_number')
                ->withInputGroup('cms_users_input_group_secondary')
            ->end()
            ->withField('birth_date')
                ->withInputType(FormBuilder::DATE)
                ->withShowInGrid(false)
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_secondary')
            ->end()
            ->withField('password_last_changed_timestamp')
                ->withInputType(FormBuilder::DATE)
                ->withShowInForm(false)
                ->withInputGroup('cms_users_input_group_main')
            ->end()
            ->withField('alternative_email')
                ->withShowInGrid(false)
                ->withValidationRules('strtolower|trim|valid_email')
                ->withInputGroup('cms_users_input_group_secondary')
            ->end()
            ->withField('note')
                ->withInputType(FormBuilder::TEXTAREA)
                ->withShowInGrid(false)
                ->withValidationRules('')
                ->withInputGroup('cms_users_input_group_secondary')
            ->end()
            ->withImplicitTranslations($this->module_name, $this->lang)
            ->build();


        if (!$this->auth->getDriver()->isPasswordChangeSupported()) {
            unset($definition['login']);
            unset($definition['password']);
            unset($definition['password_last_changed_timestamp']);
            unset($definition['is_locked']);
        }


        $this->datagrid->setRowCssClassFormattingFunction(array($this, '_datagrid_row_callback'));
        $this->setDefinition($definition);
        $this->setMetaOrderField('display_name', $this->lang->line($lang_field_prefix . 'display_name'));
        $this->setMetaTitlePattern(array($this, '_fb_format_title_and_modify_option'));
    }

    public function _fb_format_title_and_modify_option($content, $line)
    {
        $this->removeMetaActions();
        if ($line->status == 0) {
            $this->setEditable(false);
            if (SecurityManager::hasAccess('users', 'activate', 'users')) {
                $this->addMetaAction(module_url() . 'activate/id-{user_id}', $this->lang->line($this->module_name . '_activate'), '', false);
            }
        } else {
            $this->setEditable(true);

            if (SecurityManager::hasAccess('users', 'reset_password', 'users') && $this->auth->getDriver()->isPasswordChangeSupported()) {
                $this->addMetaAction(module_url() . 'reset_password/id-{user_id}', $this->lang->line($this->module_name . '_reset_password'), 'ask_to_confirm', false);
            }


            if (SecurityManager::hasAccess('users', 'inactivate', 'users')) {
                $this->addMetaAction(module_url() . 'inactivate/id-{user_id}', $this->lang->line($this->module_name . '_inactivate'), 'delete ask_to_confirm', false);
            }

            if ($line->is_locked) {
                if (SecurityManager::hasAccess('users', 'unlock', 'users')) {
                    $this->addMetaAction(module_url() . 'unlock/id-{user_id}', $this->lang->line($this->module_name . '_unlock'), 'ask_to_confirm', false);
                }
            }
        }

        return $line->display_name . ' - ' . $line->user_email;
    }

    public function _datagrid_format_groups_label_column($content, $line)
    {
        if ($line->is_root) {
            return '<b>' . $this->lang->line('cms_users_is_root') . '</b>';
        }
        return $content;
    }

    public function _datagrid_row_callback($line)
    {
        if (!$line->status) {
            return DataGrid::ROW_COLOR_GRAY;
        }

        if ($line->is_locked) {
            return DataGrid::ROW_COLOR_RED;
        }

        if ($line->account_type != 0) {
            return DataGrid::ROW_COLOR_ORANGE;
        }

        return false;
    }

    /**
     * Can manipulate data after read, before rendering
     * @param object $object
     */
    public function _fb_callback_before_render(&$object)
    {
        if (isset($object->password)) {
            unset($object->password);
        }
    }

    /**
     * Callback, input array as parameter must be passed by reference!
     *
     * @param Array $data
     * @return boolean
     */
    public function _fb_callback_on_save(&$data)
    {
        $user_id = $this->formbuilder->getId();

        if (!$data['birth_date'] || $data['birth_date'] = '0000-00-00') {
            $data['birth_date'] = null;
        }

        // Validating user login
        $data['user_login'] = trim(strtolower($data['user_login']));
        if ($data['user_login']) {
            $user_id_by_login = $this->User_model->getUserIdByUserLogin($data['user_login']);
            if ($user_id_by_login) {
                if ($user_id) {
                    if ($user_id != $user_id_by_login) {
                        // Login already taken
                        $this->formbuilder->setValidationErrormessage(sprintf($this->lang->line('cms_users_dialog_login_already_in_database'), $this->input->post('user_login')));
                        return false;
                    }
                } else {
                    // Login already taken
                    $this->formbuilder->setValidationErrormessage(sprintf($this->lang->line('cms_users_dialog_login_already_in_database'), $this->input->post('user_login')));
                    return false;
                }
            }
        }

        $groups = $this->Generic_model->getAssocPairs('group_id', 'group_name', $this->config->item('database_table_groups'));

        // Only root can make another user a root
        $is_root = null;
        if (isset($data['is_root']) && $this->auth->isUserRoot()) {
            $is_root = $data['is_root'] == 1;
        }

        // Filtering users
        $groups_ids = null;
        if (isset($data['groups'])) {
            $groups_ids = is_array($data['groups']) ? $data['groups'] : array();
        }

        $password = (isset($data['password']) && trim($data['password']) ? trim($data['password']) : false);

        if ($user_id) {
            if ($password) {
                if (!$this->User_model->changePasswordByUserId($this->formbuilder->getId(), $password)) {
                    $this->formbuilder->setValidationErrorMessage(sprintf($this->lang->line('changepassword_dialog_new_password_is_not_strong_enough'), $this->User_model->getMinimumAllowedPasswordLenght()));
                    return false;
                }
            }

            // Updating
            return $this->User_model->update($this->formbuilder->getId(), $data['display_name'], strtolower($data['user_login']), $groups_ids, false, $is_root, $data);
        } else {
            // Checking if the email is free
            if (!$this->User_model->emailExists($data['user_email'])) {
                // For systems that have no groups and all the users are roots
                if (count($groups) == 0 && $this->auth->isUserRoot()) {
                    $is_root = true;
                }

                $this->User_model->register($data['display_name'], strtolower($data['user_email']), strtolower($data['user_login']), $password, $groups_ids, $is_root, $data['send_email_notification'], $data);

                $this->simplesessionmessage->setMessage('cms_users_dialog_user_registered_success');
                return true;
            } else {
                // Error, email exists
                $this->formbuilder->setValidationErrormessage(sprintf($this->lang->line('cms_users_dialog_email_already_in_database'), $this->input->post('user_email')));
                return false;
            }
        }
    }

    public function edit($display_view = true)
    {
        $id = $this->input->getParam('id');
        $sameuser = $this->auth->getUserId() == $id;

        $definition = $this->getDefinition();

        if ($this->auth->isUserRoot()) {
            $input_type = FormBuilder::CHECKBOX;
            if (count($this->Generic_model->getAssocPairs('group_id', 'group_name', $this->config->item('database_table_groups'))) == 0) {
                $input_type = FormBuilder::HIDDEN;
            }

            $definition['is_root']['input_type'] == $input_type;
            // Change definition of is_root
        }

        if ($sameuser) {
            unset($definition['note']);
            $this->formbuilder->setReadOnly();
        }

        $this->assign('non_standard_account', false);
        // For existing users
        if ($id) {
            unset($definition['send_email_notification']);
            $definition['user_email']['input_is_editable'] = false;


            $user = $this->getFeedObject()->getById($id, 'account_type');
            if ($user->account_type != 0) {
                $this->assign('non_standard_account', true);
                $this->formbuilder->setReadOnly();
            }
        }

        $this->setDefinition($definition);
        parent::edit();
    }

    public function inactivate()
    {
        $user_id = $this->input->getParam('id');

        if ($user_id == $this->auth->getUserId()) {
            show_404();
        }

        $this->User_model->inactivateById($user_id);

        // Setting the message and redirecting
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
            ->setMessage('global_header_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }

    public function activate()
    {
        $user_id = $this->input->getParam('id');

        $this->User_model->activateById($user_id);

        // Setting the message and redirecting
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
            ->setMessage('global_header_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }

    public function unlock()
    {
        $user_id = $this->input->getParam('id');

        $this->User_model->unlockById($user_id);

        // Setting the message and redirecting
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
            ->setMessage('global_header_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }

    public function reset_password()
    {
        if (!$this->auth->getDriver()->isPasswordChangeSupported()) {
            show_404();
        }

        $user_id = $this->input->getParam('id');

        if ($this->User_model->resetPasswordByUserId($user_id)) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->setMessage('global_header_success');
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR)
                ->setRawMessage('Unable to reset password. Please try again.');
        }

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(module_url());
        }
    }
}
