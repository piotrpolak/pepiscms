<?php
// PepisCMS bypass

/**
 * Preset config variables for compatibility
 */
$config['cms_enable_frontend'] = true;
$config['cms_intranet'] = false;
$config['cms_enable_pages'] = true;
$config['cms_customization_logo'] = false;
$config['cms_customization_login_view_path'] = false;
$config['cms_customization_on_login_redirect_url'] = false;
$config['cms_enable_utilities'] = true;
$config['cms_enable_filemanager'] = true;
$config['cms_customization_site_public_url'] = false;
$config['cms_enable_reset_password'] = true;
$config['cms_login_page_description'] = '';
$config['timezone'] = false;

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/_pepiscms.php')) {
    include(INSTALLATIONPATH . 'application/config/_pepiscms.php');
}
