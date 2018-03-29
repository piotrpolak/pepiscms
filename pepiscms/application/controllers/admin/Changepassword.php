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
 * Change own password controller
 */
class Changepassword extends AdminController
{
    public function index()
    {
        if (!$this->auth->getDriver()->isPasswordChangeSupported()) {
            show_404();
        }

        $this->load->model('User_model');
        $this->load->model('Password_history_model');

        $is_password_expired = $this->auth->isUserPasswordExpired();
        $this->assign('is_password_expired', $is_password_expired);
        if ($is_password_expired) {
            $this->assign('adminmenu', '');
        }

        $this->load->library('FormBuilder');

        $definition = CrudDefinitionBuilder::create()
            ->withField('current_password')
            ->withInputType(FormBuilder::PASSWORD)
            ->withLabel($this->lang->line('changepassword_label_current_password'))
            ->end()
            ->withField('new_password')
            ->withInputType(FormBuilder::PASSWORD)
            ->withLabel($this->lang->line('changepassword_label_new_password'))
            ->end()
            ->withField('confirm_new_password')
            ->withInputType(FormBuilder::PASSWORD)
            ->withLabel($this->lang->line('changepassword_label_confirm_new_password'))
            ->end()
            ->build();

        $that = &$this;
        $this->formbuilder->setCallback(function ($data) use ($that) {
            if (!$that->User_model->validateByEmail($that->auth->getUser(), $data['current_password'])) {
                $that->formbuilder->setValidationErrorMessage($that->lang->line('changepassword_dialog_incorrect_current_password'));
                return false;
            }

            if ($data['new_password'] != $data['confirm_new_password']) {
                $that->formbuilder->setValidationErrorMessage($that->lang->line('changepassword_dialog_new_password_no_match'));
                return false;
            }

            if ($data['new_password'] == $data['current_password']) {
                $that->formbuilder->setValidationErrorMessage($that->lang->line('changepassword_dialog_new_password_must_be_different_from_the_old_one'));
                return false;
            }

            $password_last_used = $that->Password_history_model->getPasswordLastUsedDatetime($that->auth->getUserId(), $data['new_password']);
            if($password_last_used) {
                $that->formbuilder->setValidationErrorMessage(sprintf($that->lang->line('changepassword_dialog_password_already_used_in_past'), $password_last_used));
                return false;
            }

            if ($that->User_model->changePasswordByUserId($that->auth->getUserId(), $data['new_password'])) {
                $that->auth->refreshSession();
                LOGGER::info('Changing own password', 'USER');

                $that->load->library('SimpleSessionMessage');
                $that->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
                $that->simplesessionmessage->setMessage('changepassword_dialog_pasword_changed');

                redirect(admin_url());
            } else {
                $that->formbuilder->setValidationErrorMessage(sprintf($that->lang->line('changepassword_dialog_new_password_is_not_strong_enough'), $this->User_model->getMinimumAllowedPasswordLenght()));
                return false;
            }
        }, FormBuilder::CALLBACK_ON_SAVE);

        $this->formbuilder->setTitle($this->lang->line('changepassword_change_password'));
        $this->formbuilder->setDefinition($definition);
        $this->assign('form', $this->formbuilder->generate());
        $this->display();
    }
}
