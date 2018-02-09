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

        $this->assign('warning', '');

        $is_password_expired = $this->auth->isUserPasswordExpired();
        $this->assign('is_password_expired', $is_password_expired);
        if ($is_password_expired) {
            $this->assign('adminmenu', '');
        }

        // TODO WHENEVER USER AUTH DRIVER WAS DIFFERENT THAN native
        // This is used whren migrating from CAS to NATIVE driver
        $is_user_password_virgin = FALSE;

        // On form submit
        if ($this->input->post('confirm')) {
            if ($is_user_password_virgin || ($this->input->post('password') && $this->User_model->validateByEmail($this->auth->getUser(), $this->input->post('password')))) {
                if (strlen($this->input->post('new_password')) > 0) {
                    if ($this->input->post('new_password') == $this->input->post('confirm_new_password')) {
                        if ($is_user_password_virgin || $this->input->post('password') != $this->input->post('new_password')) {
                            if ($this->User_model->changePasswordByUserId($this->auth->getUserId(), $this->input->post('new_password'))) {
                                $this->auth->refreshSession();
                                LOGGER::info('Changing own password', 'USER');

                                $this->load->library('SimpleSessionMessage');
                                $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
                                $this->simplesessionmessage->setMessage('changepassword_dialog_pasword_changed');

                                redirect(admin_url());
                            } else {
                                $this->assign('warning', sprintf($this->lang->line('changepassword_dialog_new_password_is_not_strong_enough'), $this->User_model->getMinimumAllowedPasswordLenght()));
                            }
                        } else {
                            $this->assign('warning', $this->lang->line('changepassword_dialog_new_password_must_be_different_from_the_old_one'));
                        }
                    } else {
                        // password does not match
                        $this->assign('warning', $this->lang->line('changepassword_dialog_new_password_no_match'));
                    }
                } else {
                    // password too short
                    $this->assign('warning', $this->lang->line('changepassword_dialog_new_password_too_short'));
                }
            } else {
                // Wrong password
                $this->assign('warning', $this->lang->line('changepassword_dialog_incorrect_current_password'));
            }
        }

        $this->assign('is_user_password_virgin', $is_user_password_virgin);
        $this->display();
    }

}
