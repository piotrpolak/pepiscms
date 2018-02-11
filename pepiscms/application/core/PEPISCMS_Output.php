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
 * Provides a way to overwrite default cache mechanisms.
 */
class PEPISCMS_Output extends CI_Output
{
    const REQUEST_URI_SERVER_ATTRIBUTE_NAME = 'REQUEST_URI';

    /**
     * Write Cache.
     *
     * @param string $output Output data to cache
     * @return void
     */
    public function _write_cache($output)
    {
        // Failsafe variable
        $no_fastcacheable = true;

        if (isset($_SERVER[self::REQUEST_URI_SERVER_ATTRIBUTE_NAME])) {
            if (is_callable('fast_cache_set_cache_for_uri')) {
                $no_fastcacheable = false;

                if (fast_cache_set_cache_for_uri($_SERVER[self::REQUEST_URI_SERVER_ATTRIBUTE_NAME], $output, $this->cache_expiration * 60)) {
                    log_message('debug', "Cache file written: " . $_SERVER[self::REQUEST_URI_SERVER_ATTRIBUTE_NAME]);
                }
            }
        }

        // Failsafe
        if ($no_fastcacheable) {
            return parent::_write_cache($output);
        }

        return FALSE;
    }

    /**
     * Update/serve cached output.
     *
     * @uses CI_Config
     * @uses CI_URI
     *
     * @param object &$CFG CI_Config class instance
     * @param object &$URI CI_URI class instance
     * @return bool TRUE on success or FALSE on failure
     */
    public function _display_cache(&$CFG, &$URI)
    {
        // Failsafe variable
        $no_fastcacheable = true;

        if (isset($_SERVER[self::REQUEST_URI_SERVER_ATTRIBUTE_NAME])) {
            if (is_callable('fast_cache_get_cache_for_uri')) {
                $no_fastcacheable = false;

                $cache = fast_cache_get_cache_for_uri($_SERVER[self::REQUEST_URI_SERVER_ATTRIBUTE_NAME]);
                if ($cache === FALSE) {
                    return FALSE;
                }

                // Display the cache
                $this->_display($cache);
                log_message('debug', "Cache file is current. Sending it to browser.");
                return TRUE;
            }
        }

        // Failsafe
        if ($no_fastcacheable) {
            return parent::_display_cache($CFG, $URI);
        }

        return FALSE;
    }

}
