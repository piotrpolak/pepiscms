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
 * Extended Xmlrpcs
 */
class PEPISCMS_Xmlrpcs extends CI_Xmlrpcs
{

    protected $check_if_callable_on_execute = FALSE;

    /**
     * Executes the Method
     *
     * @access protected
     * @param object
     * @return mixed
     */
    function _execute($m)
    {
        if ($this->check_if_callable_on_execute) {
            return parent::_execute($m);
        }

        $methName = $m->method_name;

        // Check to see if it is a system call
        $system_call = (strncmp($methName, 'system', 5) == 0) ? TRUE : FALSE;

        if ($this->xss_clean == FALSE) {
            $m->xss_clean = FALSE;
        }

        //-------------------------------------
        //  Valid Method
        //-------------------------------------

        if (!isset($this->methods[$methName]['function'])) {
            return new XML_RPC_Response(0, $this->xmlrpcerr['unknown_method'], $this->xmlrpcstr['unknown_method']);
        }

        //-------------------------------------
        //  Check for Method (and Object)
        //-------------------------------------

        $method_parts = explode(".", $this->methods[$methName]['function']);
        $objectCall = (isset($method_parts['1']) && $method_parts['1'] != "") ? TRUE : FALSE;

        if ($system_call === TRUE) {
            if (!is_callable(array($this, $method_parts['1']))) {
                return new XML_RPC_Response(0, $this->xmlrpcerr['unknown_method'], $this->xmlrpcstr['unknown_method']);
            }
        }
        /* PEPISCMS Modification
          else
          {
          if ($objectCall && ! is_callable(array($method_parts['0'],$method_parts['1'])))
          {
          return new XML_RPC_Response(0, $this->xmlrpcerr['unknown_method'], $this->xmlrpcstr['unknown_method']);
          }
          elseif ( ! $objectCall && ! is_callable($this->methods[$methName]['function']))
          {
          return new XML_RPC_Response(0, $this->xmlrpcerr['unknown_method'], $this->xmlrpcstr['unknown_method']);
          }
          }
         */

        //-------------------------------------
        //  Checking Methods Signature
        //-------------------------------------

        if (isset($this->methods[$methName]['signature'])) {
            $sig = $this->methods[$methName]['signature'];
            for ($i = 0; $i < count($sig); $i++) {
                $current_sig = $sig[$i];

                if (count($current_sig) == count($m->params) + 1) {
                    for ($n = 0; $n < count($m->params); $n++) {
                        $p = $m->params[$n];
                        $pt = ($p->kindOf() == 'scalar') ? $p->scalarval() : $p->kindOf();

                        if ($pt != $current_sig[$n + 1]) {
                            $pno = $n + 1;
                            $wanted = $current_sig[$n + 1];

                            return new XML_RPC_Response(0, $this->xmlrpcerr['incorrect_params'], $this->xmlrpcstr['incorrect_params'] .
                                ": Wanted {$wanted}, got {$pt} at param {$pno})");
                        }
                    }
                }
            }
        }

        //-------------------------------------
        //  Calls the Function
        //-------------------------------------

        if ($objectCall === TRUE) {
            if ($method_parts[0] == "this" && $system_call == TRUE) {
                return call_user_func(array($this, $method_parts[1]), $m);
            } else {
                if ($this->object === FALSE) {
                    $CI = &get_instance();
                    return $CI->$method_parts['1']($m);
                } else {
                    return $this->object->$method_parts['1']($m);
                    //return call_user_func(array(&$method_parts['0'],$method_parts['1']), $m);
                }
            }
        } else {
            return call_user_func($this->methods[$methName]['function'], $m);
        }
    }

}
