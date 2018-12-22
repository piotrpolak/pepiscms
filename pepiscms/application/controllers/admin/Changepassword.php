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
    const NEW_PASSWORD_FIELD_NAME = 'new_password';
    const CONFIRM_NEW_PASSWORD_FIELD_NAME = 'confirm_new_password';
    const CURRENT_PASSWORD_FIELD_NAME = 'current_password';

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

        $this->load->library(array('FormBuilder', 'SimpleSessionMessage'));

        $builder = CrudDefinitionBuilder::create();

        if (!$is_password_expired) {
            $builder
                ->withField(self::CURRENT_PASSWORD_FIELD_NAME)
                    ->withInputType(FormBuilder::PASSWORD)
                    ->withLabel($this->lang->line('changepassword_label_current_password'))
                ->end();
        }

        $builder
            ->withField(self::NEW_PASSWORD_FIELD_NAME)
                ->withInputType(FormBuilder::PASSWORD)
                ->withLabel($this->lang->line('changepassword_label_new_password'))
            ->end()
            ->withField(self::CONFIRM_NEW_PASSWORD_FIELD_NAME)
                ->withInputType(FormBuilder::PASSWORD)
                ->withLabel($this->lang->line('changepassword_label_confirm_new_password'))
            ->end();

        $that = &$this;
        $this->formbuilder->setCallback(function ($data) use ($that, $is_password_expired) {
            if (!$is_password_expired) {
                if (!$that->User_model->validateByEmail($that->auth->getUser(), $data[self::CURRENT_PASSWORD_FIELD_NAME])) {
                    $that->formbuilder->setValidationErrorMessage($that->lang->line('changepassword_dialog_incorrect_current_password'));
                    return false;
                }

                if ($data[self::NEW_PASSWORD_FIELD_NAME] == $data[self::CURRENT_PASSWORD_FIELD_NAME]) {
                    $that->formbuilder->setValidationErrorMessage(
                        $that->lang->line('changepassword_dialog_new_password_must_be_different_from_the_old_one'));
                    return false;
                }
            }

            if ($data[self::NEW_PASSWORD_FIELD_NAME] != $data[self::CONFIRM_NEW_PASSWORD_FIELD_NAME]) {
                $that->formbuilder->setValidationErrorMessage($that->lang->line('changepassword_dialog_new_password_no_match'));
                return false;
            }

            $password_last_used = $that->Password_history_model->getPasswordLastUsedDatetime($that->auth->getUserId(),
                $data[self::NEW_PASSWORD_FIELD_NAME]);

            if ($password_last_used) {
                $that->formbuilder->setValidationErrorMessage(sprintf(
                    $that->lang->line('changepassword_dialog_password_already_used_in_past'), $password_last_used
                ));
                return false;
            }

            if ($that->User_model->changePasswordByUserId($that->auth->getUserId(), $data[self::NEW_PASSWORD_FIELD_NAME])) {
                $that->auth->refreshSession();
                LOGGER::info('Changing own password', 'USER');

                $that->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                    ->setMessage('changepassword_dialog_pasword_changed');

                redirect(admin_url());
            } else {
                $that->formbuilder->setValidationErrorMessage(sprintf(
                    $that->lang->line('changepassword_dialog_new_password_is_not_strong_enough'),
                    $this->User_model->getMinimumAllowedPasswordLenght()
                ));
                return false;
            }
        }, FormBuilder::CALLBACK_ON_SAVE);

        $this->formbuilder->setTitle($this->lang->line('changepassword_change_password'))
            ->setDefinition($builder->build());
        $this->assign('form', $this->formbuilder->generate())
            ->display();
    }
}
