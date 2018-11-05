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
 * MY_Form_validation, provides some extra features
 *
 * @since 0.1.4.13
 *
 */
class PEPISCMS_Form_validation extends CI_Form_validation
{

    /**
     * PEPISCMS_Form_validation constructor.
     * @param array $rules
     */
    public function __construct($rules = array())
    {
        parent::__construct($rules);

        // The following code is needed for module callbacks to work
        $module_instance = ModuleRunner::get_instance();
        if ($module_instance) {
            unset($this->CI);
            $this->CI = $module_instance;
        }

        $this->CI->load->language('pepiscms_form_validation');
    }

    // --------------------------------------------------------------------

    /**
     * min
     *
     * @param string $str
     * @param mixed $val
     * @return bool
     */
    public function min($str, $val)
    {
        $success = $str >= $val;
        if (!$success) {
            $this->set_message('min', sprintf($this->CI->lang->line('pepiscms_form_validation_min'), '%s', $val));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * max
     *
     * @param string $str
     * @param mixed $val
     * @return bool
     */
    public function max($str, $val)
    {
        $success = $str <= $val;
        if (!$success) {
            $this->set_message('max', sprintf($this->CI->lang->line('pepiscms_form_validation_max'), '%s', $val));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * odd
     *
     * @param string $str
     * @return bool
     */
    public function odd($str)
    {
        $success = $str % 2 == 1;
        if (!$success) {
            $this->set_message('odd', $this->CI->lang->line('pepiscms_form_validation_odd'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * even
     *
     * @param string $str
     * @return bool
     */
    public function even($str)
    {
        $success = $str % 2 == 0;
        if (!$success) {
            $this->set_message('even', $this->CI->lang->line('pepiscms_form_validation_even'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * phone_number
     *
     * @param string $str
     * @param string $prefix
     * @return bool
     */
    public function valid_phone_number(&$str, $prefix = '')
    {
        $lenght = strlen($str);
        if (!$lenght) {
            return true;
        }

        //preg_replace('/\D/', '', $string)

        if ($lenght == 9) {
            $str = $prefix . $str;
        }

        $success = is_numeric($str) && strlen($str) == 11;


        if (!$success) {
            $this->set_message('valid_phone_number', $this->CI->lang->line('pepiscms_form_validation_phone_number'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Validates IBAN
     *
     * @param string $str
     * @return bool
     */
    private function _valid_iban($str)
    {
        $country_code = false;
        if (!is_numeric(substr($str, 0, 2))) {
            $country_code = substr($str, 0, 2);
        }
        if (!$country_code) {
            $country_code = 'PL'; // Default Poland
        }
        $str = $country_code . $str;

        $ibanReplaceChars = range('A', 'Z');

        foreach (range(10, 35) as $tempvalue) {
            $ibanReplaceValues[] = strval($tempvalue);
        }

        $str = str_replace($ibanReplaceChars, $ibanReplaceValues, substr($str, 0, 2)) . substr($str, 2);
        $str = substr($str, 6) . substr($str, 0, 6); // 6 digits = 4 chars

        return bcmod($str, 97) == 1;
    }

    // --------------------------------------------------------------------

    /**
     * Validates IBAN, requires php-bcmath
     *
     * yum install php-bcmath
     *
     * @param string $str
     * @return bool
     */
    public function valid_iban($str)
    {
        //http://en.wikipedia.org/wiki/International_Bank_Account_Number
        // 15 min for Norway (with chars)
        // 32 max (with chars)

        $str = str_replace(' ', '', $str);
        $success = $this->_valid_iban($str);

        if (!$success) {
            $this->set_message('valid_iban', $this->CI->lang->line('pepiscms_form_validation_valid_iban'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Validates Polish bank account number
     *
     * @param string $str
     * @return bool
     */
    public function valid_polish_bank_number($str)
    {
        $str = str_replace(array(' ', '-', '.'), '', $str);
        $success = is_numeric($str) && strlen($str) == 26 && $this->_valid_iban($str);

        if (!$success) {
            $this->set_message('valid_polish_bank_number', $this->CI->lang->line('pepiscms_form_validation_valid_polish_bank_number'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Validates bank SWIFT code
     *
     * @param string $str
     * @return bool
     */
    public function valid_swift_code($str)
    {
        // http://en.wikipedia.org/wiki/ISO_9362

        $str = str_replace(' ', '', $str);
        $len = strlen($str);
        $success = ($len == 7 || $len == 8 || $len == 11);

        if ($success) {
            if (!ctype_alnum($str)) { // TODO NOT A REAL CHECK
                $success = false;
            }
        }

        if (!$success) {
            $this->set_message('valid_swift_code', $this->CI->lang->line('pepiscms_form_validation_valid_swift_code'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Validates PESEL code
     *
     * @param string $str
     * @return bool
     */
    public function valid_pesel($str)
    {
        $str = str_replace(' ', '', $str);
        $success = is_numeric($str) && strlen($str) == 11;

        if (!$success) {
            $this->set_message('valid_pesel', $this->CI->lang->line('pepiscms_form_validation_valid_pesel'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Validates bank account number using a basic method
     *
     * @param string $str
     * @return bool
     */
    public function valid_bank_number_simple($str)
    {
        $str = str_replace(' ', '', $str);
        $success = is_numeric($str) && strlen($str) > 15;

        if (!$success) {
            $this->set_message('valid_bank_number_simple', $this->CI->lang->line('pepiscms_form_validation_valid_iban'));
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Validates if the date is of format YYYY-MM-DD and if it exists
     *
     * @param string $str
     * @return bool
     */
    public function valid_date($str)
    {
        if (strlen($str) == 0) {
            return true;
        }

        $matches = array();
        if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $str, $matches)) {
            $yy = $matches[1];  // first element of the array is year
            $mm = $matches[2];  // second element is month
            $dd = $matches[3];  // third element is days

            if (checkdate($mm, $dd, $yy)) {
                return true;
            }
        }

        $this->set_message('valid_date', $this->CI->lang->line('pepiscms_form_validation_valid_date'));
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Validates if the date is of format YYYY-MM-DD H:i:s and if it exists
     *
     * @param string $str
     * @return bool
     */
    public function valid_timestamp($str)
    {
        if (strlen($str) == 0) {
            return true;
        }

        $matches = array();
        if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/", $str, $matches)) {
            $yy = $matches[1];  // first element of the array is year
            $mm = $matches[2];  // second element is month
            $dd = $matches[3];  // third element is days

            $H = $matches[4];  // first element of the array is year
            $i = $matches[5];  // second element is month
            $s = $matches[6];  // third element is days

            if (checkdate($mm, $dd, $yy)) {
                if ($H >= 0 && $H <= 24
                    && $i >= 0 && $i <= 60
                    && $s >= 0 && $s <= 60
                ) {
                    return true;
                }
            }
        }

        $this->set_message('valid_timestamp', $this->CI->lang->line('pepiscms_form_validation_valid_timestamp'));
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Validates IMEI number
     *
     * @param string $str
     * @return bool
     */
    public function valid_imei($str)
    {
        // AABBBBBBCCCCCCD
        $success = false;
        if (is_numeric($str) && strlen($str) == 15) {
            $success = self::luhn($str);
        }
        if (!$success) {
            $this->set_message('valid_imei', $this->CI->lang->line('pepiscms_form_validation_valid_imei'));
        }

        return $success;
    }

    /**
     * Checks if all the characters are lowercase
     *
     * @param string $str
     * @return bool
     */
    public function no_uppercase($str)
    {
        $success = ($str == strtolower($str));
        if (!$success) {
            $this->set_message('no_uppercase', $this->CI->lang->line('pepiscms_form_validation_no_uppercase'));
        }

        return $success;
    }

    /**
     * Checks if all the characters are lowercase
     *
     * @param string $str
     * @return bool
     */
    public function no_lowercase($str)
    {
        $success = ($str == strtoupper($str));
        if (!$success) {
            $this->set_message('no_lowercase', $this->CI->lang->line('pepiscms_form_validation_no_lowercase'));
        }

        return $success;
    }

    /*
      Copyright (c) 2008, reusablecode.blogspot.com; some rights reserved.
      This work is licensed under the Creative Commons Attribution License. To view
      a copy of this license, visit http://creativecommons.org/licenses/by/3.0/ or
      send a letter to Creative Commons, 559 Nathan Abbott Way, Stanford, California
      94305, USA.
     */

    protected static function luhn($input)
    {
        $sum = 0;
        $odd = strlen($input) % 2;

        // Remove any non-numeric characters.
        if (!is_numeric($input)) {
            $input = preg_replace('/\D/', '', $input);
        }

        // Calculate sum of digits.
        for ($i = 0; $i < strlen($input); $i++) {
            $sum += $odd ? $input[$i] : (($input[$i] * 2 > 9) ? $input[$i] * 2 - 9 : $input[$i] * 2);
            $odd = !$odd;
        }

        // Check validity.
        return ($sum % 10 == 0) ? true : false;
    }
}
