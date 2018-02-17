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
 * Native authentication driver
 *
 * @since 0.2.2
 */
class NativeAuthDriver implements AuthDriverableInterface
{
    /**
     * Constructs auth driver
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        // Do nothing ;)
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
        $CI = &get_instance();
        $CI->load->model('User_model');

        if (strpos($user_email_or_login, '@') !== FALSE) {
            $row = $CI->User_model->validateByEmail($user_email_or_login, $password);
        } else {
            $row = $CI->User_model->validateByLogin($user_email_or_login, $password);
        }

        if (!$row) {
            return FALSE;
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
        return TRUE;
    }

    /**
     * Method called on auth request, usually when the user session is about to expire
     *
     * @return bool
     */
    public function onAuthRecheck()
    {
        return TRUE;
    }

    /**
     * Tells whether the password can be changed by CMS
     *
     * @return bool
     */
    public function isPasswordChangeSupported()
    {
        return TRUE;
    }

    /**
     * Terminates session
     *
     * @param bool $explicit
     * @return bool
     */
    public function logout($explicit = FALSE)
    {
        return TRUE;
    }
}
