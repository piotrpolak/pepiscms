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
 * User authentication library
 *
 * @since 0.1.0
 */
class Auth extends ContainerAware
{
    const LOGGED_COOKIE_NAME = 'pepiscms_logged';
    /**
     * Auth update timeout in seconds
     *
     * @var string
     */
    private $session_variable_preffix = 'pepis_cms';

    /**
     * When true then the auth component will check if the IP of the session matches
     *
     * @var bool
     */
    private $session_must_match_ip = false;

    /**
     * Indicates if authenticated, cache variable
     *
     * @var bool
     */
    private $is_authenticated = null;

    /**
     * Auth update timeout in seconds
     *
     * @var int
     */
    const auth_update_timeout = 2700; //45min

    /**
     * Auth expire timeout in seconds
     * If user had no action in the given number of seconds, he must reauth
     *
     * @var int
     */
    const auth_max_idle_time = 3600; // 1h

    /**
     * Auth driver
     * @var AuthDriverableInterface
     */
    private $driver = null;

    /**
     * Auth constructor.
     */
    public function __construct()
    {
        @session_start();

        $this->load->model('User_model');
        $this->load->library('Logger'); // To prevent fatal errors
        $this->load->config('auth');


        $this->session_must_match_ip = $this->config->item('security_session_must_match_ip');

        $driver_type = $this->config->item('auth_driver');

        $this->driver = $this->instantiateDriver($driver_type);
    }

    /**
     * Tells whether given driver is enabled and can be used.
     *
     * @param $driver_type
     * @return bool
     */
    public function isAuthDriverEnabled($driver_type)
    {
        return $this->instantiateDriver($driver_type)->isEnabled();
    }

    /**
     * Returns instance of currenty used driver
     *
     * @return AuthDriverableInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Authenticates user, tells whenever user-password is correct
     *
     * @param string $user_email_or_login
     * @param string $password
     * @return bool
     */
    public function authenticate($user_email_or_login, $password)
    {
        $row = $this->driver->authenticate($user_email_or_login, $password);

        if ($row) {
            $time = time();
            $this->doAuthenticate($row);
            return true;
        }

        return false;
    }

    /**
     * Tells whether the user is authorised
     *
     * @param bool $force_do_all_checks_again
     * @return bool
     */
    public function isAuthenticated($force_do_all_checks_again = false)
    {
        if (!$force_do_all_checks_again) {
            if ($this->is_authenticated !== null) {
                // Kind of cache
                return $this->is_authenticated;
            }
        }

        $user_id = $this->getUserId();

        if ($user_id && $this->getSessionVariable('auth_instance_key') == md5(INSTALLATIONPATH)) {
            // Force renew session and cache to prevent cache format mismatch
            // Added in PepisCMS 0.2.4
            if ($this->getSessionVariable('pepiscms_version') !== PEPISCMS_VERSION) {
                $this->unsetSession();
                $this->load->library('Cachedobjectmanager');
                $this->cachedobjectmanager->cleanup();
                $this->db->cache_delete_all();
            }

            if (!$this->session_must_match_ip || $this->getSessionVariable('auth_ip') == $this->input->ip_address()) {
                $time = time();

                if ($this->getSessionVariable('auth_last_activity') + self::auth_max_idle_time >= $time) {
                    $success = true;
                    if ($this->getSessionVariable('auth_last_update') + self::auth_update_timeout < $time) {
                        // Check whenever CAS session is still active
                        $success = false;
                        if ($this->driver->onAuthRecheck()) {
                            $success = $this->renewUserData($user_id);
                        }
                    }
                    if ($success) {
                        $this->setSessionVariable('auth_last_activity', $time);
                        $this->is_authenticated = true;

                        return $this->is_authenticated;
                    }
                } else {
                    Logger::notice('Too long inactivity. Logging out.', 'LOGIN');
                }
            } else {
                Logger::error('The IP of the client does not match with the IP of the session. Original session IP is ' . $_SESSION[$this->session_variable_preffix]['auth_ip'] . '. Logging out.', 'LOGIN');
            }
        }

        $this->logout();
        $this->is_authenticated = false;
        return $this->is_authenticated;
    }

    /**
     * Returns user data. The user must be authenticated, otherwise returns false
     *
     * @return Object
     */
    public function getUserData()
    {
        return isset($_SESSION[$this->session_variable_preffix]['user_data']) ? $_SESSION[$this->session_variable_preffix]['user_data'] : false;
    }

    /**
     * Returns user attribute by name.
     * This method of accessing user info is used when the user info is extended
     *
     * @param string $attribute_name
     * @return mixed
     */
    public function getAttribute($attribute_name)
    {
        $data = $this->getUserData();
        if ($data) {
            if (isset($data->$attribute_name)) {
                return $data->$attribute_name;
            }
        }

        return false;
    }

    /**
     * Returns user email
     *
     * @return string
     */
    public function getUser()
    {
        return $this->getUserEmail();
    }

    /**
     * Returns user email
     *
     * @return string|bool
     */
    public function getUserEmail()
    {
        return isset($_SESSION[$this->session_variable_preffix]['user_email']) ? $_SESSION[$this->session_variable_preffix]['user_email'] : false;
    }

    /**
     * Returns user ID
     *
     * @return int|bool
     */
    public function getUserId()
    {
        return isset($_SESSION[$this->session_variable_preffix]['user_id']) ? $_SESSION[$this->session_variable_preffix]['user_id'] : false;
    }

