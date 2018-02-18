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
 * Library for managing cached objects
 *
 * @since 0.1.0
 */
class Cachedobjectmanager
{
    protected $cache_path;
    protected $objects = array();

    /**
     * Default constructor
     *
     * @param array $parameters
     */
    public function __construct($parameters = array())
    {
        $CI = &CI_Controller::getInstance();

        // Getting cache path from configuration
        $path = $CI->config->item('cache_path');
        $path = ($path === '') ? 'application/cache/' : $path;

        // Prepending INSTALLATIONPATH for relative values
        if ($path{0} !== '/') {
            $path = INSTALLATIONPATH . $path;
        }
        $this->cache_path = $path;

        // Attempt to create missing directory
        if (!file_exists($this->cache_path)) {
            @mkdir($this->cache_path);
        }

        $this->cache_path .= 'cached_objects/';
        // Attempt to create missing subdirectory
        if (!file_exists($this->cache_path)) {
            @mkdir($this->cache_path);
        }
    }

    /**
     * Storing all the objects at destruction
     */
    function __destruct()
    {
        // Delayed save of every object
        foreach ($this->objects as $o) {
            $this->storeObject($o['name'], $o['object'], $o['collection']);
        }
    }

    /**
     * Retrieves object from persistent memory
     *
     * @param string $name
     * @param string $time_to_live
     * @param string $collection
     *
     * @return bool
     */
    public function getObject($name, $time_to_live, $collection = '')
    {
        $hash = $this->computeHash($name);
        $path = $this->computePath($collection, $hash);

        CI_Controller::getInstance()->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_start');

        if (!file_exists($path)) {
            CI_Controller::getInstance()->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_end');
            return FALSE;
        }

        // Comparing timestamps
        if (filemtime($path) < time() - $time_to_live) {
            CI_Controller::getInstance()->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_end');
            return FALSE;
        }

        $object = FALSE; // Supposed to be overwritten
        /** @noinspection PhpIncludeInspection */
        @include($path);
        CI_Controller::getInstance()->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_end');
        return $object;
    }

    /**
     * Sets object by name. If store on destruct is set to true, the object will be storied with a delay
     *
     * @param string $name
     * @param object $object
     * @param string $collection
     * @param bool $store_on_destruct
     *
     * @return bool
     */
    public function setObject($name, $object, $collection = '', $store_on_destruct = FALSE)
    {
        if (!$store_on_destruct) {
            return $this->storeObject($name, $object, $collection);
        }

        $this->objects[] = array(
            'name' => $name,
            'object' => $object,
            'collection' => $collection
        );

        return TRUE;
    }

    /**
     * Stories cached object on the hard disk
     *
     * @param string $name
     * @param object $object_to_write
     * @param string $collection
     * @return bool
     */
    protected function storeObject($name, $object_to_write, $collection = '')
    {
        $hash = $this->computeHash($name);
        $path = $this->computePath($collection, $hash);

        CI_Controller::getInstance()->benchmark->mark('cached_object_manager_store_object_' . $collection . '_' . $hash . '_start');

        $error = false;

        // Serializing and saving - method #1
        $contents = '<?php // ' . $name . ' - Written at ' . date('Y-m-d, H:i:s') . "\n" . ' $object = unserialize(\'' . str_replace('\'', '\\\'', serialize($object_to_write)) . '\');';
        if (!file_put_contents($path, $contents, LOCK_EX)) {
            $error = true;
        }

        // If there was an error - skip
        if (!$error) {
            // Testing if serialization worked
            @include($path);
            /** @noinspection PhpUndefinedVariableInspection */
            if (!$object) {
                CI_Controller::getInstance()->benchmark->mark('cached_object_manager_failsafe_store_object_' . $collection . '_' . $hash . '_start');

                // Serializing and saving - method #2
                $contents = '<?php // ' . $name . ' - Written at ' . date('Y-m-d, H:i:s') . "\n" . ' $object = @unserialize(base64_decode(\'' . base64_encode(serialize($object_to_write)) . '\'));';
                if (!file_put_contents($path, $contents, LOCK_EX)) {
                    $error = true;
                }
                CI_Controller::getInstance()->benchmark->mark('cached_object_manager_failsafe_store_object_' . $collection . '_' . $hash . '_end');
            }
        }

        CI_Controller::getInstance()->benchmark->mark('cached_object_manager_store_object_' . $collection . '_' . $hash . '_end');

        if ($error) {
            Logger::error('Unable to write system cache ' . $path, 'SYSTEM');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Cleans up all objects belonging to the given collection
     * If no collection specified, all objects are deleted
     *
     * @param string $collection
     * @return array
     */
    public function cleanup($collection = '')
    {
        $return = array(
            'size' => 0,
            'count' => 0
        );

        $cache_files = glob($this->cache_path . $collection . '*.php');

        if (!is_array($cache_files) || count($cache_files) == 0) {
            return $return;
        }

        foreach ($cache_files as $file_path) {
            if (!file_exists($file_path) || !is_file($file_path)) {
                continue;
            }

            $file_size = filesize($file_path);
            if (@unlink($file_path)) {
                $return['size'] += $file_size;
                $return['count']++;
            }
        }

        return $return;
    }

    /**
     * @param $collection
     * @param $hash
     * @return string
     */
    private function computePath($collection, $hash)
    {
        return $this->cache_path . $collection . '_' . $hash . '.php';
    }

    /**
     * @param $name
     * @return string
     */
    private function computeHash($name)
    {
        $hash = md5($name);
        return $hash;
    }

}
