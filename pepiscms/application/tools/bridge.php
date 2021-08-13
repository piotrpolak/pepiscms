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
 * PepisCMS file bridge
 * V0.4
 */

$bridged_path = str_replace('..', '', $_GET['bridge']);

$mime = array(
    'css' => 'text/css',
    'htm' => 'text/html',
    'js' => 'application/x-javascript',
    'xml' => 'application/xhtml+xml',
    'php' => 'text/html',
    'html' => 'text/html',
    'gif' => 'image/gif',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
);


$base = dirname(APPPATH) . '/';
$section_base_paths = array(
    '3rdparty/ckeditor/' => BASEPATH . '../../../ckeditor/ckeditor/',
    '3rdparty/' => BASEPATH . '../../../piotrpolak/pepiscms-3rdparty/',
    'js/' => $base . 'js/',
    'theme/' => $base . 'theme/',
    'modules/' => $base . 'modules/'
);

function bridge_show_not_found()
{
    header('HTTP/1.1 404 Not Found');
    die('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></head>');
}

if (!$bridged_path) {
    bridge_show_not_found();
}

$ext = strtolower(substr(strrchr($bridged_path, '.'), 1));
if (!$ext) {
    bridge_show_not_found();
}


$slash_pos = strpos($bridged_path, "/");
if ($slash_pos == false) {
    bridge_show_not_found();
}

$bridged_path_parts = explode('/', $bridged_path);

if ($bridged_path_parts[0] == 'usermodules' && $bridged_path_parts[2] == 'resources'){
    $user_module_name = $bridged_path_parts[1];
    $user_module_resources_directory = INSTALLATIONPATH . 'modules/' . $user_module_name .'/resources/';
    if (is_dir($user_module_resources_directory)){
        $section_base_paths['usermodules/'.$user_module_name.'/resources/'] = $user_module_resources_directory;
    }
}

$destination_file_path = false;
foreach ($section_base_paths as $key => $path) {
    if (strpos($bridged_path, $key) === 0) {
        $destination_file_path = $path . substr($bridged_path, strlen($key));
        break;
    }
}

if (!$destination_file_path || !isset($mime[$ext]) || !file_exists($destination_file_path) || !is_file($destination_file_path)) {
    bridge_show_not_found();
}

$offset = 86400 * 7; // 7 days
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
header('Content-Type: ' . $mime[$ext]);

readfile($destination_file_path);

die();
