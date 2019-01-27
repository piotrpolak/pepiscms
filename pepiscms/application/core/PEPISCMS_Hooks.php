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
 * Enhanced hooks class supporting instance defined hooks.
 *
 */
class PEPISCMS_Hooks extends CI_Hooks
{
    /**
     * Run Hook.
     *
     * Runs a particular hook.
     *
     * @param    array $data Hook details
     * @return    bool    true on success or false on failure
     */
    protected function _run_hook($data)
    {
        // Closures/lambda functions and array($object, 'method') callables
        if (is_callable($data)) {
            is_array($data)
                ? $data[0]->{$data[1]}()
                : $data();

            return true;
        } elseif (!is_array($data)) {
            return false;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen
        if ($this->_in_progress === true) {
            return false;
        }

        // -----------------------------------
        // Set file path
        // -----------------------------------

        if (!isset($data['filepath'], $data['filename'])) {
            return false;
        }

        // PepisCMS customization start
        $filepath = INSTALLATIONPATH . 'application/' . $data['filepath'] . '/' . $data['filename'];

        if (!file_exists($filepath)) {
            $filepath = APPPATH . $data['filepath'] . '/' . $data['filename'];
            if (!file_exists($filepath)) {
                return false;
            }
        }
        // PepisCMS customization end

        // Determine and class and/or function names
        $class = empty($data['class']) ? false : $data['class'];
        $function = empty($data['function']) ? false : $data['function'];
        $params = isset($data['params']) ? $data['params'] : '';

        if (empty($function)) {
            return false;
        }

        // Set the _in_progress flag
        $this->_in_progress = true;

        // Call the requested class and/or function
        if ($class !== false) {
            // The object is stored?
            if (isset($this->_objects[$class])) {
                if (method_exists($this->_objects[$class], $function)) {
                    $this->_objects[$class]->$function($params);
                } else {
                    return $this->_in_progress = false;
                }
            } else {
                class_exists($class, false) or require_once($filepath);

                if (!class_exists($class, false) or !method_exists($class, $function)) {
                    return $this->_in_progress = false;
                }

                // Store the object and execute the method
                $this->_objects[$class] = new $class();
                $this->_objects[$class]->$function($params);
            }
        } else {
            function_exists($function) or require_once($filepath);

            if (!function_exists($function)) {
                return $this->_in_progress = false;
            }

            $function($params);
        }

        $this->_in_progress = false;
        return true;
    }
}
