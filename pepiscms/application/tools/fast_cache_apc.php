<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
    $uri_hash = md5(INSTALLATIONPATH . $uri);
    return apc_fetch('page_' . $uri_hash);
}

/**
 * Sets cache for given URI
 * @param $uri
 * @param $output
 * @param $expires_in_seconds
 * @return mixed
 */
function fast_cache_set_cache_for_uri($uri, $output, $expires_in_seconds = 360)
{
    if ($expires_in_seconds < 1) {
        return FALSE;
    }

    $uri_hash = md5(INSTALLATIONPATH . $uri);
    $output .= "\n <!-- FastCache APC generated at: " . date(DATE_RFC822) . "-->";
    return apc_store('page_' . $uri_hash, $output, $expires_in_seconds);
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
        return FALSE;
    }

    $cache = fast_cache_get_cache_for_uri($uri);
    if ($cache === FALSE) {
        return FALSE;
    }

    header("PepisCMS-cache: true");
    die($cache);
}

// An attempt to serve cached page
if (isset($_SERVER['REQUEST_URI'])) {
    fast_cache_serve_for_uri_if_exists($_SERVER['REQUEST_URI']);
}