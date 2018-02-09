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

if (!function_exists('youtube_get_id_by_url')) {

    /**
     * Extracts YouTube ID from film URL
     * @param string $url
     * @return string|boolean
     * @author https://stackoverflow.com/questions/5830387/how-do-i-find-all-youtube-video-ids-in-a-string-using-a-regex/5831191#5831191
     * @author http://stackoverflow.com/questions/6556559/youtube-api-extract-video-id
     */
    function youtube_get_id_by_url($url)
    {
        $pattern = '%^# Match any youtube URL
                (?:https?://)?  # Optional scheme. Either http or https
                (?:www\.)?      # Optional www subdomain
                (?:             # Group host alternatives
                  youtu\.be/    # Either youtu.be,
                | youtube\.com  # or youtube.com
                  (?:           # Group path alternatives
                    /embed/     # Either /embed/
                  | /v/         # or /v/
                  | .*v=        # or /watch\?v=
                  )             # End path alternatives.
                )               # End host alternatives.
                ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                ($|&).*         # if additional parameters are also in query string after video id.
                $%x';
        $result = preg_match($pattern, $url, $matches);
        if (false !== $result) {
            if (isset($matches[1])) {
                return $matches[1];
            }
        }

        return FALSE;
    }

}