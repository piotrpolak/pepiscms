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
 * CAS authentication driver
 *
 * @since 0.2.2
 */
class CasAuthDriver extends ContainerAware implements AuthDriverableInterface
{
    private $is_initialized = FALSE;
    private $conf = array();
    private $auth = NULL;

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

    private function _init()
    {
        if (!$this->is_initialized) {
            $this->is_initialized = TRUE;

            if (!isset($this->conf['cas_host']) || !$this->conf['cas_host']) {
                show_error(sprintf($this->lang->line('auth_cas_misconfig'), INSTALLATIONPATH));
            }

            phpCAS::client(CAS_VERSION_2_0, $this->conf['cas_host'], (int)$this->conf['cas_port'], $this->conf['cas_url']);
            phpCAS::setNoCasServerValidation();
        }
    }

    /**
     * Authorizes user, tells whenever user-password correct
     *
     * @param string $user_email_or_login
     * @param string $password
     * @return boolean
     */
    public function authorize($user_email_or_login, $password)
    {
        return FALSE;
    }

    /**
     * Method called on auth request, usually when redirecting to the login page
     *
     * @return bool
     * @throws Exception
     */
    public function onAuthRequest()
    {
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $_GET);
        $this->_init();
        phpCAS::forceAuthentication();

        // Trying to get an existing user
        $user_id = $this->User_model->getUserIdByEmail(phpCAS::getUser());

        // Trying to register an user
        if ($user_id === FALSE) {
            // Checking whether allowed usernames are set and whether an user is one of them
            if (isset($this->conf['allowed_usernames']) && count($this->conf['allowed_usernames']) > 0) {
                if (!in_array(phpCAS::getUser(), $this->conf['allowed_usernames'])) {
                    Logger::warning('Username ' . phpCAS::getUser() . ' is not allowed by AUTH driver option allowed_usernames.', 'AUTH');
                    return FALSE;
                }
            }

            // Checking whether allowed domains are set and whether an user belongs to a trusted domain
            if (isset($this->conf['allowed_domains']) && count($this->conf['allowed_domains']) > 0) {
                $domain = substr(phpCAS::getUser(), strpos(phpCAS::getUser(), '@') + 1);
                if (!in_array($domain, $this->conf['allowed_domains'])) {
                    Logger::warning('User domain ' . $domain . ' is not allowed.', 'AUTH');
                    return FALSE;
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

            $display_name = explode('@', phpCAS::getUser());
            $display_name = ucwords(str_replace(array('.', '-', '_'), ' ', $display_name[0]));

            $is_root = !$this->User_model->countAll();
            $this->User_model->register($display_name, phpCAS::getUser(), FALSE, FALSE, $user_group_ids, $is_root, FALSE, array('status' => $status));
            $user_id = $this->User_model->getUserIdByEmail(phpCAS::getUser());
        }

        if ($user_id) {
            if (!$this->auth->forceLogin($user_id)) {
                Logger::warning('Attempt to login using inactive account (deactivated locally). User ID ' . $user_id, 'AUTH');
                show_error($this->lang->line('auth_cas_attempt_to_login_using_inactive_account'), 403, $this->lang->line('auth_no_access'));
            }
            return TRUE;
        }

        Logger::warning('Unable to login using CAS driver. Unknown reason. Please check database conectivity', 'AUTH');
        show_error($this->lang->line('auth_cas_unknown_error_check_database'), 403, $this->lang->line('auth_no_access'));

        return FALSE;
    }

    /**
     * Method called on auth request, usually when the user session is about to expire
     *
     * @return bool
     */
    public function onAuthRecheck()
    {
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $_GET);
        $this->_init();
        phpCAS::forceAuthentication();

        return TRUE;
    }

    /**
     * Tells whether the password can be changed by CMS
     *
     * @return boolean
     */
    public function isPasswordChangeSupported()
    {
        return FALSE;
    }

    /**
     * Terminates session
     *
     * @param boolean $explicit
     * @return boolean
     */
    public function logout($explicit = FALSE)
    {
        if ($explicit) {
            // This sucks but otherwise the session is never terminated
            $this->auth->unsetSession();
            @session_destroy();

            $this->_init();
            return @phpCAS::logoutWithUrl(base_url());
        }
        return TRUE;
    }

}
