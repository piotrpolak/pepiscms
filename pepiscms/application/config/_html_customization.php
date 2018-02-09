<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$config[ 'html_customization_logged_in_head_prepend' ] = '';
$config[ 'html_customization_not_logged_in_head_prepend' ] = '';
$config[ 'html_customization_logged_in_head_append' ] = '';
$config[ 'html_customization_not_logged_in_head_append' ] = '';
$config[ 'html_customization_logged_in_body_prepend' ] = '';
$config[ 'html_customization_not_logged_in_body_prepend' ] = '';
$config[ 'html_customization_logged_in_body_append' ] = '';
$config[ 'html_customization_not_logged_in_body_append' ] = '';

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/_html_customization.php'))
{
    include(INSTALLATIONPATH . 'application/config/_html_customization.php');
}