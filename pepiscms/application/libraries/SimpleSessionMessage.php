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
 * SimpleSessionMessage
 *
 * @since 0.1.2
 */
class SimpleSessionMessage extends ContainerAware
{
    const FUNCTION_SUCCESS = 'display_success';
    const FUNCTION_TIP = 'display_tip';
    const FUNCTION_NOTIFICATION = 'display_notification';
    const FUNCTION_WARNING = 'display_warning';
    const FUNCTION_ERROR = 'display_error';

    protected $session_var_name = 'simple_session_message';

    /**
     * Default constructor
     *
     * @param array|boolean $params
     */
    public function __construct($params = FALSE)
    {
        if (!isset($_SESSION[$this->session_var_name . '_formatting_function'])) {
            $_SESSION[$this->session_var_name . '_formatting_function'] = self::FUNCTION_SUCCESS;
        }
    }

    /**
     * Sets message to be displayed, by label
     *
     * @param string $label_name
     * @param mixed $param1
     * @param mixed $param2
     * @param mixed $param3
     * @param mixed $param4
     * @return SimpleSessionMessage
     */
    public function setMessage($label_name, $param1 = null, $param2 = null, $param3 = null, $param4 = null)
    {
        if ($param1 === null) {
            $_SESSION[$this->session_var_name] = $this->lang->line($label_name);
        } else {
            $_SESSION[$this->session_var_name] = sprintf($this->lang->line($label_name), $param1, $param2, $param3, $param4);
        }

        return $this;
    }

    /**
     * Sets RAW message (not localized)
     *
     * @param string $message
     * @return SimpleSessionMessage
     */
    public function setRawMessage($message)
    {
        $_SESSION[$this->session_var_name] = $message;

        return $this;
    }

    /**
     * Sets message formatting function
     *
     * @param string $function_name
     * @return SimpleSessionMessage
     */
    public function setFormattingFunction($function_name = '')
    {
        $_SESSION[$this->session_var_name . '_formatting_function'] = $function_name;

        return $this;
    }

    /**
     * Returns localized message
     *
     * @return string|bool
     */
    public function getLocalizedMessage()
    {
        if (!isset($_SESSION[$this->session_var_name])) {
            return FALSE;
        }

        $message = $_SESSION[$this->session_var_name];
        unset($_SESSION[$this->session_var_name]);

        if (!isset($_SESSION[$this->session_var_name . '_formatting_function'])) {
            return $message;
        }

        $function = $_SESSION[$this->session_var_name . '_formatting_function'];
        unset($_SESSION[$this->session_var_name . '_formatting_function']);

        return $function($message);
    }

}
