## New in version 1.0.0 (xx.xx.2018)

### New features

 * **PepisCMS codebase is cleaned up and the PepisCMS core is released as open source!**
 * PepisCMS is now Composer compatible and supports Composer autoload!
 * Dropped support for PHP 5.5 and lower. The code is now PHP 5.6 to PHP 7.2 compatible
 * Site and module configuration is now stored in database with a fallback to files!
 * Improved installer and migration scripts, implemented command line unattended installer
 * Installation script now reads default values from ENV variables
 * Implemented password history feature that prohibits from reusing previously set passwords (needs to be configured separately)
 * Improved authentication security: added timeout on consecutive unsuccessful authentications
 * Implemented automatic loading of module models upon first use (for modules)
 * Added Behat end-to-end tests covering core features
 * Implemented `CrudDefinitionBuilder` and `CrudFieldDefinitionBuilder` aiming to simplify CRUD definition setup
 * Improved module generation, implemented new module file structure with fallback to the legacy module structure (pre 1.0.0)
 * Fixed file manager previews
 * Improved backup utility now works even without `mysqldump` command available
 * Implemented fluent API for most used libraries
 * CodeIgniter upgraded to version 3.1.9
 * CKE Editor updated to 4.9.0
 * Pages got the possibility to define a page image
 * Migrated to the latest `phpspreadsheet` library
 * Implemented lazy image load for `DataGrid`, File Manager and CRUD modules to improve performance
 * Reviewed application translations
 * Added new utility library `Query_helper`
 * Extracted pages as a separate module
 * Added possibility to display widgets on the admin dashboard
 * Improved cache invalidation
 * Improved development features and developer's experience
 * Dropped support for PHPTal, FCKEditor
 * Dropped support for Spanish and Romanian languages
 * Dropped support for features previously marked as deprecated

### API Changes in 1.0.0.x branch

 * Extended database schema (see upgrade scripts [pepiscms/resources/sql/upgrade/1.0.0-stage1.sql](pepiscms/resources/sql/upgrade/1.0.0-stage1.sql))
 * **New `index.php` and `.htaccess` files!**
 * 3rd party backend components are now initialized using Composer, 3rd party frontend components are now moved out of this repository.
 * Removed methods:
    * `Generic_model::generateTextId()`,
    * `DataGrid:;getColumnDefinitions()`,
    * `DataGrid::getFilterDefinitions()`,
    * `FormBuilder::reset()`,
    * `SecurityPolicy::FULL_CONTROLL`,
    * `SimpleSessionMessage::setFormatingFunction()`,
    * `AdminCRUDController::getItemOrderCollumn()`,
    * `AdminCRUDController::getItemOrderConstraintCollumn()`,
    * `AdminCRUDController::isDetelable()`,
    * `AdminCRUDController::setDetelable()`,
    * `FormBuilder::addFileField()`,
    * `FormBuilder::addImageField()`,
    * `FormBuilder::MULTIPLEIMAGES`,
    * `MenuRendor::getMenuModules()`,
 * Deleted utilities:
    * `AssetMinimifier`,
    * `CrossDomainAuth`,
    * `CommonHook`
 * Removed constants:
    * `DataGrid::FILTER_CONDITION_EQAL` (typo)
 * `ModuleAdminController` and `ModuleController` are now abstract.
 * Automatic loading of module models
 * Removed deprecated customized `MY_DB_Cache`
 * Removed deprecated `Loader::plugin()`
 * *New* `index.php`! You must replace the existing file
 * `Auth_Driverable` is renamed to `AuthDriverableInterface`
 * `FormRenderable` is renamed to `FormRenderableInterface`
 * `Entitable` is now deprecated, please use `EntitableInterface`
 * `Moveable` is now deprecated, please use `MoveableInterface`
 * `Translateable` is now deprecated, please use `TranslateableInterface`
 
## 0.2.4.4 (04.01.2017)

