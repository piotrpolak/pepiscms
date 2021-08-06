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
 * CAS authentication driver
 *
 * @since 0.2.2
 */
class CasAuthDriver extends ContainerAware implements AuthDriverableInterface
{
    private $is_initialized = false;
    protected $conf = array();
    protected $auth = null;

    /**
     * Constructs auth driver
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->load->model('User_model');
        $this->load->language('auth');
        $this->conf = $this->config->item('auth_driver_options');

        $this->auth = $auth;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return class_exists('phpCAS');
    }


    private function _init()
    {
        if (!$this->is_initialized) {
            $this->is_initialized = true;

            if (!isset($this->conf['cas_host']) || !$this->conf['cas_host']) {
                show_error(sprintf($this->lang->line('auth_cas_misconfig'), INSTALLATIONPATH));
            }

            phpCAS::client(CAS_VERSION_2_0, $this->conf['cas_host'], (int)$this->conf['cas_port'], $this->conf['cas_url']);
            phpCAS::setNoCasServerValidation();
        }
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user_email_or_login, $password)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function onAuthRequest()
    {
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $_GET);
        $this->_init();
        phpCAS::forceAuthentication();

        $cas_user = phpCAS::getUser();
        // validate cas user
        $this->validateCasUser($cas_user);

        // Trying to get an existing user
        $user_id = $this->User_model->getUserIdByEmail($cas_user);

        // Trying to register an user
        if ($user_id === false) {
            // Checking whether allowed usernames are set and whether an user is one of them
            if (isset($this->conf['allowed_usernames']) && count($this->conf['allowed_usernames']) > 0) {
                if (!in_array($cas_user, $this->conf['allowed_usernames'])) {
                    Logger::warning('Username ' . $cas_user . ' is not allowed by AUTH driver option allowed_usernames.', 'AUTH');
                    return false;
                }
            }

            // Checking whether allowed domains are set and whether an user belongs to a trusted domain
            if (isset($this->conf['allowed_domains']) && count($this->conf['allowed_domains']) > 0) {
                $domain = substr($cas_user, strpos($cas_user, '@') + 1);
                if (!in_array($domain, $this->conf['allowed_domains'])) {
                    Logger::warning('User domain ' . $domain . ' is not allowed.', 'AUTH');
                    return false;
                }
            }

            $status = 1; // You might want to change it to 0
            if (isset($this->conf['implicit_user_status'])) {
                $status = $this->conf['implicit_user_status'];
            }

            $user_group_ids = array();
            if (isset($this->conf['implicit_user_group_ids']) && is_array($this->conf['implicit_user_group_ids'])) {
                $user_group_ids = $this->conf['implicit_user_group_ids'];
            }

            $display_name = explode('@', $cas_user);
            $display_name = ucwords(str_replace(array('.', '-', '_'), ' ', $display_name[0]));

            $is_root = !$this->User_model->countAll();
            $this->User_model->register($display_name, $cas_user, false, false, $user_group_ids, $is_root, false, array('status' => $status));
            $user_id = $this->User_model->getUserIdByEmail($cas_user);
        }

        if ($user_id) {
            if (!$this->auth->forceLogin($user_id)) {
                Logger::warning('Attempt to login using inactive account (deactivated locally). User ID ' . $user_id, 'AUTH');
                show_error($this->lang->line('auth_cas_attempt_to_login_using_inactive_account'), 403, $this->lang->line('auth_no_access'));
            }
            return true;
        }

        Logger::warning('Unable to login using CAS driver. Unknown reason. Please check database conectivity', 'AUTH');
        show_error($this->lang->line('auth_cas_unknown_error_check_database'), 403, $this->lang->line('auth_no_access'));

        return false;
    }

    /**
     * @inheritdoc
     */
    public function onAuthRecheck()
    {
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $_GET);
        $this->_init();
        phpCAS::forceAuthentication();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isPasswordChangeSupported()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function logout($explicit = false)
    {
        if ($explicit) {
            // This sucks but otherwise the session is never terminated
            $this->auth->unsetSession();
            @session_destroy();

            $this->_init();
            return @phpCAS::logoutWithUrl(base_url());
        }
        return true;
    }

    /**
     * @param string $cas_user
     */
    protected function validateCasUser(string $cas_user)
    {
        // nothin here, but can be used in derived classes
    }
}
