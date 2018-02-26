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

        $this->formbuilder->setCallback(array($this, '_fb_callback_on_save'), FormBuilder::CALLBACK_ON_SAVE);
        $this->formbuilder->setCallback(array($this, '_fb_callback_before_render'), FormBuilder::CALLBACK_BEFORE_RENDER);
        $this->formbuilder->setApplyButtonEnabled(true);

        $this->load->helper('string');
        $definition = array(
            'display_name' => array(
                'filter_type' => DataGrid::FILTER_BASIC,
                'validation_rules' => 'required',
                'show_in_grid' => false,
                'input_group' => 'cms_users_input_group_main',
            ),
            'user_email' => array(
                'description' => $this->lang->line('cms_users_user_email_description'),
                'filter_type' => DataGrid::FILTER_BASIC,
                'show_in_grid' => false,
                'validation_rules' => 'strtolower|trim|required|valid_email',
                'input_group' => 'cms_users_input_group_main',
            ),
            'password' => array(
                'show_in_grid' => false,
                'validation_rules' => 'trim|min_length[4]',
                'input_type' => FormBuilder::PASSWORD,
                'input_group' => 'cms_users_input_group_main',
            ),
            'groups_label' => array(
                'label' => $this->lang->line('cms_users_groups'),
                'show_in_form' => false,
                'grid_is_orderable' => false,
                'grid_formating_callback' => array($this, '_datagrid_format_groups_label_column'),
                'input_group' => 'cms_users_input_group_main',
            ),
            'is_root' => array(
                'description' => $this->lang->line('cms_users_is_root_description'),
                'input_type' => FormBuilder::CHECKBOX,
                'show_in_grid' => false,
                'validation_rules' => '',
                'input_default_value' => 1,
                'values' => array(0 => $this->lang->line('global_dialog_no'), 1 => $this->lang->line('global_dialog_yes')),
                'input_group' => 'cms_users_input_group_main',
            ),
            'groups' => array(
                'input_type' => FormBuilder::MULTIPLECHECKBOX,
                'show_in_grid' => false,
                'validation_rules' => '',
                'foreign_key_relationship_type' => FormBuilder::FOREIGN_KEY_MANY_TO_MANY,
                'foreign_key_table' => $this->config->item('database_table_groups'),
                'foreign_key_field' => 'group_id',
                'foreign_key_label_field' => 'group_name',
                'foreign_key_junction_id_field_right' => 'group_id',
                'foreign_key_junction_id_field_left' => 'user_id',
                'foreign_key_junction_table' => $this->config->item('database_table_user_to_group'),
                'input_group' => 'cms_users_input_group_main',
            ),
            'status' => array(
                'description' => $this->lang->line('cms_users_status_description'),
                'input_type' => FormBuilder::SELECTBOX,
                'values' => array(0 => $this->lang->line('cms_users_status_inactive'), 1 => $this->lang->line('cms_users_status_active')),
                'input_default_value' => 1,
                'input_group' => 'cms_users_input_group_main',
            ),
            'is_locked' => array(
                'description' => $this->lang->line('cms_users_is_locked_description'),
                'input_type' => FormBuilder::CHECKBOX,
                'input_is_editable' => false,
                'input_default_value' => 0,
                'validation_rules' => '',
                'values' => array(0 => $this->lang->line('cms_users_status_is_locked_no'), 1 => $this->lang->line('cms_users_status_is_locked_yes')),
                'input_group' => 'cms_users_input_group_main',
            ),
            'send_email_notification' => array(
                'description' => $this->lang->line('cms_users_send_email_notification_description'),
                'input_type' => FormBuilder::CHECKBOX,
                'show_in_grid' => false,
                'validation_rules' => '',
                'input_group' => 'cms_users_input_group_main',
            ),
            'user_login' => array(
                'description' => $this->lang->line('cms_users_user_login_description'),
                'filter_type' => DataGrid::FILTER_BASIC,
                'show_in_grid' => true,
                'validation_rules' => 'strtolower|trim|min_length[4]|alpha_numeric|max_length[128]',
                'input_group' => 'cms_users_input_group_secondary',
            ),
            'title' => array(
                'validation_rules' => false,
                'input_group' => 'cms_users_input_group_secondary',
            ),
            'image_path' => array(
                'show_in_grid' => false,
                'input_type' => FormBuilder::IMAGE,
                'upload_path' => INSTALLATIONPATH . 'application/users/',
                'upload_display_path' => 'application/users/',
                'upload_complete_callback' => array($this, '_fb_callback_make_filename_seo_friendly'),
                'input_group' => 'cms_users_input_group_secondary',
            ),
            'phone_number' => array(
                'validation_rules' => '',
                'filter_type' => DataGrid::FILTER_BASIC,
                'validation_rules' => 'remove_separators|valid_phone_number',
                'input_group' => 'cms_users_input_group_secondary',
            ),
            'birth_date' => array(
                'input_type' => FormBuilder::DATE,
                'label' => $this->lang->line('cms_users_birth_date'),
                'show_in_grid' => false,
                'validation_rules' => '',
                'input_group' => 'cms_users_input_group_secondary',
            ),
            'password_last_changed_timestamp' => array(
                'show_in_form' => false,
                'input_group' => 'cms_users_input_group_main',
            ),
            'alternative_email' => array(
                'input_type' => FormBuilder::DATE,
                'show_in_grid' => false,
                'validation_rules' => 'strtolower|trim|valid_email',
                'input_group' => 'cms_users_input_group_secondary',
            ),
            'note' => array(
                'validation_rules' => '',
                'show_in_grid' => false,
                'input_type' => FormBuilder::TEXTAREA,
                'input_group' => 'cms_users_input_group_secondary',
            ),
        );


        if (!$this->auth->getDriver()->isPasswordChangeSupported()) {
            unset($definition['login']);
            unset($definition['password']);
            unset($definition['password_last_changed_timestamp']);
            unset($definition['is_locked']);
        }


        // Getting translations and setting input groups
        foreach ($definition as $field => &$def) {
            $key = isset($def['field']) ? $def['field'] : $field;

            // Getting label
            if (!isset($def['label'])) {
                $def['label'] = $this->lang->line($module_name . '_' . $key);
            }

            // Getting description
            if (!isset($def['description'])) {
                $description = $this->lang->line($module_name . '_' . $key . '_description', false);
                if ($description !== false) {
                    $def['description'] = $description;
                }
            }

            // Setting default input group
            if (!isset($def['input_group']) || !$def['input_group']) {
                $def['input_group'] = 'default';
            }
        }

        $this->datagrid->setRowCssClassFormattingFunction(array($this, '_datagrid_row_callback'));
        $this->setDefinition($definition);
        $this->setMetaOrderField('display_name', $this->lang->line($lang_field_prefix . 'display_name'));
        $this->setMetaTitlePattern(array($this, '_fb_format_title_and_modify_option'));
        $this->setMetaImageField('image_path', 'application/users/');
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

                $user_id = $this->User_model->register($data['display_name'], strtolower($data['user_email']), strtolower($data['user_login']), $password, $groups_ids, $is_root, $data['send_email_notification'], $data);

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
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');

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
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');

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
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');

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
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            $this->simplesessionmessage->setMessage('global_header_success');
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_ERROR);
            $this->simplesessionmessage->setRawMessage('Unable to reset password. Please try again.');
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
     * Callback function changing the name of the file to SEO friendly
     *
     * @version: 1.2.1
     * @date: 2015-03-04
     * @param string $filename
     * @param type $base_path
     * @param string $data
     * @param string $current_image_field_name
     * @return bool
     */
    public function _fb_callback_make_filename_seo_friendly(&$filename, $base_path, &$data, $current_image_field_name)
    {
        // List of the fields to be used, if no value is present for a given key
        // then the key will be ignored. By default all values of the keys
        // specified will be concatenated
        $title_field_names = array('name', 'title', 'label');

        $this->load->helper('string');
        $path = $base_path . $filename;
        $path_parts = pathinfo($path);

        // Attempt to build a name
        $new_base_filename = '';
        foreach ($title_field_names as $title_field_name) {
            // Concatenating all the elements
            if (isset($data[$title_field_name]) && $data[$title_field_name]) {
                $new_base_filename .= '-' . $data[$title_field_name];
            }
        }

        // Making it web safe
        if ($new_base_filename) {
            $new_base_filename = niceuri($new_base_filename);
        }

        // This should not be an else statement as niceuri can return empty string sometimes
        if (!$new_base_filename) {
            $new_base_filename = niceuri($path_parts['filename']);
        }

        // This should normally never happen, but who knows - this is bulletproof
        if (!$new_base_filename) {
            $new_base_filename = md5(time() + rand(1000, 9999));
        }

        $new_base_path = '';

        // We don't like upper case extensions
        $extension = strtolower($path_parts['extension']);
        $new_name = $new_base_filename . '.' . $extension;

        // Protection against existing files
        $i = 2;
        while (file_exists($base_path . $new_base_path . $new_name)) {
            $new_name = $new_base_filename . '-' . $i . '.' . $extension;
            if ($i++ > 50 || strlen($i) > 2) { // strlen is a protection against the infinity loop for md5 checksums
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
            $filename = $new_base_path . $new_name;

            return true;
        }
        return false;
    }
}
