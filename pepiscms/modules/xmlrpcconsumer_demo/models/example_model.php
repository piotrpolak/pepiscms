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
 * Class Example_model
 */
class Example_model extends WebserviceConsumer
{
    /**
     * Base URL for all the binds
     * @var String
     */
    private $base_url = '';

    /**
     * Default constructor
     */
    public function __construct()
    {
        $key = '9091a2594d277c1ef56e9b5ebc494ae8';
        $secret = '815738bc36921fdaf9b2e0ea3375b1a7';
        $public_ip = trim($this->getOwnPublicIp());

        $this->base_url = 'http://example.com/beta/';

        if (!$public_ip) {
            die('ERROR: Unable to detect own public IP address. No network connectivity.');
        }

        parent::__construct();
        $this->bind($this->base_url . 'api/example/example', 80);
        $this->setPublicIpAddress($public_ip);
        $this->setCredentials($key, $secret);
        //$this->debug();
    }

    /**
     * Binds the model to the buy price model
     */
    public function bindToPrices()
    {
        $this->bind($this->base_url . 'api/genericdirectory/Phone_buy_price', 80);
    }

    /**
     * Detects and returns public IP. An external server is used.
     *
     * @return string
     */
    public function getOwnPublicIp()
    {
        $ip = @file_get_contents('http://example.com/beta/getmyip.php');
        return $ip;
    }

}
