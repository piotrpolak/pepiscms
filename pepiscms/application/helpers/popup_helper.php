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

if (!function_exists('popup_close_html')) {

    /**
     * Returns string used to close popup window
     *
     * @return string
     */
    function popup_close_html()
    {
        $out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<base href="' . base_url() . '" />
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<script type="text/javascript" src="' . base_url() . 'pepiscms/3rdparty/jquery/jquery.min.js"></script>
					<script type="text/javascript" src="' . base_url() . 'pepiscms/js/popup.js?v=' . PEPISCMS_VERSION . '"></script>
				</head>
				<body class="popup"><span class="popup_close"></span></body></html>';

        return $out;
    }

}