    /**
     * Returns array representing user rights
     *
     * @return array|bool
     */
    public function getUserAccess()
    {
        return isset($_SESSION[$this->session_variable_preffix]['user_access']) ? $_SESSION[$this->session_variable_preffix]['user_access'] : false;
    }

    /**
     * Returns true for power users
     *
     * @return bool
     */
    public function isUserRoot()
    {
        return isset($_SESSION[$this->session_variable_preffix]['is_root']) ? $_SESSION[$this->session_variable_preffix]['is_root'] > 0 : false;
    }

    /**
     * Tells whether user password is expired
     *
     * @return bool
     */
    public function isUserPasswordExpired()
    {
        if ($this->getSessionVariable('is_user_password_expired') !== null) {
            return $this->getSessionVariable('is_user_password_expired');
        }

        $maximum_password_age_in_seconds = $this->config->item('security_maximum_password_age_in_seconds');
        if (!($maximum_password_age_in_seconds > 0)) {
            $this->setSessionVariable('is_user_password_expired', false); // Disabled
            return $this->getSessionVariable('is_user_password_expired');
        }
        $password_last_changed_timestamp = strtotime($this->getAttribute('password_last_changed_timestamp'));
        if (!$password_last_changed_timestamp) {
            $this->setSessionVariable('is_user_password_expired', true); // Expired because never changed
            return $this->getSessionVariable('is_user_password_expired');
        }

        $now_timestamp = (int)gmdate('U');

        // If the last time + difference is in future
        if ($password_last_changed_timestamp + $maximum_password_age_in_seconds < $now_timestamp) {
            $this->setSessionVariable('is_user_password_expired', true); // Expired
            return $this->getSessionVariable('is_user_password_expired');
        }

        $this->setSessionVariable('is_user_password_expired', false);  // Not expired
        return $this->getSessionVariable('is_user_password_expired');
    }

    /**
     * Sets session variable
     *
     * @param string $name
     * @param mixed $value
     * @return Auth|void
     */
    public function setSessionVariable($name, $value)
    {
        if ($value === null) {
            unset($_SESSION[$this->session_variable_preffix][$name]);
        } else {
            $_SESSION[$this->session_variable_preffix][$name] = $value;
        }

        return $this;
    }

    /**
     * Returns session variable
     *
     * @param string $name
     * @return mixed
     */
    public function getSessionVariable($name)
    {
        if (!isset($_SESSION[$this->session_variable_preffix][$name])) {
            return null;
        }
        return $_SESSION[$this->session_variable_preffix][$name];
    }

    /**
     * Method called on auth request
     */
    public function onAuthRequest()
    {
        $this->driver->onAuthRequest();
    }

    /**
     * Authenticates user without password
     *
     * @param $user_id
     * @return bool
     */
    public function forceLogin($user_id)
    {
        $success = $this->renewUserData($user_id);
        if ($success) {
            Logger::info('Login (forced)', 'LOGIN');
        }
        return $success;
    }

    /**
     * Renews user data WARNING: this logs user no matter the password was specified or no!
     *
     * @param int $user_id
     * @return bool
     */
    private function renewUserData($user_id)
    {
        $row = $this->User_model->getActiveById($user_id);

        if ($row) {
            $this->doAuthenticate($row);
            return true;
        }

        $this->logout();
        return false;
    }

    /**
     * Refreshes session
     *
     * @return bool
     */
    public function refreshSession()
    {
        if ($this->isAuthenticated()) {
            $user_id = $this->getUserId();
            $this->unsetSession()
                ->renewUserData($user_id);
            return true;
        }

        return false;
    }

    /**
     * Unsets session variables and removes session cookie
     */
    public function unsetSession()
    {
        setcookie(self::LOGGED_COOKIE_NAME, 0, 20, '/');
        unset($_SESSION[$this->session_variable_preffix]);
        return $this;
    }

    /**
     * Terminates session
     *
     * @param bool $explicit
     * @return bool
     */
    public function logout($explicit = false)
    {
        $this->driver->logout($explicit);
        $this->unsetSession();

        return true;
    }

    /**
     * Returns expiration timestamp
     *
     * @return int
     */
    public function getExpirationTimestamp()
    {
        return $_SESSION[$this->session_variable_preffix]['auth_last_activity'] + self::auth_max_idle_time;
    }

    /**
     * @param $driver_type
     * @return AuthDriverableInterface
     */
    private function instantiateDriver($driver_type)
    {
        $driver_class_name = ucfirst($driver_type) . 'AuthDriver';

        $driver_path = APPPATH . 'drivers/auth/' . $driver_class_name . '.php';
        if (!file_exists($driver_path)) {
            show_error('Auth driver specified does not exist on the filesystem or driver name empty ' . $driver_path);
        }

        return new $driver_class_name($this);
    }

    /**
     * @param $row
     */
    private function doAuthenticate($row)
    {
        $time = time();

        $this->setSessionVariable('user_access', $this->User_model->getUserAccess($row->user_id))
            ->setSessionVariable('user_id', $row->user_id)
            ->setSessionVariable('is_root', $row->is_root)
            ->setSessionVariable('user_email', $row->user_email)
            ->setSessionVariable('auth_last_update', $time)
            ->setSessionVariable('auth_last_activity', $time)
            ->setSessionVariable('auth_ip', $this->input->ip_address())
            ->setSessionVariable('auth_instance_key', md5(INSTALLATIONPATH))
            ->setSessionVariable('user_data', $row)
            ->setSessionVariable('pepiscms_version', PEPISCMS_VERSION)
            ->setSessionVariable('is_user_password_expired', null);

        setcookie(self::LOGGED_COOKIE_NAME, 1, 0, '/');
    }
}
