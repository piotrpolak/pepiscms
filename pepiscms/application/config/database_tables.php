<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['database_table_users'] = 'cms_users';
$config['database_table_group_to_entity'] = 'cms_group_to_entity';
$config['database_table_user_to_group'] = 'cms_user_to_group';
$config['database_table_groups'] = 'cms_groups';
$config['database_table_modules'] = 'cms_modules';
$config['database_table_logs'] = 'cms_logs';
$config['database_table_menu'] = 'cms_menu';
$config['database_table_pages'] = 'cms_pages';
$config['database_table_site_languages'] = 'cms_site_languages';
$config['database_table_journal'] = 'cms_journal';

// PepisCMS bypass
if (file_exists(INSTALLATIONPATH . 'application/config/database_tables.php')) {
    include(INSTALLATIONPATH . 'application/config/database_tables.php');
}
