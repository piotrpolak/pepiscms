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
 * Utility class for logging user actions.
 * Logged events are storied in database table for further inspection and analysis.
 * All the methods should be called in static manner.
 *
 * @since 0.1.3
 */
class Logger
{
    const MESSAGE_LEVEL_INFO = 0;
    const MESSAGE_LEVEL_NOTICE = 5;
    const MESSAGE_LEVEL_WARNING = 10;
    const MESSAGE_LEVEL_ERROR = 15;
    const MESSAGE_LEVEL_DEBUG = 55;

    private static $config;

    /**
     * Default constructor
     */
    public function __construct()
    {
        set_error_handler('Logger::errorHandlerWithLogging');
        $CI = &get_instance();
        self::$config = $CI->config;
        self::$config->load('debug');
    }

    /**
     * Logs a message
     *
     * @param $message
     * @param int $level
     * @param string $collection
     * @param mixed $resource_id
     * @param bool|int $user_id
     * @param bool|int $timestamp
     * @return bool
     */
    public static function log($message, $level = self::MESSAGE_LEVEL_INFO, $collection = 'SYSTEM', $resource_id = false, $user_id = false, $timestamp = false)
    {
        $CI = &get_instance();
        $CI->load->helper('date');

        $ip = $CI->input->ip_address();

        if (!$user_id) {
            $CI->load->library('Auth');
            if (isset($CI->auth)) {
                $user_id = $CI->auth->getUserId();
            }
        }
        $url = 'http' . ((!empty($_SERVER['HTTPS'])) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        if (!$timestamp) {
            $timestamp = utc_timestamp();
        }

        $CI->db->set('message', substr($message, 0, 2048))->set('timestamp', $timestamp)->set('level', $level)->set('ip', $ip);
        if ($user_id) {
            $CI->db->set('user_id', $user_id);
        }
        if ($referer) {
            $CI->db->set('referer', $referer);
        }

        $CI->db->set('collection', $collection)->set('resource_id', $resource_id)->set('url', $url);

        if (isset($CI->modulerunner)) {
            if (($module = $CI->modulerunner->getRunningModuleName())) {
                $CI->db->set('module', $module);
            }
        }

        return $CI->db->insert(self::$config->item('database_table_logs'));
    }

    /**
     * Logs an INFO message
     *
     * @param string $message
     * @param string $collection
     * @param string|bool $resource_id
     * @return bool
     */
    public static function info($message, $collection = 'SYSTEM', $resource_id = false)
    {
        return self::log($message, self::MESSAGE_LEVEL_INFO, $collection, $resource_id);
    }

    /**
     * Logs a NOTICE message
     *
     * @param string $message
     * @param string $collection
     * @param string|bool $resource_id
     * @return bool
     */
    public static function notice($message, $collection = 'SYSTEM', $resource_id = false)
    {
        return self::log($message, self::MESSAGE_LEVEL_NOTICE, $collection, $resource_id);
    }

    /**
     * Logs a WARNING message
     *
     * @param string $message
     * @param string $collection
     * @param string|bool $resource_id
     * @return bool
     */
    public static function warning($message, $collection = 'SYSTEM', $resource_id = false)
    {
        return self::log($message, self::MESSAGE_LEVEL_WARNING, $collection, $resource_id);
    }

    /**
     * Logs an ERROR message
     * Please make sure you log errors only for critical actions.
     * A system administrator is notified when an error occurs.
     *
     * @param string $message
     * @param string $collection
     * @param string|bool $resource_id
     * @param bool $notify
     * @return bool
     */
    public static function error($message, $collection = 'SYSTEM', $resource_id = false, $notify = true)
    {
        if ($notify && self::$config->item('debug_maintainer_email_address')) {
            $hash = md5($message . '' . $collection);
            $path = INSTALLATIONPATH . 'application/cache/errors/';
            if (!file_exists($path)) {
                mkdir($path);
            }

            $path .= $hash . '.lock';
            if (!file_exists($path)) {
                // Sending email
                touch($path);

                $CI = &get_instance();
                $CI->load->library('user_agent');
                $CI->load->library('EmailSender');

                $subject = '[ERROR] ' . $collection;
                $to_email = self::$config->item('debug_maintainer_email_address');
                $from_email = self::$config->item('site_email');
                $from_name = self::$config->item('site_name');
                $url = 'http' . ((!empty($_SERVER['HTTPS'])) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                $trace = self::getDebugTrace();
                array_shift($trace);

                $edata = array(
                    'message' => $message,
                    'hash' => $hash,
                    'date' => date('Y-m-d, h:i:s'),
                    'collection' => $collection,
                    'base_url' => base_url(),
                    'site_name' => $from_name,
                    'url' => $url,
                    'trace' => $trace,
                    'ip' => $CI->input->ip_address(),
                    'agent' => $CI->agent->agent_string(),
                    'headers' => $CI->input->request_headers(),
                );

                //$CI->emailsender->debug();
                if ($CI->emailsender->sendSystemTemplate($to_email, $from_email, $from_name, $subject, 'error_report', $edata, false, 'english')) {
                    $message .= '; error report sent by email';
                } else {
                    $message .= '; unable to send report by email';
                }
            } else {
                $message .= '; error report not send by email, lock exists';
            }
        }
        return self::log($message, self::MESSAGE_LEVEL_ERROR, $collection, $resource_id);
    }

    /**
     * Logs a DEBUG message
     *
     * @@param string $message
     * @param string $collection
     * @param string|bool $resource_id
     * @return bool
     */
    public static function debug($message, $collection = 'SYSTEM', $resource_id = false)
    {
        return self::log($message, self::MESSAGE_LEVEL_DEBUG, $collection, $resource_id);
    }

    /**
     * Resets error lock, reenables
     * @param string $hash
     * @return bool
     */
    public static function resetErrorLock($hash)
    {
        $path = INSTALLATIONPATH . 'application/cache/errors/' . $hash . '.lock';
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * This function should not be called from outsite. For internal use only, must be public for some reasons.
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public static function errorHandlerWithLogging($errno, $errstr, $errfile, $errline)
    {
        if ($errno == E_USER_ERROR) {
            if (self::$config->item('debug_log_php_error')) {
                Logger::error($errstr . '; line: ' . $errline . '; file: ' . $errfile, 'PHP');
            }

            echo '<h1>' . $errstr . '; line: ' . $errline . '; file: ' . $errfile . '</h1>';
            echo impode("<br>\n", self::getDebugTrace());
            return true;
        } elseif ($errno == E_USER_WARNING || $errno == E_WARNING) {
            if (self::$config->item('debug_log_php_warning')) {
                Logger::warning($errstr . '; line: ' . $errline . '; file: ' . $errfile, 'PHP');
            }
        } elseif ($errno == E_USER_DEPRECATED) {
            if (self::$config->item('debug_log_php_deprecated')) {
                $caller = self::getCallerLine(3);
                Logger::warning('DEPRECATED: ' . $errstr . '; used in ' . $caller[0] . '; line number ' . $caller[1], 'PHP');
            }
        }

        /* Don't execute PHP internal error handler */
        #return true;

        return false;
    }

    // -------------------------------------------------------------------------
    // Debugger functions
    // -------------------------------------------------------------------------

    /**
     * Returns debug trace of the application
     *
     * @return array
     */
    public function getDebugTrace()
    {
        $debug_backtrace = debug_backtrace();

        $trace = array();
        array_shift($debug_backtrace);
        foreach ($debug_backtrace as $entry) {
            $trace[] = (isset($entry['class']) ? $entry['class'] . '::' : '') . $entry['function'] . (isset($entry['line']) ? '; line: ' . $entry['line'] : '') . (isset($entry['file']) ? '; file: ' . str_replace('\\', '/', $entry['file']) : '');
        }

        return $trace;
    }

    /**
     * Returns name of calling function
     *
     * gets the calling line of the function it was called from,
     * i.e., this function's grandparent.
     *
     * @param int $levels
     * @return array
     */
    private static function getCallerLine($levels = 2)
    {
        $bt = debug_backtrace();

        $file = isset($bt[$levels]['file']) ? $bt[$levels]['file'] : '';
        $line = isset($bt[$levels]['line']) ? $bt[$levels]['line'] : '';

        return array($file, $line);
    }
}
