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
 * Memcache component for storing objects in fast distributed memory
 *
 * @since 0.1.4
 */
class CMSMemcache
{

    private static $memcache = FALSE;
    private static $host = 'localhost';
    private static $port = '11211';
    private static $variable_prefix = 'default';
    private static $enabled = FALSE;
    private static $is_connected = FALSE;

    /**
     * CMSMemcache constructor.
     * @param array|null $params
     */
    function __construct($params = NULL)
    {
        if (file_exists(INSTALLATIONPATH . 'application/config/memcache.php')) {
            include(INSTALLATIONPATH . 'application/config/memcache.php');
            /** @noinspection PhpUndefinedVariableInspection */
            self::$enabled = $config['memcache_enabled'];
            self::$host = $config['memcache_server_host'];
            self::$port = $config['memcache_server_port'];
            self::$variable_prefix = $config['memcache_variable_prefix'];
        }
    }

    /**
     * Tells if memcache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return self::$enabled;
    }

    /**
     * Checks connection and returns true if connected
     *
     * @return bool
     */
    public function checkConnection()
    {
        if (!self::$enabled) {
            return FALSE;
        }

        self::getMemcache();
        return self::$is_connected;
    }

    /**
     * Returns an instance of Memcache
     *
     * @return Memcache
     */
    private static function getMemcache()
    {
        if (!self::$memcache) {
            if (!class_exists('Memcache')) {
                return FALSE;
            }

            self::$memcache = new Memcache;
            self::$is_connected = @self::$memcache->connect(self::$host, self::$port); // or die('Unable to connect to Memcache server.');
        }
        return self::$memcache;
    }

    /**
     * Gets the specified key
     *
     * @param string|array $key
     * @return mixed
     */
    function get($key)
    {
        if (!self::$enabled) {
            return FALSE;
        }

        if (is_array($key)) {
            foreach ($key as &$k) {
                $k = self::$variable_prefix . '_' . $k;
            }
        } else {
            $key = self::$variable_prefix . '_' . $key;
        }

        return @self::getMemcache()->get($key);
    }

    /**
     * Sets the specified key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expires
     * @param bool $compress
     * @return bool
     */
    function set($key, $value, $expires = 360, $compress = FALSE)
    {
        if (!self::$enabled) {
            return FALSE;
        }

        $key = self::$variable_prefix . '_' . $key;
        return @self::getMemcache()->set($key, $value, $compress ? MEMCACHE_COMPRESSED : 0, $expires);
    }

    /**
     * Flushes all cache
     */
    function flushAll()
    {
        $fp = @fsockopen(self::$host, self::$port, $errno, $fsockerr, 1.0);
        fwrite($fp, "flush_all\r\n");
    }

}
