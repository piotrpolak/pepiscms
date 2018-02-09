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
 * Generic remote exception
 *
 * @since 0.1.5
 */
class RemoteException extends Exception
{
}

/**
 * Authorization exception
 *
 * @since 0.1.5
 */
class RemoteAuthorizationException extends RemoteException
{
}

/**
 * Web service consumer wrapper class
 *
 * @since 0.1.5
 */
class WebserviceConsumer
{
    private $api_key = '';
    private $api_secret = '';
    private $public_ip = FALSE;

    /**
     * WebserviceConsumer constructor.
     *
     * @param null|array $parameters
     */
    public function __construct($parameters = NULL)
    {
        $this->load->library('xmlrpc');
    }

    /**
     * Sets public IP for signing the call. Use this method if your local IP differs from your public IP
     *
     * @param string $public_ip
     */
    public function setPublicIpAddress($public_ip)
    {
        $this->public_ip = $public_ip;
    }

    /**
     * Returns public IP needed for signing request
     *
     * @return string
     */
    public function getPublicIpAddress()
    {
        if (!$this->public_ip) {
            // Attempt to detect public IP.
            // This might not be true for some configurations
            $this->public_ip = $_SERVER['SERVER_ADDR'];
        }

        return trim($this->public_ip);
    }

    /**
     * Binds the consumer to the specified URL and port
     *
     * @param string $url
     * @param int $port
     */
    public function bind($url, $port = 80)
    {
        //$this->xmlrpc->set_debug(TRUE);
        $this->xmlrpc->server($url, $port);
    }

    /**
     * Sets debug flag
     *
     * @param bool $debug
     */
    public function debug($debug = TRUE)
    {
        $this->xmlrpc->set_debug($debug);
    }

    /**
     * Sets API KEY and API SECRET
     *
     * @param string $api_key
     * @param string $api_secret
     */
    public function setCredentials($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    /**
     * Generates and returns tokens used for signing request
     *
     * @return array
     */
    protected function getRequestSignatureTokens()
    {
        $time = time();
        $public_ip = $this->getPublicIpAddress();

        $signature = md5($this->api_key . $this->api_secret . $time . $public_ip);

        return array($this->api_key, $time, $signature);
    }

    /**
     * CodeIgniter attributes proxy
     *
     * @param string $var
     * @return mixed
     */
    function __get($var)
    {
        $ci = CI_Controller::get_instance();
        return $ci->$var;
    }

    /**
     * Proxy call.
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws RemoteAuthorizationException
     * @throws RemoteException
     */
    public function __call($name, $arguments)
    {
        $arguments = array_merge($this->getRequestSignatureTokens(), $arguments);

        $this->xmlrpc->method($name);
        $this->xmlrpc->request($arguments);

        if (!$this->xmlrpc->send_request()) {
            switch ($this->xmlrpc->getResult()->faultCode()) {
                case 401:
                    throw new RemoteAuthorizationException($this->xmlrpc->display_error());
                    break;
                default:
                    throw new RemoteException($this->xmlrpc->display_error());
            }
        } else {
            return $this->xmlrpc->display_response();
        }
    }

}
