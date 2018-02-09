<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Automatically generated language file

 * @date 2015-04-11
 * @file backup_lang.php
 */
$lang['backup_database_settings_not_found']          = 'Database connection settings not found. Make sure the configuration file is valid. Sometimes it is hard to determine database settings when you do includes in config files.';
$lang['backup_dump_disabled_on_windows']             = 'Database dump is disabled on Windows';
$lang['backup_dump_unable_to_make']                  = 'Unable to make database dump. See system logs.';
$lang['backup_dump_works_only_with_mysql']           = 'Database dump is works only with MySQL';
$lang['backup_index_tip']                            = 'Creating and restoring actions are logged into the system logs';
$lang['backup_module_description']                   = 'Backup the entire database, backup and restore pages\' content and menu structure';
$lang['backup_module_name']                          = 'Backup';
$lang['backup_sql_backup']                           = 'Backup SQL';
$lang['backup_sql_do']                               = 'Perform a full SQL backup';
$lang['backup_sql_do_description']                   = 'Dumps the entire database structure and contents into an downloadable SQL file';
$lang['backup_sql_do_groups_and_rights']             = 'Backup user access rights';
$lang['backup_sql_do_groups_and_rights_description'] = 'Dumps the part of the database containing user rights into an downloadable SQL file';
$lang['backup_xml_backup']                           = 'Backup XML - pages and menu structure';
$lang['backup_xml_do']                               = 'Back up pages';
$lang['backup_xml_do_description']                   = 'Creates a downloadable backup of the contents of the site';
$lang['backup_xml_restore']                          = 'Restore pages from backup';
$lang['backup_xml_restore_description']              = 'Restores menu structrure and page contents from XML backup file';
$lang['backup_xml_restore_not_a_valid_backup_file']  = 'The submited file is not a valid backup file. Aborting.';
$lang['backup_xml_restore_not_a_valid_xml_document'] = 'The submited file is not a valid XML document of no file was uploaded. Aborting.';
$lang['backup_xml_restore_tip']                      = 'Please upload XML backup file. Please note that the entire website structure will be overwritten.';
$lang['backup_xml_restore_unable_to_restore']        = 'Unable to restore contents from backup, database transaction failed.';