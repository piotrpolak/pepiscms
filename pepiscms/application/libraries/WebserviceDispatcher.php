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
 * Webservice dispatcher class
 *
 * The class is responsible for dispatching web service calls, security and
 * generating documentation.
 *
 * @since 0.1.5
 */
class WebserviceDispatcher
{
    private $webservice_instance = NULL;

    /**
     * WebserviceDispatcher constructor.
     *
     * @param null|array $parameters
     */
    public function __construct($parameters = NULL)
    {
        CI_Controller::get_instance()->load->model('Remote_application_model');
        CI_Controller::get_instance()->load->model('Remote_model_helper_model');

        CI_Controller::get_instance()->load->config('webservice');

        CI_Controller::get_instance()->load->library('Xmlrpc');
        CI_Controller::get_instance()->load->library('Xmlrpcs');
    }

    /**
     * Dispatches webservice
     *
     * @global mixed $HTTP_RAW_POST_DATA
     * @param object $webservice_instance
     * @return void
     */
    public function dispatch($webservice_instance)
    {
        $this->webservice_instance = $webservice_instance;

        global $HTTP_RAW_POST_DATA;
        if (strlen($HTTP_RAW_POST_DATA) == 0) {
            $this->dispatchDocumentation();
            return;
        }


        $methods = CI_Controller::get_instance()->Remote_model_helper_model->listRemoteMethodsCached(get_class($this->webservice_instance));

        $config = array();
        $config['object'] = $this;
        foreach ($methods as $method) {
            $method_name = $method->getName();
            $config['functions'][$method_name] = array();
            $config['functions'][$method_name]['function'] = __CLASS__ . '.' . $method_name;
        }

        CI_Controller::get_instance()->xmlrpcs->initialize($config);
        CI_Controller::get_instance()->xmlrpcs->serve();
    }

    protected function dispatchDocumentation()
    {
        $methods = CI_Controller::get_instance()->Remote_model_helper_model->listRemoteMethods(get_class($this->webservice_instance));
        foreach ($methods as &$method) {
            $isLocalMethod = FALSE;
            $name = $method->getName();
            if ($name{0} == '_') // Meaning protected method
            {
                continue;
            }

            $return_type = 'unknown';

            $parameters = $doc = $tags = $param_types = $lines = $matches = array();
            foreach ($method->getParameters() as $parameter) {
                $parameters[] = $parameter->getName();
            }

            preg_match_all('/\* ([^\*|\/]+[a-z].)\n/', $method->getDocComment(), $matches);
            foreach ($matches[1] as $line) {
                $line = trim($line);
                if ($line) {
                    $lines[] = $line;
                }
            }

            foreach ($lines as $line) {
                if ($line{0} == '@') {
                    $line = substr($line, 1);
                    $tags[] = $line;
                    if (strtolower(substr($line, 1, 6)) == 'return') {
                        list($dontcare, $return) = explode(' ', $line, 2);
                    } elseif (strtolower(substr($line, 1, 5)) == 'param') {
                        list($dontcare, $param_type, $dontcare) = explode(' ', $line, 3);
                        $param_types[] = $param_type;
                    } elseif (strtolower(substr($line, 1, 5)) == 'local') {
                        $method = FALSE;
                        $isLocalMethod = TRUE;
                        continue;
                    }
                } else {
                    $doc[] = $line;
                }
            }

            if ($isLocalMethod)
                continue;

            if (count($param_types) > 0) {
                for ($i = 0; $i < count($parameters); $i++) {
                    if (!isset($param_types[$i])) {
                        $param_types[$i] = 'unknown';
                    }

                    $parameters[$i] = $param_types[$i] . ' ' . $parameters[$i];
                }
            }

            $config['functions'][$name] = array();
            $config['functions'][$name]['function'] = __CLASS__ . '.' . $name;
            $config['functions'][$name]['docstring'] = implode("\n", $doc);
            $config['functions'][$name]['signature_label'] = 'public ' . $return_type . ' ' . $name . '(' . implode(', ', $parameters) . ')';
        }

        return CI_Controller::get_instance()->load->view('templates/webservices_documentation_default', $config);
    }

    /**
     * Validates signature
     *
     * @param string $api_key
     * @param int $time
     * @param string $signature
     * @param string $public_ip
     * @return bool
     */
    protected function validateSignature($signature, $api_key, $time, $public_ip)
    {
        if (!$api_key || !$signature || !$time || !$public_ip) {
            return FALSE;
        }

        // Getting API secret by API key
        $api_secret = CI_Controller::get_instance()->Remote_application_model->getSecretByKeyCached($api_key);
        if (!$api_secret) {
            return FALSE;
        }

        return $signature == md5($api_key . $api_secret . $time . $public_ip);
    }

    /**
     * Virtual call that check if the callee is authorized and calls the webservice method
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $CI = get_instance();
        $parameters = $arguments[0]->output_parameters();

        // Checking if the 3 first parameters are set
        if (count($parameters) < 3) {
            return $CI->xmlrpc->send_error_message('400', 'Bad call - first 3 parameters are not set: API_KEY, TIME, SIGNATURE');
        }

        // Checking signature
        $public_ip = $CI->input->ip_address();
        if (!$this->validateSignature(trim($parameters[2]), trim($parameters[0]), trim($parameters[1]), $public_ip)) {
            return $CI->xmlrpc->send_error_message('401', 'Unauthorized API keys or wrong signature');
        }


        if (!$CI->Remote_model_helper_model->isMethodRemoteCached(get_class($this->webservice_instance), $name)) {
            return $CI->xmlrpc->send_error_message('404', $name . ' is not a known method for this XML-RPC Server');
        }

        $parameters = array_slice($parameters, 3, count($parameters));

        $reponse = call_user_func_array(array($this->webservice_instance, $name), $parameters);
        if (!is_array($reponse)) {
            $reponse = array($reponse);
        } else {
            $reponse = array($reponse, 'struct');
        }
        return $CI->xmlrpc->send_response($reponse);
    }

}