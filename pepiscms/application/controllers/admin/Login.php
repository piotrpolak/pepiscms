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
 * User login controller
 */
class Login extends EnhancedController
{
    /**
     * This code was moved from constructor due to compatibility problems with base_url();
     */
    private function _oninit()
    {
        $this->load->library('Auth');

        // Important 1! There should be 2 separate IFs otherwise some of the auth drivers will not work
        // Important 2! $this->auth->isAuthorized() should not be cached in a variable!

        if (!$this->auth->isAuthorized()) {
            $this->auth->onAuthRequest();
        }

        if ($this->auth->isAuthorized(true)) {
            $this->_do_post_login_redirect_if_case();

            $this->load->library('SecurityManager');

            if ($this->config->item('cms_customization_on_login_redirect_url')) {
                redirect(base_url() . $this->config->item('cms_customization_on_login_redirect_url'));
            } else {
                redirect(admin_url() . 'about/dashboard');
            }
        }
        $this->_init_variables();
    }

    private function _init_variables()
    {
        // Language
        $this->load->library('Auth');
        $language = $this->lang->getCurrentLanguage();
        if (!$language) {
            $language = $this->config->item('language');
        }

        $this->load->model('User_model');
        $this->lang->load('core', $language);
        $this->lang->load('login', $language);

        $this->assign('body_id', 'controller-login')
            ->assign('lang', $this->lang)
            ->assign('current_language', $language)
            ->assign('application_languages', $this->lang->getEnabledAdminLanguages())
            ->assign('site_name', $this->config->item('site_name'))
            ->assign('popup_layout', false)
            ->assign('auth_error', false)
            ->assign('account_is_locked', false);
    }

    private function _do_post_login_redirect_if_case()
    {
        if (isset($_SESSION['request_redirect'])) {
            if ($_SESSION['request_redirect'] == 'admin/logout') {
                // If it is annoying when you log in to be logged out
                unset($_SESSION['request_redirect']);
            } else {
                $r = $_SESSION['request_redirect'];
                unset($_SESSION['request_redirect']);
                redirect(base_url() . $r);
            }
        }

        return false;
    }

    public function refresh_session()
    {
        $this->load->library('Auth');
        $this->auth->refreshSession();

        $language = $this->lang->getCurrentLanguage();
        if (!$language) {
            $language = $this->config->item('language');
        }

        $this->lang->load('global', $language);
        $this->load->library('SimpleSessionMessage');
        $this->simplesessionmessage->setMessage('global_header_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect(admin_url());
        }
    }

    public function index()
    {
        $this->_oninit();
        $this->assign('user_email', '')
            ->display('admin/login_index');
    }

    public function logout()
    {
        $this->_init_variables();
        $this->assign('logoutsuccess', true)
            ->display('admin/login_logout');
    }

    public function dologin()
    {
        $this->_oninit();
        $this->load->library('Logger');

        $account_is_locked = $auth_error = $prevent_from_logging_in = false;

        // Preventing error display
        if (!(count($_POST) > 0)) {
            redirect(admin_url() . 'login');
        }

        if ($this->input->post('user_email') && $this->input->post('password')) {
            $user_email = strtolower($this->input->post('user_email'));
            $user_id = $this->User_model->getUserIdByEmail($user_email);

            if ($user_id) {
                $number_of_consecutive_ua_max = $this->config->item('security_number_of_unsuccessfull_authorizations_to_lock_account');


                $number_of_consecutive_ua = $this->User_model->getNumberOfConsecutiveUnsuccessfullAuthorizationsByUserId($user_id);

                if ($number_of_consecutive_ua_max) {
                    if ($number_of_consecutive_ua > $number_of_consecutive_ua_max) {
                        $account_is_locked = true;
                    } elseif ($number_of_consecutive_ua == $number_of_consecutive_ua_max) {
                        echo 1;
                        $account_is_locked = true;
                        $prevent_from_logging_in = true; // This is used only in the moment when the account is being locked
                        // Locking the account just once
                        $this->User_model->lockById($user_id);
                        // Need to be the basic method to be able to detect user ID
                        Logger::log('Locking user account for ' . $number_of_consecutive_ua . ' attempts ' . $user_email, Logger::MESSAGE_LEVEL_ERROR, 'LOGIN', $user_id, $user_id);
                    }
                }

                if (!$prevent_from_logging_in) {
                    if ($this->auth->authorize($user_email, $this->input->post('password'))) {
                        Logger::info('Login', 'LOGIN');

                        $this->_do_post_login_redirect_if_case();

                        $this->load->library('SecurityManager');
                        if ($this->config->item('cms_customization_on_login_redirect_url')) {
                            redirect(base_url() . $this->config->item('cms_customization_on_login_redirect_url'));
                        } else {
                            redirect(admin_url() . 'about/dashboard');
                        }
                    } else {
                        // Need to be the basic method to be able to detect user ID
                        Logger::log('Unable to login ' . $user_email, Logger::MESSAGE_LEVEL_WARNING, 'LOGIN', $user_id, $user_id);
                        $auth_error = true;


                        if ($number_of_consecutive_ua >= 2) {
                            sleep(4);
                        }
                    }
                }
            } else {
                // Need to be the basic method to be able to detect user ID
                Logger::log('Unable to login ' . $user_email, Logger::MESSAGE_LEVEL_WARNING, 'LOGIN', $user_id, $user_id);
                $auth_error = true;
            }
        }

        $this->assign('user_email', $this->input->post('user_email'))
            ->assign('auth_error', $auth_error)
            ->assign('account_is_locked', $account_is_locked)
            ->display('admin/login_index');
    }

    public function sessionexpired()
    {
        $this->_oninit();
        $this->assign('sessionexpired', true);
        $this->index();
    }

    public function json_session_refresh()
    {
        $this->load->library('Auth');

        $success = array('success' => 0);
        if ($this->auth->isAuthorized()) {
            $success = array('success' => 1);
        }

        echo json_encode($success);
    }
}
