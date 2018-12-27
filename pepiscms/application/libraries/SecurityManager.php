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
 * A component that is usually injected before the admin controller
 * method call in order to check user access
 *
 * @since 0.1.4
 */
class SecurityManager
{
    /**
     * @var array
     */
    private static $cache;

    /**
     * @var string
     */
    private static $cached_objects_collection_name = 'security_policy';

    /**
     * Returns true when a user has access to the method of the controller
     * When the $module parameter the controller method
     * should not be the name of the class but name of the module
     *
     * NOTE Auth library must be already loaded
     *
     * @param string $controller - name of the controller of the core or name of the module
     * @param string|bool $method
     * @param bool|bool $module
     * @return bool
     */
    public static function hasAccess($controller, $method = false, $module = false)
    {
        if (!$method) {
            $method = 'index';
        }

        if (!$module) {
            $module = '';
        }
        $CI = get_instance();

        $CI->benchmark->mark('accesscheck_' . $module . '_start');

        if (!$CI->auth) {
            return false;
        }

        // Root has access to everything
        if ($CI->auth->isUserRoot()) {
            $CI->benchmark->mark('accesscheck_' . $module . '_end');
            return true;
        }

        // Avoiding reparsing policy for the same actions within the same real request
        $cache_label = $module ? $module : 'system';
        if (isset(self::$cache[$cache_label][$controller][$method])) {
            return self::$cache[$cache_label][$controller][$method];
        }

        $access = self::getRequiredAccessRight($controller, $method, $module);
        $entity = $access['entity'];
        $min_access = $access['min_access'];

        $user_access = $CI->auth->getUserAccess();

        if ((isset($user_access[$entity]) && $user_access[$entity] >= $min_access) || $min_access == 0) {
            // User has the minimum access right
            self::$cache[$cache_label][$controller][$method] = true;
            $CI->benchmark->mark('accesscheck_' . $module . '_end');
            return true;
        }

        // NO access rights
        self::$cache[$cache_label][$controller][$method] = false;
        $CI->benchmark->mark('accesscheck_' . $module . '_end');
        return false;
    }

    /**
     * Returns the required access right
     *
     * @param string $controller - name of the controller of the core or name of the module
     * @param string $method
     * @param bool $module
     * @return array
     */
    public static function getRequiredAccessRight($controller, $method = 'index', $module = false)
    {
        if (!$module) {
            $module = '';
        }

        if (!$module) {
            $security_policy = self::getSystemSecurityPolicyCached();
        } else {
            $security_policy = self::getModuleSecurityPolicyCached($controller);
        }

        $entity = isset($security_policy[$controller][$method]['entity']) ? $security_policy[$controller][$method]['entity'] : false;
        $min_access = isset($security_policy[$controller][$method]['access']) ? $security_policy[$controller][$method]['access'] : false;

        return array('entity' => $entity, 'min_access' => $min_access);
    }

    /**
     * Returns cached system security policy
     *
     * @return array
     */
    private static function getSystemSecurityPolicyCached()
    {
        get_instance()->load->library('Cachedobjectmanager');

        $policy_name = '_system_security_policy';
        return get_instance()->cachedobjectmanager->get($policy_name, self::$cached_objects_collection_name, 3600 * 24,
            function () {
                get_instance()->load->library('SecurityPolicy');
                return get_instance()->securitypolicy->getSystemSecurityPolicy();
            }
        );
    }

    /**
     * Returns cached security policy for a given module
     *
     * @param $module
     * @return array
     */
    private static function getModuleSecurityPolicyCached($module)
    {
        get_instance()->load->library('Cachedobjectmanager');

        $policy_name = 'module_security_policy_' . $module;
        return get_instance()->cachedobjectmanager->get($policy_name, self::$cached_objects_collection_name, 3600 * 24,
            function () use ($module) {
                get_instance()->load->library('SecurityPolicy');
                return get_instance()->securitypolicy->getModuleSecurityPolicy($module);
            }
        );
    }

    /**
     * Flushes security manager cache and forcer security policies to be read again
     *
     * @return array
     */
    public static function flushCache()
    {
        $CI = get_instance();
        $CI->load->library('Cachedobjectmanager');

        return $CI->cachedobjectmanager->cleanup(self::$cached_objects_collection_name);
    }
}
