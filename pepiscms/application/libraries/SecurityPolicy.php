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
 * A component responsible to read and write SecurityPolicies
 *
 * @since 0.1.4
 */
class SecurityPolicy
{
    /**
     * Do not mess with the following values
     * Once modified, the security policy manager
     * will malfunction in a random manner (you do not want this)
     */
    const NONE = 0;
    const READ = 1;
    const WRITE = 2;
    const READ_WRITE = 3;
    const FULL_CONTROL = 4;

    private $CI;


    /**
     * Default constructor
     *
     * @param array $variables
     */
    public function __construct($variables = array())
    {
        $this->CI = get_instance();
    }


    /**
     * Returns system policy path
     *
     * @return string
     */
    public static function getSystemPolicyPath()
    {
        return APPPATH . '/security_policy.xml';
    }


    /**
     * Returns path of security policy for specified module
     *
     * @param string $module_name
     * @return string
     */
    public static function getModulePolicyPath($module_name)
    {
        return get_instance()->load->resolveModuleDirectory($module_name) . '/security_policy.xml';
    }

    /**
     * Tells whether module's security policy exists
     *
     * @param string $module_name
     * @return bool
     */
    public static function existsModulePolicy($module_name)
    {
        return file_exists(self::getModulePolicyPath($module_name));
    }

    /**
     * Returns a flat list of available entities out of the policy array
     *
     * @param array $policy
     * @return array
     */
    protected function getAvailableEntities($policy)
    {
        $available_entities = array();

        foreach ($policy as $controller => $methods) {
            foreach ($methods as $method => $access) {
                $available_entities[] = $access['entity'];
            }
        }

        return array_unique($available_entities);
    }

    /**
     * Parses policy of the specified path
     *
     * @param string $path
     * @return array
     */
    protected function parsePolicy($path)
    {
        if (!file_exists($path)) {
            return FALSE;
        }

        $security_policy = array();
        try {
            $sxe = @new SimpleXMLElement($path, NULL, TRUE);
        } catch (Exception $exception) {
            return FALSE;
        }

        $controllers = $sxe->policy->children();
        foreach ($controllers as $controller) {
            $attributes = $controller->attributes();
            $controller_name = '' . $attributes->name;

            $methods = $controller->children();

            foreach ($methods as $method) {
                $attributes = $method->attributes();
                $method_name = '' . $attributes->name;

                $entities = $method->children();
                foreach ($entities as $entity) {
                    $attributes = $entity->attributes();
                    $access = '' . $attributes->access;
                    switch ($access) {
                        case 'FULL_CONTROL':
                            $access = SecurityPolicy::FULL_CONTROL;
                            break;
                        case 'READ':
                            $access = SecurityPolicy::READ;
                            break;
                        case 'WRITE':
                            $access = SecurityPolicy::WRITE;
                            break;
                        default:
                            $access = SecurityPolicy::NONE;
                    }

                    $security_policy[$controller_name][$method_name] = array('entity' => '' . $attributes->name, 'access' => $access);
                }
            }
        }

        return $security_policy;
    }


    /**
     * Returns security policy for system core
     *
     * @return array
     */
    public function getSystemSecurityPolicy()
    {
        $this->CI->benchmark->mark('reading_system_security_policy_start');
        $policy = $this->parsePolicy(self::getSystemPolicyPath());
        $this->CI->benchmark->mark('reading_system_security_policy_end');
        return $policy;
    }


    /**
     * Returns security policy for the given module
     *
     * @param string $module
     * @return array
     */
    public function getModuleSecurityPolicy($module)
    {
        $this->CI->benchmark->mark('reading_module_security_policy_' . $module . '_start');
        $policy_file = $this->CI->load->resolveModuleDirectory($module) . '/security_policy.xml';
        if (file_exists($policy_file)) {
            $policy = $this->parsePolicy($policy_file);
            $this->CI->benchmark->mark('reading_module_security_policy_' . $module . '_end');
            return $policy;
        }
        $this->CI->benchmark->mark('reading_module_security_policy_' . $module . '_end');
        return array();
    }


