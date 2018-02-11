<?php
// PepisCMS bypass

/**
 * Preset config variables for compatibility
 */
$config['cms_enable_frontend'] = TRUE;
$config['cms_intranet'] = FALSE;
$config['cms_enable_pages'] = TRUE;
$config['cms_customization_logo'] = FALSE;
$config['cms_customization_login_view_path'] = FALSE;
$config['cms_customization_on_login_redirect_url'] = FALSE;
$config['cms_enable_utilities'] = TRUE;
$config['cms_enable_filemanager'] = TRUE;
$config['cms_customization_site_public_url'] = FALSE;
$config['cms_enable_reset_password'] = TRUE;
$config['cms_login_page_description'] = '';
$config['timezone'] = FALSE;

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/_pepiscms.php')) {
    include(INSTALLATIONPATH . 'application/config/_pepiscms.php');
}