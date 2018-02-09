# Breaking API changes and upgrade instructions

## New in version 1.0.0

 * Extended database schema (see upgrade scripts [pepiscms/resources/sql/upgrade/1..0.0-stage1.sql](pepiscms/resources/sql/upgrade/1..0.0-stage1.sql))
* 3rd party backend components are now initialized using Composer, 3rd party frontend components are now moved out of this repository.
* Generic_model::generateTextId(), DataGrid:;getColumnDefinitions(), DataGrid::getFilterDefinitions(), FormBuilder::reset(), SecurityPolicy::FULL_CONTROLL,
    SimpleSessionMessage::setFormatingFunction(), AdminCRUDController::getItemOrderCollumn(), AdminCRUDController::getItemOrderConstraintCollumn(),
    AdminCRUDController::isDetelable(), AdminCRUDController::setDetelable() deprecated methods removed
* Removed deprecated AssetMinimifier, CrossDomainAuth, CommonHook
* ModuleAdminController and ModuleController are now abstract.
* Automatic loading of module models.
* Removed deprecated customized MY_DB_Cache
* Removed deprecated Loader::plugin()
* New index.php! You must replace the existing file
* Auth_Driverable is renamed to AuthDriverableInterface
* Entitable is now deprecated, please use EntitableInterface
* Moveable is now deprecated, please use MoveableInterface
* Translateable is now deprecated, please use TranslateableInterface

## New in version 0.2.4

 * Removed PEPISCMS_DATE constant
 * CodeIgniter upgraded to 3.0, see upgrade instructions http://www.codeigniter.com/userguide3/installation/upgrade_300.html?highlight=upgrade
 * New index.php! You must replace the existing file
 * Change to the database.php file - please upgrade
     $active_group = 'default';
     // $active_record = TRUE;
     $query_builder = TRUE;
 * Check behavior of $_SERVER['QUERY_STRING'], consider replacing it by $_SERVER['REQUEST_URI']
 * Module_model::getInstalledModulesNamesDisplayedInUtilities() - changed the behavior, now returning array of strings instead of array of objects
 * $this->lang->getEnabledLanguages() renamed to $this->lang->getEnabledAdminLanguages();
 * File array_model.php becomes Array_model.php, check all the includes/requires
 * Removed unused MY_URI
 * Check your validation rules and remove xss_clean and trim - this will cause issues when the field is not required and empty
 * $this->lang->load() for module languages changed into $this->lang->loadForModule()
 * Method MY_Upload:do_multiple_upload() removed
 * Removed deprecated library ReportBuilder
 * ModuleRunner::isModuleDisplayedInMenu() when no module name is specified, it is autmatically obrained from the currently running module
 * SecurityPolicy::parsePolicy() now returns a simple array, the function becomes protecte
 * MY_Output cache - added failsafe behavior
 * New methods ModuleRunner::isModuleDisplayedInMenu($name) and ModuleRunner::isModuleDisplayedInUtilities($name) 
 * MY_Email::set_header() removed as it has been implemented in the original CI_Email library
 * Changed icon paths (!) some icons will not work
 * CSS identifiers convention changed to lowercase_underscore
 * Changed file path pepiscms/theme/back_12.png -> pepiscms/theme/img/dialog/actions/back_16.png
 * Changed file path pepiscms/theme/star.png -> pepiscms/theme/img/dialog/actions/action_16.png
 * Changed file path pepiscms/theme/add.png -> pepiscms/theme/img/dialog/actions/add_16.png
 * CSS selectors .fileTree -> .file_tree
 * Removed legacy CSS a.add and a.action
 * Removed deprecated FormBuilder code // Some backward compatibility // TODO Remove as PepisCMS 0.3
 
## Deprecated in version 0.2.4

 * Deprecated ModuleRunner::getInstalledModulesDisplayedInMenuCached(), please use ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()
 * Deprecated MenuRendor::getMenuModules(), please use ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()
 * AssetMinimifier library
 * Generic_model->generateTextId() library
 * AdminCRUDController::isDetelable() is deprecated. Consider using AdminCRUDController::isDeletable()
 * AdminCRUDController::setDetelable() is deprecated. Consider using AdminCRUDController::setDeletable()
 * SecurityPolicy::FULL_CONTROLL is now deprecated and replaced by SecurityPolicy::FULL_CONTROL
 * DataGrid::getFilterDefinitions()
 * DataGrid::getColumnDefinitions()
 * FormBuilder::reset(), use FormBuilder::clear()
 * FormBuilder::addImageField()
 * FormBuilder::addFileField()
 * FormBuilder::MULTIPLEIMAGES
 * SimpleSessionMessage::setFormatingFunction() is deprecated. Consider using SimpleSessionMessage::setFormattingFunction()