    /**
     * Returns a list of entities for system core
     *
     * @return array
     */
    public function getSystemAvailableEntities()
    {
        $policy = $this->parsePolicy(self::getSystemPolicyPath());
        return $this->getAvailableEntities($policy);
    }


    /**
     * Returns a list of all entities, the entities are grouped
     *
     * @return array
     */
    public function getAllAvailableEntities()
    {
        $all_entities = array();

        $entities = array();
        $entities['system'] = $this->getSystemAvailableEntities();

        $this->CI->load->library('ModuleRunner');

        $modules = ModuleRunner::getAvailableModules();

        foreach ($modules as $module) {
            $module_entities = $this->getModuleAvailableEntities($module);
            if (count($module_entities)) {
                //$entities[$module] = array();
                foreach ($module_entities as $entity) {
                    if (in_array($entity, $all_entities)) {
                        continue;
                    }

                    $entities[$module][] = $entity;
                }
                $all_entities = array_merge($all_entities, $module_entities);
            }
        }

        return $entities;
    }


    /**
     * Returns a list of entities for a given module
     *
     * @param string $module_name
     * @return array
     */
    public function getModuleAvailableEntities($module_name)
    {
        $policy_file = $this->CI->load->resolveModuleDirectory($module_name) . '/security_policy.xml';
        if (file_exists($policy_file)) {
            $policy = $this->parsePolicy($policy_file);
            return $this->getAvailableEntities($policy);
        }

        return array();
    }


    /**
     * Returns a list of methods for a given module
     *
     * @param string $module_name
     * @return array
     */
    public function describeModuleControllers($module_name)
    {
        $module_file = $this->CI->load->resolveModuleDirectory($module_name) . '/' . $module_name . '_admin_controller.php';
        if (!is_file($module_file)) {
            return array();
        }
        include($module_file);

        $class = ucfirst($module_name) . 'Admin';

        if (!class_exists($class)) {
            return array();
        }

        $classDescription = $this->describeClass($class, FALSE);
        $classDescription->name = str_replace('admin', '', $classDescription->name); // TODO only the suffix

        $controllers = array($classDescription);

        return $controllers;
    }


    /**
     * Returns an array of controllers containing the list of all public methods
     *
     * @return array
     */
    public function describeSystemControllers()
    {
        $controllers = array();

        $path = APPPATH . 'controllers/admin/';

        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if (!is_file($path . $file)) {
                continue;
            }

            $class = ucfirst(substr($file, 0, strlen($file) - 4));
            if (!class_exists($class)) {
                include($path . $file);
            }

            $controllers[] = $this->describeClass($class);
        }
        closedir($dir);
        return $controllers;
    }


    /**
     * Returns a list of methods for a given class
     *
     * @param string $class
     * @param bool $must_be_declaring
     * @return object
     */
    protected function describeClass($class, $must_be_declaring = TRUE)
    {
        $ignored_methods = array(
            'ModuleAdminController',
            'getPrivileges',
            'getConfigVariables',
            'ModuleAdminController',
            'display',
            'displayPDF',
            'AdminController',
            'setControllerName',
            'setMethodName',
            'assign',
            'getAttribute',
            'getAttributes',
            'setAttributes',
            'getParam',
            'getValue',
            'get_instance',
            'setRelatedModule'
        );

        $rc = new ReflectionClass($class);

        $methods = array();

        $cmethods = $rc->getMethods();
        foreach ($cmethods as $method) {
            if ($method->isPublic() && !$method->isConstructor()) {
                if ($must_be_declaring && $method->getDeclaringClass()->getName() != $class) {
                    continue;
                }

                $name = $method->getName();
                if ($name{0} == '_' || in_array($name, $ignored_methods)) {
                    continue;
                }

                $methods[] = $method;
            }
        }

        $item = (Object)null;
        $item->name = strtolower($class);
        $item->methods = $methods;

        return $item;
    }
}
