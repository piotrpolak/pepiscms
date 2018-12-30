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
     * @inheritdoc
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user_email_or_login, $password)
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
     * @inheritdoc
     */
    public function onAuthRequest()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthRecheck()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isPasswordChangeSupported()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function logout($explicit = false)
    {
        return true;
    }
}
