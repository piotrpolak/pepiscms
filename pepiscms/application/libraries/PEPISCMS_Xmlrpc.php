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
 * Extended Xmlrpc
 */
class PEPISCMS_Xmlrpc extends CI_Xmlrpc
{

    /**
     *
     * @return XML_RPC_Response
     */
    public function getResult()
    {
        return $this->result;
    }

}
