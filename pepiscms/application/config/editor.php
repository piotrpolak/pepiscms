<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Css file that will be attached to the CKE editor
 *
 * For best results use absolute paths
 * If a relative path is passed, then the file will be relative to
 * the current theme unless not prepended with / - then it is relative to the document root
 *
 * Default <current_theme>/editor.css
 */
$config['editor_css_file'] = 'editor.css';

/**
 * Body id
 * Without # sign
 */
$config['editor_css_body_id'] = 'content';

/**
 * Body class name
 * Without .sign
 */
$config['editor_css_body_class'] = '';


if (file_exists(INSTALLATIONPATH . 'application/config/editor.php')) {
    require_once INSTALLATIONPATH . 'application/config/editor.php';
}