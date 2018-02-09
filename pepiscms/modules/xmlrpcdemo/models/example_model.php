<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Example model created for development purposes
 */
class Example_model extends Remote_model
{
    /**
     * This example function says hello. Both of the parameters must be specified.
     * This function is for demonstration purposes only.
     *
     * @param string $first_name
     * @param string $last_name
     * @return string
     */
    public function hello($first_name, $last_name)
    {
        return 'Hello ' . $first_name . ' ' . $last_name . '!';
    }

    /**
     * Returns caler ip
     *
     * @return string
     */
    public function getOwnIp()
    {
        return $this->input->ip_address();
    }

    /**
     * Returns current timestamp number of seconds
     *
     * @return int
     */
    public function time()
    {
        return time();
    }

    /**
     * Returns database version
     * @return string
     */
    public function getDbVersion()
    {
        return $this->db->version();
    }

    /**
     * This function does nothing.
     *
     * @return void
     */
    public function handshake()
    {
        return;
    }
}