### New features

 * CodeIgniter upgraded to version 3.0.3
 * CKE Editor updated to 4.5.6
 * Updated CKE Editor configuration to prevent removing custom content such as iframes
 * Fixed a bug in Pages that caused the Document instance inside a template to have no page ID
 * Created a full default theme template
 * Fixed login form styles, integrated reset.css into the layout.css
 * Fixed several front-page errors introduced previously while refactoring the code
 * Implemented `foreign_key_junction_where_conditions` flag for `FormBuilder::FOREIGN_KEY_MANY_TO_MANY`
 * Fixed an issue with user module that prevented admins to change password of other users when the password was too weak
 * Fixed behavior of formbuilder apply button due to XSS restrictions
 * Revision author displayed on revision history screen

## 0.2.4.3 (12.08.2015)

### New features

 * Improved form tooltip styles
 * Removed deprecated code from SASS styles

## 0.2.4.2 (17.07.2015)

### New features

 * Improved translator module that now invalidates optcache of edited translation files under PHP 5.6
 * FormBuilder refactored usage of get_instance(), fixed an issue introduced in the previous release
 * SimpleSessionMessage and its related code refactored

## 0.2.4.1 (02.07.2015)

### New features

 * Improved compatibility and modularity of admin module controllers
 * Changed behavior od CRUD order buttons - when filters are specified, the order buttons are disabled
 * Implemented `FormBuilder::COLORPICKER` field, improved FormBuilder styles and `setDefinition()` method
 * Extended `FormBuilder::IMAGE` field displaying metainfo and extension icons for unknown files
 * Improved module generator and module template
 * Improved AdminCRUDController API, added new helper methods such as `AdminCRUDController::getModuleName()` and `AdminCRUDController::getModel()`
 * Updated CAS library to 1.3.3 version
 * `DataGrid::clear()` and `FormBuilder::clear()` reset methods implemented, DataGrid and FormBuilder code reviewed.
 * Minor improvements to logs, system_info modules

## 0.2.4.0 (25.05.2015)

A mature version of 0.2.3 with minimal backward incompatibility, CodeIgniter upgraded to major version 3.0, 4 months in alpha - 53 releases, 2 beta releases

### New features

 * Upgraded CodeIgniter to version 3.0.0
 * Upgraded CKE Editor to new major version 4.4.6
 * Completely rewritten XLS/XLSX file import and export
 * Introduced unit test for selected components
 * Brand new SQL Console module
 * Application theme improved (menu dropdown, buttons realign, titles/bubbles fixed and removed when not needed)
 * Rewriten and compacted JavaScript UI
 * Application translation improved, removed outdated Romanian translation for administration panel
 * Extended configuration tests
 * New dashboard containing grouped links and configuration tests
 * System info moved to a separate module
 * Added Ssh_model to system core
 * Performance improvements to Array_model
 * Fail safe mechanism for serialization method in cached object manager, added benchmarking
 * Symfony2 bridge logs rewritten and improved, fixed minor issues
 * Backup moved to a separate module
 * Upgraded file extensions icons used in file manager
 * Upgraded and cleaned interface translations
 * Pages/menu user interface improved and standardized
 * XML file import improved for older versions of backup feeds
 * Content revisions system improved
 * Refactored logs widget, used in backup module
 * Improved file manager
 * Form builder now displays errors related to file uploads, multiple fixes
 * Added command line utility: `php index.php tools`
 * Improved development tools
 * Added security policy builder, changed security policy format (backward compatible)

