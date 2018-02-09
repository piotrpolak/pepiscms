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

if (!function_exists('email_html_open')) {

    function email_html_open($padding = 20) {
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Email</title>
		<style type="text/css">
			a:hover {
				text-decoration: none !important;
			}
		</style>
    </head>
	<body bgcolor="#EAEAEA" style="margin:0; padding: 0; background-color: #EAEAEA;">
		<table width="100%" style="margin-top: 10px; margin-bottom: 10px;">
			<tr>
				<td></td>
				<td width="<?=(600-($padding*2))?>" bgcolor="#FFF" style="padding: <?=$padding?>px; border: #D9D9D9 solid 1px; background-color: #FFF;">
			<?php
    }
}

if (!function_exists('email_html_close')) {

    function email_html_close() {
        ?></td><td></td></tr></table></body>
</html><?php
    }
}

if (!function_exists('email_h1_open')) {

    function email_h1_open() {
        ?><h1 style="color: #333; font-size: 25px; font-family: Arial; font-weight: bold; margin-top: 10px; margin-bottom: 15px;"><?php
    }
}

if (!function_exists('email_h1_close')) {

    function email_h1_close() {
        ?></h1><?php
    }
}

if (!function_exists('email_h2_open')) {

    function email_h2_open() {
        ?><h2 style="color: #333; font-size: 14px; font-family: Arial; font-weight: bold; margin-top: 20px;"><?php
    }
}

if (!function_exists('email_h2_close')) {

    function email_h2_close() {
        ?></h2><?php
    }
}

if (!function_exists('email_p_open')) {

    function email_p_open() {
        ?><p style="color: #333; font-size: 12px; font-family: Arial; margin-bottom: 5px; line-height: 20px;"><?php
    }
}

if (!function_exists('email_p_close')) {

    function email_p_close() {
        ?></p><?php
    }
}

if (!function_exists('email_a_open')) {

    function email_a_open($url) {
        ?><a style="color: #427DE3;" href="<?=$url?>"><?php
    }
}

if (!function_exists('email_a_close')) {

    function email_a_close() {
        ?></a><?php
    }
}

if (!function_exists('email_html_footer_open')) {

    function email_html_footer_open($padding=0, $margin_top=40) {
        ?><p style="color: #666; font-size: 10px; font-family: Arial; margin-bottom: 5px; line-height: 14px; padding: <?=$padding?>px; padding-top: 10px; border-top: #EAEAEA solid 1px; margin-top: <?=$margin_top?>px;"><?php
    }
}

if (!function_exists('email_html_footer_close')) {

    function email_html_footer_close() {
        ?></p><?php
    }
}