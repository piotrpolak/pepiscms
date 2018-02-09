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
 * Utility library for sending emails
 *
 * @since               0.1.4
 */
class EmailSender
{

    /**
     * Debug flag
     * @var boolean
     */
    private $debug = FALSE;
    private $charset = 'UTF-8';
    private $new_line = "\r\n";
    private $is_failsafe_enabled = FALSE;
    private $config = FALSE;

    /**
     * Sets new line delimiter
     *
     * @param string $new_line
     */
    public function setNewLine($new_line)
    {
        $this->new_line = $new_line;
    }

    /**
     * Returns new line delimiter
     *
     * @return string
     */
    public function getNewLine()
    {
        return $this->new_line;
    }

    /**
     * Sets charset
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Returns charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Enables/disables failsafe
     *
     * @param bool $is_failsafe_enabled
     */
    public function setFailsafeEnabled($is_failsafe_enabled)
    {
        $this->is_failsafe_enabled = $is_failsafe_enabled;
    }

    /**
     * Tells whether failsafe is enabled
     *
     * @return bool
     */
    public function isFailsafeEnabled()
    {
        return $this->is_failsafe_enabled;
    }

    /**
     * Sets debug option.
     * When debug is enabled the email is displayed to the browser and not sent to the destination.
     *
     * @param $debug
     *
     */
    public function debug($debug = TRUE)
    {
        $this->debug = $debug;
    }

    /**
     * smtp_host smtp_user smtp_pass smtp_port
     *
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Returns email config
     *
     * @return array
     */
    public function getConfig()
    {
        if (!$this->config) {
            $CI = &get_instance();
            $CI->load->config('email');

            $this->config = array();
            $this->config['smtp_host'] = $CI->config->item('email_smtp_host');
            $this->config['smtp_user'] = $CI->config->item('email_smtp_user');
            $this->config['smtp_pass'] = $CI->config->item('email_smtp_pass');
            $this->config['smtp_port'] = $CI->config->item('email_smtp_port');
        }
        return $this->config;
    }

    /**
     * Sends an email message
     *
     * @param string $to
     * @param string $from
     * @param string $from_name
     * @param string $subject
     * @param string $message
     * @param bool $html
     * @param array $attachments
     * @param string|boolean $reply_to
     * @param bool $reply_to_name
     * @return bool
     */
    public function send($to, $from, $from_name, $subject, $message, $html = FALSE, $attachments = array(), $reply_to = FALSE, $reply_to_name = FALSE)
    {
        if (!$html) {
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\n", "\r\n", $message);
        }

        if ($this->debug) {
            die($message);
        }

        $CI = &get_instance();
        $CI->load->library('email');
        $CI->load->config('email');

        $config = array();
        $config['protocol'] = 'mail';
        if ($CI->config->item('email_use_smtp')) {
            $config = $this->getConfig();
            $config['protocol'] = 'smtp';
        }

        if (strtoupper($this->getCharset()) != 'UTF-8') {
            $message = iconv('UTF-8', $this->getCharset(), $message);
        }

        $config['charset'] = $this->getCharset();
        if ($html) {
            $config['mailtype'] = 'html';
        } else {
            $config['mailtype'] = 'text';
            $config['wordwrap'] = TRUE;
        }

        $CI->email->initialize($config);
        $CI->email->from($from, $from_name);
        if (!$reply_to && !$reply_to_name) {
            $CI->email->reply_to($from, $from_name);
        } else {
            $CI->email->reply_to($reply_to, $reply_to_name);
        }
        //$CI->email->_set_header( 'Return-Path', $from_name . ' <' . $from . '>' );
        $CI->email->to($to);
        $CI->email->set_newline($this->getNewLine());
        $CI->email->subject($subject);
        $CI->email->message($message);
        //$CI->email->set_alt_message()

        foreach ($attachments as $attachment) {
            $CI->email->attach($attachment);
        }

        if (!$CI->email->send()) {
            //die( $CI->email->print_debugger() );
            // We tried to send an email using smtp and something did not work
            if ($config['protocol'] == 'smtp' && $this->isFailsafeEnabled()) {
                LOGGER::error('Unable to send email using SMTP, trying failsafe MAIL: ' . strip_tags($CI->email->print_debugger()), 'EMAIL');

                // This only happens when SMTP fails
                $config['protocol'] = 'mail';
                $CI->email->clear();

                $CI->email->initialize($config);
                $CI->email->from($from, $from_name);
                if (!$reply_to && !$reply_to_name) {
                    $CI->email->reply_to($from, $from_name);
                } else {
                    $CI->email->reply_to($reply_to, $reply_to_name);
                }
                //$CI->email->_set_header( 'Return-Path', $from_name . ' <' . $from . '>' );
                $CI->email->to($to);
                $CI->email->subject($subject);
                $CI->email->message($message);
                //$CI->email->set_alt_message()

                foreach ($attachments as $attachment) {
                    $CI->email->attach($attachment);
                }

                if (!$CI->email->send()) {
                    LOGGER::error('Unable to send email using MAIL (SMTP failsafe): ' . strip_tags($CI->email->print_debugger()), 'EMAIL');
                    return FALSE;
                }

                return TRUE;
            }

            LOGGER::error('Unable to send email using SMTP protocol: ' . strip_tags($CI->email->print_debugger()), 'EMAIL', FALSE, FALSE);
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Sends rendered message.
     * The email template path must be a path relative to index.php
     *
     * @param string $to
     * @param string $from
     * @param string $from_name
     * @param string $subject
     * @param string $email_template_path
     * @param array $data
     * @param bool $html
     * @param array $attachments
     * @return bool
     */
    public function sendTemplate($to, $from, $from_name, $subject, $email_template_path, $data = array(), $html = FALSE, $attachments = array())
    {
        $CI = &get_instance();

        ob_start();
        $CI->load->theme($email_template_path, $data);
        $message = ob_get_contents();
        @ob_end_clean();

        return $this->send($to, $from, $from_name, $subject, $message, $html, $attachments);
    }

    /**
     * Sends system email, emails must be located in application/emails
     *
     * @param string $to
     * @param string $from
     * @param string $from_name
     * @param string $subject
     * @param string $email_template_name
     * @param array $data
     * @param bool $html
     * @param bool $language
     * @param array $attachments
     * @return bool
     */
    public function sendSystemTemplate($to, $from, $from_name, $subject, $email_template_name, $data = array(), $html = FALSE, $language = FALSE, $attachments = array())
    {
        if (!$language) {
            $language = 'english';
        }

        if (!file_exists(APPPATH . 'emails/' . $language . '/') && $language != 'english') {
            $language = 'english';
        }

        get_instance()->load->helper('email_html');

        $email_template_path = APPPATH . 'emails/' . $language . '/' . $email_template_name . '.php';
        return $this->sendTemplate($to, $from, $from_name, $subject, $email_template_path, $data, $html, $attachments);
    }

}