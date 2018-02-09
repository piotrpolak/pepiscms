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
 * Email extended class
 *
 * @since 0.1.3
 */
class PEPISCMS_Email extends CI_Email
{

    /**
     * Set the bulk header, use for sending newsletter
     */
    public function set_bulk()
    {
        $this->set_header('Precedence', 'bulk');
    }

    /**
     * Set return path
     * NOTE This must be called after from()
     *
     * @param string $email
     */
    public function set_return_path($email)
    {
        $email = $this->clean_email($email);
        if ($this->validate) {
            $this->validate_email($email);
        }
        $this->set_header('Return-Path', $email);
    }

    /**
     * Set list unsubscribe header, use for sending newsletter
     *
     * @param string $email
     */
    public function set_list_unsubscribe($email)
    {
        $email = $this->clean_email($email);
        if ($this->validate) {
            $this->validate_email($email);
        }

        $this->set_header('List-Unsubscribe', $email);
    }

    /**
     * Set sender email
     *
     * @param string $email
     */
    public function set_sender($email)
    {
        $email = $this->clean_email($email);
        if ($this->validate) {
            $this->validate_email($email);
        }

        $this->set_header('Sender', $email);
    }

}
