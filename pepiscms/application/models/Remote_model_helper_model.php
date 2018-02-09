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
 * Remote model helper
 *
 * @since 0.1.5
 */
class Remote_model_helper_model extends CI_Model
{

    /**
     * Lists remote methods
     *
     * @param string $class_name
     * @param bool $returnNames
     * @return array
     */
    public function listRemoteMethods($class_name, $returnNames = FALSE)
    {
        $remote_methods = array();

        $reflection = new ReflectionClass($class_name);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as &$method) {
            $isLocalMethod = FALSE;
            $name = $method->getName();
            if ($name{0} == '_') // Meaning protected method
            {
                continue;
            }

            $lines = $matches = array();

            preg_match_all('/\* ([^\*|\/]+[a-z].)\n/', $method->getDocComment(), $matches);
            foreach ($matches[1] as $line) {
                $line = trim($line);
                if ($line) {
                    $lines[] = $line;
                }
            }

            $is_local_method = FALSE;
            foreach ($lines as $line) {
                if ($line{0} == '@') {
                    if (strtolower(substr($line, 1, 5)) == 'local') {
                        $is_local_method = TRUE;
                        continue;
                    }
                }
            }

            if (!$is_local_method) {
                if ($returnNames) {
                    $remote_methods[] = $method->getName();
                } else {
                    $remote_methods[] = $method;
                }
            }
        }
        return $remote_methods;
    }

    /**
     * Lists remote methods
     *
     * @param string $class_name
     * @param bool $returnNames
     * @return bool
     */
    public function listRemoteMethodsCached($class_name, $returnNames = FALSE)
    {
        $this->load->library('Cachedobjectmanager');

        $object_name = 'webservice_list_remote_methods_' . $class_name . '_' . ($returnNames ? 'names' : 'reflection');
        $object = $this->cachedobjectmanager->getObject($object_name,
            CI_Controller::get_instance()->config->item('webservice_definition_cache_ttl'), 'webservice');

        if ($object === FALSE) {
            $object = $this->listRemoteMethods($class_name, $returnNames);
            $this->cachedobjectmanager->setObject($object_name, $object, 'webservice');
        }
        return $object;
    }

    /**
     * Tells whether a method can be remotely called
     *
     * @param string $class_name
     * @param string $method_name
     * @return bool
     */
    public function isMethodRemote($class_name, $method_name)
    {
        return in_array($method_name, $this->listRemoteMethods($class_name, TRUE));
    }

    /**
     * Tells whether a method can be remotely called, cached
     *
     * @param string $class_name
     * @param string $method_name
     * @return bool
     */
    public function isMethodRemoteCached($class_name, $method_name)
    {
        $this->load->library('Cachedobjectmanager');

        $object_name = 'webservice_method_is_remote_' . $class_name . '_' . $method_name;
        $object = $this->cachedobjectmanager->getObject($object_name,
            CI_Controller::get_instance()->config->item('webservice_definition_cache_ttl'), 'webservice');

        if ($object === FALSE) {
            $object = $this->isMethodRemote($class_name, $method_name);
            $this->cachedobjectmanager->setObject($object_name, $object, 'webservice');
        }
        return $object;
    }
}