## New in version 0.2.3

 * Extended database schema (see upgrade scripts)
 * Removed deprecated Controller and Model classes (_compatibility.php)
 * Removed deprecated niceuri_helper.php, function niceuri() can be found in string helper
 * Removed deprecated Auth::setUserPreference, Auth::getUserPreference
 * Removed deprecated MenuRendor::getInstalledModules(), Use ModuleRunner::getAvailableModules()
 * Removed sqlconsole/TableUtility deprecated utility, use CRUD/
 * Deprecated Spreadsheet::parseCVS()
 * Removed Menu_model::getInstalledModulesNamesDisplayedInMenu()
 * Removed ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()
 * Removed Lang::getAvailableLanguages()
 * Removed theme images up.gif and down.gif, use up.png and down.png respectively
 * Removed theme images: exit.png group_32.png group_add_32.png edit.gif open.png sdd.gif user_active_32.png user_add_32.png user_inactive_32.png user_32.png user_16.png
 * Modules users, groups renamed to cms_users, cms_groups, PLEASE REVIEW YOUR SECURITY POLICY ANG GROUP ACCESS
 * Direct access to self::$uri_prefix and self::$site_language in subclasses of Dispatches has been deprecated
 * Added Dispatcher::setSiteLanguage()

## New in version 0.2.2

 * Changed modules database table, please execute the following queries:
	ALTER TABLE `modules` DROP COLUMN `label`;
	ALTER TABLE `modules` DROP COLUMN `description`;
	ALTER TABLE `modules` DROP COLUMN `is_configurable`;
	ALTER TABLE `modules` DROP COLUMN `is_displayed_in_sitemap`;
 * Removed deprecated controller constructors AdminController, ModuleController, ModuleAdminController
 * Removed deprecated method EnhancedController::getParam() Use $this->input->getParam( $paramName )
 * Removed deprecated ModuleController::uri_components
 * Removed deprecated GenericDataFeedable_model
 * Added possibility to set page title in admin panel by assigning title variable: $this->assign('title', 'Development tools');

## New in version 0.2.1

 * Database changes, module names can have longer values, user can now have a separate login
	ALTER TABLE `logs` CHANGE COLUMN `module` `module` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL  ;
	ALTER TABLE `modules` CHANGE COLUMN `name` `name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL  , CHANGE COLUMN `label` `label` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL;
	ALTER TABLE `users` ADD COLUMN `user_login` VARCHAR(128) NULL DEFAULT NULL  AFTER `user_email`;
	DELETE FROM group2entity WHERE entity=0 AND access=0;

## New in version 0.2.0

 * Introduced module descriptors
 * getSitemapLinks has now became getSitemapURLs and should be implemented in module descriptor
 * getConfigVariables() should now be implemented in module descriptor and should return FORMBUILDER DEFINITION!
 * Removed ModuleRunner::getModuleSitemapLinks
 * FormBuilder::CHECKBOX is not threated as boolean
 * EnhancedController::getValue deprecated function removed (use $this->input->getValue or getAttribute)
 * DataGrid::setColumns deprecated function removed (use setDefinition)
 * DataGrid::addcollumn deprecated function removed (misspelled, use setDefinition)
 * Deprecated methods of PluginPage removed
 * Rewriten Document and Menu support for themes. Use $document instead of $cms
 * Pages and menu tables reduced, only Page2menu_model and Menu2Uri_model have beed removed
 * FormBuilder is now got the posibility to resolve many-to-many relationships
 * Changed Usergroups_model intro Group_model, Remoteapplications_model into Remote_application_model, Sitelanguages_model into Site_language_model
 * Changed behavior of Language::load, now it includes English translation first and then it merges it with the destination language
 * CMSPage and PluginPage now replaced by Document object

## New in version 0.1.5

 * Validation is removed, use Form_validation
 * Replace $this->language must be changed to $this->lang
 * Replace $this->db->orderby() must be changed to $this->db->order_by()
 * GenericDataFeedable replaced by Generic_model, require_once('ModelInterfaces.php'); is no longer needed
 * Make sure all callbacks take desired parameters by reference - callback functions of datagrid and form builder do not pass any objects by referrence by defalut
 * niceuri, shortname now moved to string helper -> $this->load->plugin( 'niceuri' ); becomes $this->load->helper( 'string' );
 * reflect2xml_pi now moved do xml helper
 * PHPTAL is no longer part of PepisCMS, to use PHPTAL configure PHPTALPATH anywhere in your application
 * FCK Editor and MCE Editor are no longer part of PepisCMS
 * jQuery is now moved to pepiscms/3rdparty/jquery/jquery.min.js
 * Removed config item pages_extension, replaced by url_suffix
 * FormBuilder is no longer prepending back_url with base_url() if it contains http or https
 * Generic model is now being clonned in FormBuilder and DataGrid when using setTable - check your callbacks