### API Changes in 0.2.4.x branch

 * Removed `PEPISCMS_DATE` constant
 * CodeIgniter upgraded to 3.0, see upgrade instructions
    [http://www.codeigniter.com/userguide3/installation/upgrade_300.html?highlight=upgrade](http://www.codeigniter.com/userguide3/installation/upgrade_300.html?highlight=upgrade)
 * New index.php! You must replace the existing file
 * Change to the database.php file - please upgrade
    ```php
     $active_group = 'default';
     // $active_record = TRUE;
     $query_builder = TRUE;
    ```
 * Check behavior of `$_SERVER['QUERY_STRING']`, consider replacing it by `$_SERVER['REQUEST_URI']`
 * `Module_model::getInstalledModulesNamesDisplayedInUtilities()` - changed the behavior,
    now returning array of strings instead of array of objects
 * `$this->lang->getEnabledLanguages()` renamed to `$this->lang->getEnabledAdminLanguages()`;
 * File `array_model.php` becomes `Array_model.php`, check all the includes/requires
 * Removed unused `MY_URI`
 * Check your validation rules and remove xss_clean and trim - this will cause issues when the field is not required and empty
 * `$this->lang->load()` for module languages changed into `$this->lang->loadForModule()`
 * Method `MY_Upload:do_multiple_upload()` removed
 * Removed deprecated library `ReportBuilder`
 * `ModuleRunner::isModuleDisplayedInMenu()` when no module name is specified, it is automatically obtained
    from the currently running module
 * `SecurityPolicy::parsePolicy()` now returns a simple array, the function becomes protected
 * `MY_Output` cache - added failsafe behavior
 * New methods `ModuleRunner::isModuleDisplayedInMenu($name)` and `ModuleRunner::isModuleDisplayedInUtilities($name)`
 * `MY_Email::set_header()` removed as it has been implemented in the original CI_Email library
 * Changed icon paths (!) some icons will not work
 * CSS identifiers convention changed to lowercase_underscore
 * Changed file path pepiscms/theme/back_12.png -> pepiscms/theme/img/dialog/actions/back_16.png
 * Changed file path pepiscms/theme/star.png -> pepiscms/theme/img/dialog/actions/action_16.png
 * Changed file path pepiscms/theme/add.png -> pepiscms/theme/img/dialog/actions/add_16.png
 * CSS selectors `.fileTree` -> `.file_tree`
 * Removed legacy CSS `a.add` and `a.action`
 * Removed deprecated FormBuilder code
 
### Deprecated in 0.2.4.x branch

 * Deprecated `ModuleRunner::getInstalledModulesDisplayedInMenuCached()`,
    please use `ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()`
 * Deprecated `MenuRendor::getMenuModules()`, please use `ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()`
 * `AssetMinimifier` library
 * `Generic_model->generateTextId()` library
 * `AdminCRUDController::isDetelable()` is deprecated. Consider using `AdminCRUDController::isDeletable()`
 * `AdminCRUDController::setDetelable()` is deprecated. Consider using `AdminCRUDController::setDeletable()`
 * `SecurityPolicy::FULL_CONTROLL` is now deprecated and replaced by `SecurityPolicy::FULL_CONTROL`
 * `DataGrid::getFilterDefinitions()`
 * `DataGrid::getColumnDefinitions()`
 * `FormBuilder::reset()`, use `FormBuilder::clear()`
 * `FormBuilder::addImageField()`
 * `FormBuilder::addFileField()`
 * `FormBuilder::MULTIPLEIMAGES`
 * `SimpleSessionMessage::setFormatingFunction()` is deprecated. Consider using `SimpleSessionMessage::setFormattingFunction()`

## 0.2.3.4 (27.02.2015)

### New features

 * Fixed spreadsheet import glitch when required column contains 0 interpreted as FALSE

## 0.2.3.3 (18.02.2015)

### New features

 * Improved form builder, added reset method
 * Fixed rare misbehavior of CRUD import form

## 0.2.3.2 (10.02.2015)

### New features

 * Maintenance update
 * CodeIgniter upgraded to latest stable version 2.2.1
 * Minor bugfixes and improvements: improved `Generic_model::move()` function, fixed licenses path, improved module template and back redirects (`User_agent` usage fixed)

## 0.2.3.1 (16.01.2015)

### New features

 * Improved CRUD admin module generator template
 * Improved XLS file import and export for newer versions of PHP (5.4+)
 * SLQ Backup improved
 * New index.php template ready for CI3
 * Improved setup page - now able to specify site logo and logo anchor URL

## 0.2.3.0 (23.12.2014)

### New features

 * CodeIgniter upgraded to the latest version (2.2.0)
 * jQuery upgraded to 1.10
 * All builtin CMS database tables are now prefixed with cms_
 * Implemented full password sanding with variable hasing algorithm and any number of iterations (enhanced security!)
 * Removed deprecated code
 * Fancybox upgraded to Colorbox
 * Implement dropdown menu, rewritten `MenuRendor`, added possibility to attach modules to other modules
 * Site theme moved from raw CSS to Compass/SASS
 * Add possibility to define dashboard actions via ModuleDescriptor
 * Improved module generation
 * Brand new CMS installer with possibility to import Symfony database settings
 * File manager minor improvements
 * Improved UI of ACL management, groups management and system logs
 * More restrictive CAS Auth driver
 * Improved Generic_model
 * Reviewed and improved users module, added possibility to check CAS user status directly from admin panel
 * Added html_customization module that allows injecting code into HTML template of the administration panel
 * Added email_html helper
 * Modified DisplayPage public controller, added possibility to define `mainpage_module` and `mainpage_module_method` for handeling mainpage request by the specified module
 * Module generator now generates improved filters for date fields
 * CMS modules groups and users now renamed to cms_groups and cms_users
 * Improved Google chart generator
 * DataGrid got new method getAdvancedFeed() that acts as a proxy to feed object.
 * Upgraded SQL console module, fixed UTF-8 encoding in data view
 * Improved import procedure in `AdminCRUDController`
 * Fixed upload for CSV files
 * Improved Spreadsheet to detect tabs as CSV separators
 * Added Symfony2 bridge module (`symfony2_bridge`) allowing PepisCMS to use logic embedded in Symfony application
 * Added Symfony2 log preview utility
 * Implemented and validated all filters in `Array_model`
 * Module generator can now resolve many-to-many relationships
 * Implemented `Upgradedb` utility available at admin/upgradedb URL
 * Introduced content journaling

### API Changes in 0.2.3.x branch

 * Extended database schema (see upgrade scripts)
 * Removed deprecated legacy `Controller` and `Model` classes (`_compatibility.php`)
 * Removed deprecated `niceuri_helper.php`, function `niceuri()` can be found in string helper
 * Removed deprecated `Auth::setUserPreference()`, `Auth::getUserPreference()`
 * Removed deprecated `MenuRendor::getInstalledModules()`, `Use ModuleRunner::getAvailableModules()`
 * Removed `sqlconsole/TableUtility` deprecated utility, use CRUD/
 * Deprecated `Spreadsheet::parseCVS()`
 * Removed `Menu_model::getInstalledModulesNamesDisplayedInMenu()`
 * Removed `ModuleRunner::getInstalledModulesNamesDisplayedInMenuCached()`
 * Removed `Lang::getAvailableLanguages()`
 * Removed theme images up.gif and down.gif, use up.png and down.png respectively
 * Removed theme images:
    * exit.png
    * group_32.pngg
    * group_add_32.png
    * edit.gif
    * open.png
    * sdd.gif
    * user_active_32.png
    * user_add_32.png
    * user_inactive_32.png
    * user_32.png
    * user_16.png
 * Modules users, groups renamed to `cms_users`, `cms_groups`, PLEASE REVIEW YOUR SECURITY POLICY ANG GROUP ACCESS
 * Direct access to `self::$uri_prefix` and `self::$site_language` in subclasses of Dispatches has been deprecated
 * Added `Dispatcher::setSiteLanguage()`
 
## 0.2.2.13-LTS (14.05.2014)

### New features

 * Improved date and timestamp validation
 * Improved PHP5 compatibility

## 0.2.2.12-LTS (10.04.2014)

### New features

 * Improved method Generic_model::applyFilters for string EQ filters

## 0.2.2.11-LTS (18.03.2014)

### New features

 * Upgraded SQL console module, fixed UTF-8 encoding in data view

## 0.2.2.10-LTS (27.02.2014)

 * Reviewed and improved users module, added possibility to check CAS user status directly from admin panel
 * Minor fix in CAS driver, displaying correct error message for cases when user account is locally locked

## 0.2.2.9-LTS (03.02.2014)

### New features

 * Improved workaround in Uploads library to accept PDF files uploaded with wrong or non standard mime-type

## 0.2.2.8-LTS (20.01.2014)

### New features

 * Implemented workaround in Uploads library to accept ZIP/PDF files uploaded with wrong mime-type (as binary data)
 * Updated mime config
 * Minor changes in AdminCRUDController template

## 0.2.2.7 (07.01.2014)

### New features

 * Spreadsheet library method parseCSV now uses native mechanism for reading CSV files
 * Improvements to CLI
 * Improved widget compatibility
 * Fixed an error that was introduced during query builder usage refractoring some minor versions ago, in User_model and Menu_model
 * Added Array_model that allows building abstract models for CRUD from any source (XML, CSV, web services)
 * Added BasicDataFeedableInterface and Moveable interfaces

## 0.2.2.6 (01.12.2013)

### New features

 * IMPORTANT: CBACL will now throw an error and block access when project ID is set but no API KEY is present
 * Localized CAS driver error messages
 * Code inspection and reformat
 * Added Composer package description
 * FormBuilder foreign key fix in MANY_TO_MANY relationship (for entries having no ID)

## 0.2.2.5 (11.11.2013)

### New features

 * Improved CRUD and controller templates
 * Added CRUD import and export features
 * Fixed a typo in Spreadsheet library
 * User module now is got the possibility to set user password
 * Improved form builder - select fields now can have null values
 * Generic_model now is got "in" filter type, implemented MANY_TO_MANY filter in Datagrid table and filters
 * Module generator now generates labels and filters for boolean elements
 * Added YouTube helper
 * Added home button to main menu, improved MenuRendor, improved default dashboard
 * Added new validation rules: no_uppercase and no_lowercase
 * Improved translator interface
 * Fixed CAS logout
 * EmailSender is got the possibility to overwrite the default config
 * Addedd possibility to define FormBuilder field's options
 * Updated CKE to the latest version 3.6.6.1

## 0.2.2.4 (15.08.2013)

### New features

 * Improved controller templates
 * Added spreadsheet tests, fixed Spreadsheet library
 * Added logs performance tests
 * System backup now sends correct headers for SQL file
 * Improved string_helper `niceuri()`
 * Added dmesg module
 * Interface JavaScript refractored
 * Remote models retested and improved
 * Changed order of instructions in `Generic_model::getAdvancedFeed()`
 * CAS upgraded to 1.3.2+
 * Improved translator module - now all labels are automatically refreshed after save
 * Extended and improved module generator

## 0.2.2.3 (20.05.2013)

### New features

 * Improved documentation and controller templates
 * DataGrid minor change is setFilterValue
 * `Generic_model::getAdvancedFeed()` now applies filter field mappings to `order_by` field
 * Minor changes and code cleanup in TableUtility
 * Minor changes in Spreadsheet library
 * Cleanups and minor UI fixes

## 0.2.2.2 (07.03.2013)

### New features

 * Added new configuration options to Auth drivers: allowed_domains, allowed_usernames
 * Added Gmail Auth driver
 * Pages module bugfix
 * Minor change to FormBuilder: if saveById returns an integer then it is considered the instance ID, otherwise the `$this->db->insert_id()` is called
 * RTFEditor improvement - now it is possible to specify editor_styles_set_file in theme descriptor
 * Removed URI components

## 0.2.2.1 (31.01.2013)

### New features

 * Implemented import of CodeIgniter logs
 * Improved Auth drivers, updated CBACL gateway
 * Upgraded jQuery UI to the latest version, added time picker to FormBuilder
 * Implemented `development_tools/switch_user` allowing root users to switch accounts
 * Extended configuration tests
 * Minor fixes

## 0.2.2.0 (26.12.2012)

A mature version of 0.2.1

### New features

 * System setup UI fix
 * Auth `$session_variable_preffix` changed from pepis_cms to pepiscms
 * Added auth drivers
 * `Auth::renewUserData()` is now private, use `forceLogin()` or `refreshSession()` instead
 * Added possibility to map database table names
 * Generic model got the possibility to change database on fly
 * Added CRUD module generator
 * Added cache revalidation when installing/changing module - this makes all module changes directly visible
 * Fixed HTTP cache for thumbnails displayed in administration panel - improved page load speed
 * Extended FormBuilder image upload callback
 * Improved system translations
 * System logs became a separate module and now got some analytical features for finding related accounts and user/IP statistics
 * Simplified module installation, minor database change
 * Improved grid move procedure
 * Tested CLI support, now run any public controller: `php index.php /controller_name/method_name/extra_component`


### API Changes in 0.2.2.x branch

 * Changed modules database table, please execute the following queries:
    ```sql
ALTER TABLE `modules` DROP COLUMN `label`;
ALTER TABLE `modules` DROP COLUMN `description`;
ALTER TABLE `modules` DROP COLUMN `is_configurable`;
ALTER TABLE `modules` DROP COLUMN `is_displayed_in_sitemap`;
    ```
 * Removed deprecated controller constructors `AdminController`, `ModuleController`, `ModuleAdminController`
 * Removed deprecated method `EnhancedController::getParam()` Use `$this->input->getParam($paramName)`
 * Removed deprecated `ModuleController::uri_components`
 * Removed deprecated `GenericDataFeedable_model`
 * Added possibility to set page title in admin panel by assigning title variable:
    `$this->assign('title', 'Development tools');`

## 0.2.1.2 (26.07.2012)

### New features

 * Maintenance update
 * Fixed bug that always set English as default language
 * Datagrid filters autosubmit has been disabled
 * Improved Spreadsheet library

## 0.2.1.1 (26.06.2012)

### New features

 * Added timezone setting to configuration. IMPORTANT: UTC is no longer the default timezone! When upgrading please recompile the `_pepiscms.php` file.
 * Interface fixes - improved popup animations, improved form SELECT styles, some more minor changes
 * Improved datagrid pagination
 * Improved Spreadsheet library

## 0.2.1.0 (18.06.2012

### New features

 * Model loader can now load models from `INSTALLATIONPATH/application/models`
 * Improved language loads for different locations
 * User management as a separate module
 * New Spreadsheet library for generating and parsing CVS and Excel files, generating XML files
 * Form validation is got new methods for validating IMEI, bank account, SWIFT, PESEL and some more.
 * Minor UI fixes
 * Removed deprecated Validation and CacheControll
 * Generic_model is now able to map filtered fields (to be used in case of &quot;ambiguous&quot; error).
 * Added possibility to specify user login (optional). WARNING: User_model methods (register, update) are now changed!
 * Added configuration check after user login

### API Changes in 0.2.1.x branch

 * Database changes, module names can have longer values, user can now have a separate login
    ```sql
ALTER TABLE `logs` CHANGE COLUMN `module` `module` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL  ;
ALTER TABLE `modules` CHANGE COLUMN `name` `name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL  , CHANGE COLUMN `label` `label` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL;
ALTER TABLE `users` ADD COLUMN `user_login` VARCHAR(128) NULL DEFAULT NULL  AFTER `user_email`;
DELETE FROM group2entity WHERE entity=0 AND access=0;
    ```
    
## 0.2.0.0 (11.01.2012)

### New features

 * Brand new user interface
 * Improved database schema (partially backward compatible)
 * Improved security, user session expires after one hour of inactivity, the user is now forced to use strong passwords,
    passwords expire in a given period, user account is being lock after a number of unsuccessful authorization attempts
 * Improved data grid filters and forms
 * Introduced module descriptors, including installation/uninstallation procedures
 * Upgraded CKE Editor to the latest version
 * Added CRUD controller for building CRUD modules with ease
 * Added helpers and libraries for generating PDF/Excel files

### API Changes in 0.2.0.x branch

 * Introduced module descriptors
 * `getSitemapLinks` has now became `getSitemapURLs` and should be implemented in module descriptor
 * `getConfigVariables()` should now be implemented in module descriptor and should return FORMBUILDER DEFINITION!
 * Removed `ModuleRunner::getModuleSitemapLinks`
 * `FormBuilder::CHECKBOX` is not threated as boolean
 * `EnhancedController::getValue()` deprecated function removed (use $this->input->getValue or getAttribute)
 * `DataGrid::setColumns()` deprecated function removed (use setDefinition)
 * `DataGrid::addcollumn()` deprecated function removed (misspelled, use setDefinition)
 * Deprecated methods of `PluginPage` removed
 * Rewritten `Document` and `Menu` support for themes. Use `$document` instead of `$cms`
 * Pages and menu tables reduced, only `Page2menu_model` and `Menu2Uri_model` have been removed
 * FormBuilder is now got the possibility to resolve many-to-many relationships
 * Changed `Usergroups_model` intro `Group_model`, `Remoteapplications_model` into `Remote_application_model`, 
    `Sitelanguages_model` into `Site_language_model`
 * Changed behavior of `Language::load()`, now it includes English translation
    first and then it merges it with the destination language
 * CMSPage and `PluginPage` now replaced by `Document` object

## 0.1.5.0 (12.08.2011)

### New features

 * New api V5 compatible with CodeIgniter 2.0
 * Introduced XML-RPC webservices
 * Added SQL dump utility
 * Improved user interface, icons and layout facelift
 * SQL console and Translator now as builtin modules
 * New siteconfig utility
 * Rewritten page management
 * Dynamic base_url
 * Added URL helper functions
 * Improved security and Auth component
 * Compacted
 * Improved upgrade utility

### API Changes in 0.1.5.x branch

 * Validation is removed, use Form_validation
 * `$this->language` must be changed to `$this->lang`
 * `$this->db->orderby()` must be changed to `$this->db->order_by()`
 * `GenericDataFeedable` replaced by Generic_model, `require_once('ModelInterfaces.php')` is no longer needed
 * Make sure all callbacks take desired parameters by reference - callback functions of datagrid and form builder do not pass any objects by reference by default
 * niceuri, shortname now moved to string helper -> `$this->load->plugin('niceuri')` becomes `$this->load->helper('string')`
 * reflect2xml_pi now moved do xml helper
 * PHPTAL is no longer part of PepisCMS, to use `PHPTAL` configure `PHPTALPATH` anywhere in your application
 * FCK Editor and MCE Editor are no longer part of PepisCMS
 * jQuery is now moved to `pepiscms/3rdparty/jquery/jquery.min.js`
 * Removed config item `pages_extension`, replaced by `url_suffix`
 * FormBuilder is no longer pretending `back_url()` with `base_url()` if it contains http or https
 * Generic model is now being cloned in `FormBuilder` and `DataGrid` when using `setTable()` - check your callbacks

## 0.1.4.15 (02.05.2011)

### New features

 * Maintenance update
 * Added compatibility functions that let modules written for API V5 to be run on 0.1.4
 * Minor UI fixes
 * Extended pagination class

## 0.1.4.13 (15.04.2011)

### New features

 * Fixed load language procedure to detect user language
 * FormRenderable gets a new method for overloading the default error formatting delimiters
 * Added new validation methods to form_validation: min, max, even, odd
 * Added translation service for PHPTAL that takes translations from PepisCMS config files
 * Lang::load now detects language both for front-end and backend
 * Upgraded CKEditor to 3.5.3, FCK to 2.6.6, Tiny MCE 3.4.2, Fancybox 1.3.4, jQuery to 1.4.4
 * DisplayPage dispatcher now uses object cache for retrieving information about the current page and site language, partial support for CMSPage also
 * New installation script
 * Module configuration gets a new config variable type - numeric. Numeric variables automatically transform "," into "."
 * Added new APC page cache

## 0.1.4.11 (01.03.2011)

### New features

 * Added upload allowed types wildcard
 * Added Dispatcher::getUriPrefix() and Dispatcher::getSiteLanguage() methods

## 0.1.4.8 (15.02.2011)

### New features

 * Added support for PHPTAL via Template class
 * EnhancedController::getParam is now alias to URI::getParam
 * URI::shift is implemented
 * Updated jQuery UI Datepicker and it's style
 * DataGrid and FormBuilder support now definitions and foreign keys
 * Added &quot;apply&quot; button to FormBuilder. Note, if you use SimpleSessionMessage, the message must be read/assigned after the form is generated!
 * Improved ModuleRunner debug
 * Added complete Russian translation
 * `AdminModuleController::display()` now supports absolute file paths
 * Improved installer
 * Removed deprecated External Auth

## 0.1.4.2 (31.01.2011)

### New features

 * EnhancedController methods getControllerName() and getMethodName() implemented
 * Extended and fixed system info
 * Better support for Cyrillic alphabet, automatic generation of latin URLs using niceuri
 * Backup restore has been fixed
 * Backup restore now takes a backup of existing contents before making any changes, backup files can be found in application/backup/

## 0.1.4.1 (08.01.2011)

### New features

 * Maintenance update

## 0.1.4.0 (30.12.2010)

It took 7 months and 26 betas to release this version :)

### New features

 * New authorization mechanism, now a user can belong to several groups, every single group is a collection of rights above certain entities
 * Reorganized structure of AdminController constructor
 * Improved file manager interface and implemented protected file access for intranet instances
 * Extended upload allowed file extensions list and updated MIME types for zip files
 * Module registration introduced. Every single module must be enabled by system administrator in order to be run. Module configuration files are now storied in application/config/modules/ folder (however, old method might work as well).
 * System logs along with notifications for critical errors implemented
 * DataGrid component now got the possibility od displaying user configurable filers and default data feed model for simple tables
 * DataGrid default feed fix (posibility to apply = instead of LIKE)
 * Database changes: all the database tables' engine is changed to MYISAM, from now on all the timestamps are storied using UTC timestamp
 * Simplified database structure, removed view menu_view
 * Dropped support for MySQL 4
 * System information summary page added
 * New more efficient HTML cache mechanism, the cache system is now initialized before CodeIgniter framework and requires less resources to run
 * Added SMTP configuration and EmailSender library
 * CKE Editor upgraded to version 3.5
 * Fixed admin menu highlights
 * Add possibility to add branding to the top of CMS page
 * Modified Loader::config behavior. Now it is simpler to extend configuration as default config values are read from the library path
 * Rewritten system Loader
 
### API Changes in 0.1.4.x branch

 * NOTE: All PHP timestamps are now in UTC (time() now returns UTC value), please use UTC_TIMESTAMP() for MySQL
 * NOTE: Please change `$config['uri_protocol']	= "AUTO";` to `$config['uri_protocol'] = "QUERY_STRING";` in your config file
 * NOTE: `$config['permitted_uri_chars'] must be changed to 'a-z 0-9~%.:_&=-',` otherwise you can get "The URI you submitted has disallowed characters" error.
 * NOTE: `ModuleController getSitemapLinks()` is now called in static manner
 * NOTE: use `$this->input->post('query')` instead of `$_POST`
 * NOTE: `EnhancedController:getValue()`, `PluginPage::getValue()`, `PluginPage::assign()`, `PluginPage::getValue()` now deprecated

## 0.1.3.13 (03.06.2010)

### New features

 * Login controller error causing wrong redirect in systems having dynamic `base_url` fixed.

## 0.1.3.12 (21.05.2010)

### New features

 * Updated niceuri plugin

## 0.1.3.11 (01.04.2010)

### New features

 * Maintenance update

## 0.1.3.10 (24.03.2010)

### New features

 * Now the system supports user defined config files for hooks and autoload
 * Modified Hooks library to load user defined hooks
 * Modified content models, now the database supports to host content for several web sites sharing the user tables

## 0.1.3.9 (24.02.2010)

A Maintenance update.

## 0.1.3.8 (23.01.2010)

### New features

 * New ajax based file manager
 * Improved user interface, added search box and simple view for pages, new page edit view
 * Basic configuration tests implemented (displayed in utilities and settings)
 * Module runner improvements
 * CKEditor and FCK upgraded

