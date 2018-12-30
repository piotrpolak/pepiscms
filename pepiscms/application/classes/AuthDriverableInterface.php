<?php defined('BASEPATH') or exit('No direct script access allowed');

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
 * Auth driver interface
 *
 * @since 0.2.3
 */
interface AuthDriverableInterface
{
    /**
     * Constructs auth driver
     *
     * @param auth $auth
     */
    public function __construct(Auth $auth);

    /**
     * Tells whether the driver is enabled and can be used.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Authorizes user, tell whenether user-password correct
     *
     * @param string $user_email_or_login
     * @param string $password
     * @return bool
     */
    public function authenticate($user_email_or_login, $password);

    /**
     * Terminates session
     *
     * @param bool $explicit
     * @return bool
     */
    public function logout($explicit = false);

    /**
     * Method called on auth request, usually when redirecting to the login page
     *
     * @return bool
     */
    public function onAuthRequest();

    /**
     * Method called on auth request, usually when the user session is about to expire
     *
     * @return bool
     */
    public function onAuthRecheck();

    /**
     * Tells whether the password can be changed by CMS
     *
     * @return bool
     */
    public function isPasswordChangeSupported();
}
