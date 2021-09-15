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
 * Library for managing cached objects
 *
 * @since 0.1.0
 */
class Cachedobjectmanager extends ContainerAware
{
    private $cache_path;
    private $is_enabled;
    private $objects = array();

    /**
     * Default constructor
     */
    public function __construct()
    {
        // Getting cache path from configuration
        $path = $this->config->raw_item('cache_path');
        $this->is_enabled = $this->config->raw_item('cache_object_is_enabled');

        if ($this->is_enabled !== true) {
            return;
        }

        $path = ($path === '') ? 'application/cache/' : $path;

        // Prepending INSTALLATIONPATH for relative values
        if ($path[0] !== '/') {
            $path = INSTALLATIONPATH . $path;
        }
        $this->cache_path = $path;

        // Attempt to create missing directory
        if (!file_exists($this->cache_path)) {
            if (!@mkdir($this->cache_path)) {
                Logger::error('Unable to create cache directory at ' . $this->cache_path, 'SYSTEM');
            }
        }

        $this->cache_path .= 'cached_objects/';
        // Attempt to create missing subdirectory
        if (!file_exists($this->cache_path)) {
            if (!@mkdir($this->cache_path)) {
                Logger::error('Unable to create cache directory at ' . $this->cache_path, 'SYSTEM');
            }
        }
    }

    /**
     * Storing all the objects at destruction
     */
    public function __destruct()
    {
        if ($this->is_enabled !== true) {
            return;
        }

        // Delayed save of every object
        foreach ($this->objects as $o) {
            $this->storeObject($o['name'], $o['object'], $o['collection']);
        }
    }

    /**
     * The recommended convenience method to compute and get cache.
     *
     * @param $name
     * @param $collection
     * @param int $time_to_live
     * @param $callback
     * @return bool|mixed
     */
    public function get($name, $collection, $time_to_live, $callback)
    {
        /** @noinspection PhpDeprecationInspection */
        $object = $this->getObject($name, $time_to_live, $collection);
        if ($object) {
            return $object;
        }

        $object = call_user_func($callback);

        /** @noinspection PhpDeprecationInspection */
        $this->setObject($name, $object, $collection);

        return $object;
    }

    /**
     * TIP: Use get() function to get/set cache using a callback.
     *
     * Retrieves object from persistent memory
     *
     * @param string $name
     * @param string $time_to_live
     * @param string $collection
     *
     * @deprecated use get() function.
     *
     * @return bool
     */
    public function getObject($name, $time_to_live, $collection = '')
    {
        if ($this->is_enabled !== true) {
            return false;
        }

        $hash = $this->computeHash($name);
        $path = $this->computePath($collection, $hash);

        $this->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_start');

        if (!file_exists($path)) {
            $this->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_end');
            return false;
        }

        // Comparing timestamps
        if (filemtime($path) < time() - $time_to_live) {
            $this->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_end');
            return false;
        }

        $object = false; // Supposed to be overwritten
        /** @noinspection PhpIncludeInspection */
        @include($path);
        $this->benchmark->mark('cached_object_manager_get_object_' . $collection . '_' . $hash . '_end');
        return $object;
    }

    /**
     * TIP: Use get() function to get/set cache using a callback.
     *
     * Sets object by name. If store on destruct is set to true, the object will be storied with a delay.
     *
     * @param string $name
     * @param object $object
     * @param string $collection
     * @param bool $store_on_destruct
     *
     * @deprecated use get() function.
     *
     * @return bool
     */
    public function setObject($name, $object, $collection = '', $store_on_destruct = false)
    {
        if ($this->is_enabled !== true) {
            return false;
        }

        if (!$store_on_destruct) {
            return $this->storeObject($name, $object, $collection);
        }

        $this->objects[] = array(
            'name' => $name,
            'object' => $object,
            'collection' => $collection
        );

        return true;
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
        $file_path = $this->computePath($collection, $hash);

        $this->benchmark->mark('cached_object_manager_store_object_' . $collection . '_' . $hash . '_start');

        $error = false;

        // Serializing and saving - method #1
        $contents = '<?php // ' . $name . ' - Written at ' . date('Y-m-d, H:i:s') . "\n" . ' $object = unserialize(\'' . str_replace('\'', '\\\'', serialize($object_to_write)) . '\');';
        if (!@file_put_contents($file_path, $contents, LOCK_EX)) {
            $error = true;
        }

        // If there was an error - skip
        if (!$error) {
            // Testing if serialization worked
            @include($file_path);
            /** @noinspection PhpUndefinedVariableInspection */
            if (!isset($object) || !$object) {
                $this->benchmark->mark('cached_object_manager_failsafe_store_object_' . $collection . '_' . $hash . '_start');

                // Serializing and saving - method #2
                $contents = '<?php // ' . $name . ' - Written at ' . date('Y-m-d, H:i:s') . "\n" . ' $object = @unserialize(base64_decode(\'' . base64_encode(serialize($object_to_write)) . '\'));';
                if (!file_put_contents($file_path, $contents, LOCK_EX)) {
                    $error = true;
                } else {
                    \PiotrPolak\PepisCMS\Modulerunner\OpCacheUtil::safeInvalidate($file_path);
                }
                $this->benchmark->mark('cached_object_manager_failsafe_store_object_' . $collection . '_' . $hash . '_end');
            }
        }

        $this->benchmark->mark('cached_object_manager_store_object_' . $collection . '_' . $hash . '_end');

        if ($error) {
            Logger::error('Unable to write system cache ' . $file_path, 'SYSTEM');
            return false;
        }

        return true;
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
        if ($this->is_enabled !== true) {
            return false;
        }

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
            } else {
                Logger::error('Unable to delete system cache file ' . $file_path, 'SYSTEM');
            }

            \PiotrPolak\PepisCMS\Modulerunner\OpCacheUtil::safeInvalidate($file_path);
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
        return md5($name);
    }
}
