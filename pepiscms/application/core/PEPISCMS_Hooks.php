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
 * Enhanced hooks class supporting instance defined hooks
 *
 */
class PEPISCMS_Hooks extends CI_Hooks
{

    /**
     * Run Hook
     *
     * Runs a particular hook
     *
     * @param    array $data Hook details
     * @return    bool    TRUE on success or FALSE on failure
     */
    protected function _run_hook($data)
    {
        // Closures/lambda functions and array($object, 'method') callables
        if (is_callable($data)) {
            is_array($data)
                ? $data[0]->{$data[1]}()
                : $data();

            return TRUE;
        } elseif (!is_array($data)) {
            return FALSE;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen
        if ($this->_in_progress === TRUE) {
            return FALSE;
        }

        // -----------------------------------
        // Set file path
        // -----------------------------------

        if (!isset($data['filepath'], $data['filename'])) {
            return FALSE;
        }

        // PepisCMS customization start
        $filepath = INSTALLATIONPATH . 'application/' . $data['filepath'] . '/' . $data['filename'];

        if (!file_exists($filepath)) {
            $filepath = APPPATH . $data['filepath'] . '/' . $data['filename'];
            if (!file_exists($filepath)) {
                return FALSE;
            }
        }
        // PepisCMS customization end

        // Determine and class and/or function names
        $class = empty($data['class']) ? FALSE : $data['class'];
        $function = empty($data['function']) ? FALSE : $data['function'];
        $params = isset($data['params']) ? $data['params'] : '';

        if (empty($function)) {
            return FALSE;
        }

        // Set the _in_progress flag
        $this->_in_progress = TRUE;

        // Call the requested class and/or function
        if ($class !== FALSE) {
            // The object is stored?
            if (isset($this->_objects[$class])) {
                if (method_exists($this->_objects[$class], $function)) {
                    $this->_objects[$class]->$function($params);
                } else {
                    return $this->_in_progress = FALSE;
                }
            } else {
                class_exists($class, FALSE) OR require_once($filepath);

                if (!class_exists($class, FALSE) OR !method_exists($class, $function)) {
                    return $this->_in_progress = FALSE;
                }

                // Store the object and execute the method
                $this->_objects[$class] = new $class();
                $this->_objects[$class]->$function($params);
            }
        } else {
            function_exists($function) OR require_once($filepath);

            if (!function_exists($function)) {
                return $this->_in_progress = FALSE;
            }

            $function($params);
        }

        $this->_in_progress = FALSE;
        return TRUE;
    }
}
