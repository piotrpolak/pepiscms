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
 * PepisCMS Fast cache
 * This component is intended to replace the default CI cache component
 * V0.1
 */

/**
 * Returns cache content for given URL
 * @param $uri
 * @return mixed
 */
function fast_cache_get_cache_for_uri($uri)
{
    $uri_hash = md5($uri);
    $filepath = INSTALLATIONPATH . 'application/cache/pages/' . $uri_hash . '.html';

    if (file_exists($filepath)) {
        if (filemtime($filepath) < time()) {
            @unlink($filepath);
            return false;
        }

        if (!$fp = fopen($filepath, 'rb')) {
            return false;
        }

        flock($fp, LOCK_SH);
        $cache = '';
        if (filesize($filepath) > 0) {
            $cache = fread($fp, filesize($filepath));
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        return $cache;
    }
    return false;
}

/**
 * Sets cache for given URI
 * @param $uri
 * @param $output
 * @param $expires_in_seconds
 * @return mixed
 */
function fast_cache_set_cache_for_uri($uri, $output, $expires_in_seconds = 0)
{
    if ($expires_in_seconds < 1) {
        return false;
    }

    $uri_hash = md5($uri);
    $filepath = INSTALLATIONPATH . 'application/cache/pages/' . $uri_hash . '.html';

    if (!file_exists(INSTALLATIONPATH . 'application/cache/pages/')) {
        @mkdir(INSTALLATIONPATH . 'application/cache');
        @mkdir(INSTALLATIONPATH . 'application/cache/pages');
    }

    if (!$fp = @fopen($filepath, 'wb')) {
        die("UNABLE TO WRITE"); //return FALSE;
    }

    $expire = time() + $expires_in_seconds;

    // DEBUG
    $output .= "\n <!-- FastCache FS generated at: " . date(DATE_RFC822) . "-->";

    flock($fp, LOCK_EX);
    fwrite($fp, $output);

    flock($fp, LOCK_UN);
    fclose($fp);

    touch($filepath, time() + $expires_in_seconds);
    return true;
}

/**
 * Reads and serves cache for given URI
 * @param $uri
 * @return mixed
 */
function fast_cache_serve_for_uri_if_exists($uri)
{
    //TODO Logged in administrators should be served with no-cache pages
    /**
     * Not serving cache for POST
     * Place here your conditions.
     * Logged in administrators should be served with no-cache pages
     */
    if (count($_POST) > 0) {
        return false;
    }

    $cache = fast_cache_get_cache_for_uri($uri);
    if ($cache === false) {
        return false;
    }

    header("PepisCMS-cache: true");
    die($cache);
}

// An attempt to serve cached page
if (isset($_SERVER['REQUEST_URI'])) {
    fast_cache_serve_for_uri_if_exists($_SERVER['REQUEST_URI']);
}
