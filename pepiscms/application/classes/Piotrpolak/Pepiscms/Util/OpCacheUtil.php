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

namespace Piotrpolak\Pepiscms\Modulerunner;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Provides safe opcache wrappers.
 *
 * @since 1.0.0
 */
class OpCacheUtil
{
    /**
     * Invalidates OP cache for a single file.
     *
     * @param $file_path
     * @return bool
     */
    public static function safeInvalidate($file_path)
    {
        if (function_exists('opcache_invalidate')) {
            return opcache_invalidate($file_path, true);
        }

        return false;
    }

    /**
     * Resets all OP Cache.
     *
     * @return bool
     */
    public static function safeReset()
    {
        if (function_exists('opcache_reset')) {
            return opcache_reset();
        }

        return false;
    }
}
