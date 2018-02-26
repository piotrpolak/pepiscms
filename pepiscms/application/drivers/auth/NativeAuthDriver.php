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
 * Native authentication driver
 *
 * @since 0.2.2
 */
class NativeAuthDriver extends ContainerAware implements AuthDriverableInterface
{
    /**
     * Constructs auth driver
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->load->model('User_model');
    }

    /**
     * Authorizes user, tell whenether user-password correct
     *
     * @param string $user_email_or_login
     * @param string $password
     * @return bool
     */
    public function authorize($user_email_or_login, $password)
    {
        if (strpos($user_email_or_login, '@') !== false) {
            $row = $this->User_model->validateByEmail($user_email_or_login, $password);
        } else {
            $row = $this->User_model->validateByLogin($user_email_or_login, $password);
        }

        if (!$row) {
            return false;
        }

        return $row;
    }

    /**
     * Method called on auth request, usually when redirecting to the login page
     *
     * @return bool
     */
    public function onAuthRequest()
    {
        return true;
    }

    /**
     * Method called on auth request, usually when the user session is about to expire
     *
     * @return bool
     */
    public function onAuthRecheck()
    {
        return true;
    }

    /**
     * Tells whether the password can be changed by CMS
     *
     * @return bool
     */
    public function isPasswordChangeSupported()
    {
        return true;
    }

    /**
     * Terminates session
     *
     * @param bool $explicit
     * @return bool
     */
    public function logout($explicit = false)
    {
        return true;
    }
}
