<?= display_breadcrumb(array(admin_url() . 'about' => $this->lang->line('global_about_pepiscms')), 'pepiscms/theme/img/about/about_32.png') ?>
<?= display_breadcrumb(array(admin_url() . 'about' => $this->lang->line('global_about_pepiscms')), 'pepiscms/theme/img/about/about_32.png') ?>

<?php
$actions = array(
    array(
        'name' => $this->lang->line('global_button_back'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    ),
    array(
        'name' => $this->lang->line('global_about_pepiscms'),
        'link' => admin_url() . 'about',
        'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
    ),
    array(
        'name' => $this->lang->line('about_label_theme_preview'),
        'link' => admin_url() . 'about/theme',
        'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
    ),
);
?>
<?= display_action_bar($actions) ?>

<?= display_session_message() ?>

<div id="two_pane_layout">
    <div class="left_option_pane">
        <div style="padding-right: 2em;">
            <p>PepisCMS is an extensible web based content management system developed by Piotr Polak <a href="http://www.polak.ro/" target="_blank">www.polak.ro</a>.</p>
            <p>PepisCMS is written in PHP on top of extended CodeIgniter framework.</p>
            <h1 class="contrasted">Copyright</h1>
            <p>&copy; Copyright Piotr Polak 2007-<?= date('Y') ?></p>
        </div>
    </div>
    <div class="right_content_pane">
        <h1 class="contrasted">Changelog</h1>
        <h2>1.0.0 (XX.XX.2018)</h2>
        <ul>
            <li>PepisCMS codebase is cleaned up an the project is released as open source!</li>
            <li>PepisCMS is now Composer compatible and supports Composer autoload!</li>
            <li>Codebase cleaned up from any proprietary code</li>
            <li>Improved installer and migration scripts, implemented command line unattended installer</li>
            <li>Installation script now reads default values from ENV variables</li>
            <li>Implemented automatic loading of module models upon first use</li>
            <li>Dropped support for Spanish and Romanian languages</li>
            <li>Added basic Behat smoke tests</li>
            <li>Implemented CrudDefinitionBuilder and CrudFieldDefinitionBuilder aiming to simplify CRUD definition setup</li>
            <li>Improved module generation, implemented new module file structure with fallback to the legacy module structure (pre 1.0.0)</li>
            <li>Fixed file manager previews</li>
            <li>Improved backup utility now works even without mysqldump command available</li>
            <li>Implemented fluent API for most used libraries</li>
            <li>Dropped support for deprecated features</li>
            <li>CodeIgniter upgraded to version 3.1.8</li>
            <li>CKE Editor updated to 4.9.0</li>
            <li>Implemented lazy image load for DataGrid, File Manager and CRUD modules to improve performance</li>
            <li>Added password history (and check) for improved password security.</li>
        </ul>
        <h2>0.2.4.4 (04.01.2017)</h2>
        <ul>
            <li>CodeIgniter upgraded to version 3.0.3</li>
            <li>CKE Editor updated to 4.5.6</li>
            <li>Updated CKE Editor configuration to prevent removing custom content such as iframes</li>
            <li>Fixed a bug in Pages that caused the Document instance inside a template to have no page ID</li>
            <li>Created a full default theme template</li>
            <li>Fixed login form styles, integrated reset.css into the layout.css</li>
            <li>Fixed several front-page errors introduced previously while refactoring the code</li>
            <li>Implemented foreign_key_junction_where_conditions flag for FormBuilder::FOREIGN_KEY_MANY_TO_MANY</li>
            <li>Fixed an issue with user module that prevented admins to change password of other users when the password was too weak</li>
            <li>Fixed behavior of formbuilder apply button due to XSS restrictions</li>
            <li>Revision author displayed on revision history screen</li>
        </ul>
        <h2>0.2.4.3 (12.08.2015)</h2>
        <ul>
            <li>Improved form tooltip styles</li>
            <li>Removed deprecated code from SASS styles</li>
        </ul>
        <h2>0.2.4.2 (17.07.2015)</h2>
        <ul>
            <li>Improved translator module that now invalidates optcache of edited translation files under PHP 5.6</li>
            <li>FormBuilder refactored usage of get_instance(), fixed an issue introduced in the previous release</li>
            <li>SimpleSessionMessage and its related code refactored</li>
        </ul>
        <h2>0.2.4.1 (02.07.2015)</h2>
        <ul>
            <li>Improved compatibility and modularity of admin module controllers</li>
            <li>Changed behavior od CRUD order buttons - when filters are specified, the order buttons are disabled</li>
            <li>Implemented FormBuilder::COLORPICKER field, improved FormBuilder styles and setDefinition() method</li>
            <li>Extended FormBuilder::IMAGE field displaying metainfo and extension icons for unknown files</li>
            <li>Improved module generator and module template</li>
            <li>Improved AdminCRUDController API, added new helper methods such as AdminCRUDController::getModuleName() and AdminCRUDController::getModel()</li>
            <li>Updated CAS library to 1.3.3 version</li>
            <li>DataGrid::clear() and FormBuilder::clear() reset methods implemented, DataGrid and FormBuilder code review</li>
            <li>Minor improvements to logs, system_info modules</li>
        </ul>
        <h2>0.2.4.0 (25.05.2015)</h2>
        <p>A mature version of 0.2.3 with minimal backward incompatibility, CodeIgniter upgraded to major version 3.0, 4 months in alpha - 53 releases, 2 beta releases</p>
        <ul>
            <li>Upgraded CodeIgniter to version 3.0.0</li>
            <li>Upgraded CKE Editor to new major version 4.4.6</li>
            <li>Completely rewritten XLS/XLSX file import and export</li>
            <li>Introduced unit test for selected components</li>
            <li>Brand new SQL Console module</li>
            <li>Application theme improved (menu dropdown, buttons realign, titles/bubbles fixed and removed when not needed)</li>
            <li>Rewriten and compacted JavaScript UI</li>
            <li>Application translation improved, removed outdated Romanian translation for administration panel</li>
            <li>Extended configuration tests</li>
            <li>New dashboard containing grouped links and configuration tests</li>
            <li>System info moved to a separate module</li>
            <li>Added Ssh_model to system core</li>
            <li>Performance improvements to Array_model</li>
            <li>Fail safe mechanism for serialization method in cached object manager, added benchmarking</li>
            <li>Symfony2 bridge logs rewritten and improved, fixed minor issues</li>
            <li>Backup moved to a separate module</li>
            <li>Upgraded file extensions icons used in file manager</li>
            <li>Upgraded and cleaned interface translations</li>
            <li>Pages/menu user interface improved and standardized</li>
            <li>XML file import improved for older versions of backup feeds</li>
            <li>Content revisions system improved</li>
            <li>Refactored logs widget, used in backup module</li>
            <li>Improved file manager</li>
            <li>Form builder now displays errors related to file uploads, multiple fixes</li>
            <li>Added command line utility: <i>php index.php tools</i></li>
            <li>Improved development tools</li>
            <li>Added security policy builder, changed security policy format (backward compatible)</li>
        </ul>
        <h2>0.2.3.4 (27.02.2015)</h2>
        <ul>
            <li>Fixed spreadsheet import glitch when required column contains 0 interpreted as FALSE</li>
        </ul>
        <h2>0.2.3.3 (18.02.2015)</h2>
        <ul>
            <li>Improved form builder, added reset method</li>
            <li>Fixed rare misbehavior of CRUD import form</li>
        </ul>
		<h2>0.2.3.2 (10.02.2015)</h2>
        <ul>
			<li>Maintenance update</li>
            <li>CodeIgniter upgraded to latest stable version 2.2.1</li>
			<li>Minor bugfixes and improvements: improved Generic_model::move function, fixed licenses path, improved module template and back redirects (User_agent usage fixed)</li>
        </ul>
        <h2>0.2.3.1 (16.01.2015)</h2>
        <ul>
            <li>Improved CRUD admin module generator template</li>
            <li>Improved XLS file import and export for newer versions of PHP (5.4+)</li>
            <li>SLQ Backup improved</li>
            <li>New index.php template ready for CI3</li>
            <li>Improved setup page - now able to specify site logo and logo anchor URL</li>
        </ul>
        <h2>0.2.3.0 (23.12.2014)</h2>
        <ul>
            <li>CodeIgniter upgraded to the latest version (2.2.0)</li>
            <li>jQuery upgraded to 1.10</li>
            <li>All builtin CMS database tables are now prefixed with cms_</li>
            <li>Implemented full password sanding with variable hasing algorithm and any number of iterations (enhanced security!)</li>
            <li>Removed deprecated code</li>
            <li>Fancybox upgraded to Colorbox</li>
            <li>Implement dropdown menu, rewritten MenuRendor, added possibility to attach modules to other modules</li>
            <li>Site theme moved from raw CSS to Compass/SASS</li>
            <li>Add possibility to define dashboard actions via ModuleDescriptor</li>
            <li>Improved module generation</li>
            <li>Brand new CMS installer with possibility to import Symfony database settings</li>
            <li>File manager minor improvements</li>
            <li>Improved UI of ACL management, groups management and system logs</li>
            <li>More restrictive CAS Auth driver</li>
            <li>Improved Generic_model</li>
            <li>Reviewed and improved users module, added possibility to check CAS user status directly from admin panel</li>
            <li>Added html_customization module that allows injecting code into HTML template of the administration panel</li>
            <li>Added email_html helper</li>
            <li>Modified DisplayPage public controller, added posibility to define <i>mainpage_module</i> and <i>mainpage_module_method</i> for handeling mainpage request by the specified module</li>
            <li>Module generator now generates improved filters for date fields</li>
            <li>CMS modules groups and users now renamed to cms_groups and cms_users</li>
            <li>Improved Google chart generator</li>
            <li>DataGrid got new method getAdvancedFeed() that acts as a proxy to feed object.</li>
            <li>Upgraded SQL console module, fixed UTF-8 encoding in data view</li>
            <li>Improved import procedure in AdminCRUDController</li>
            <li>Fixed upload for CSV files</li>
            <li>Improved Spreadsheet to detect tabs as CSV separators</li>
            <li>Added Symfony2 bridge module (symfony2_bridge) allowing PepisCMS to use logic embedded in Symfony application</li>
            <li>Added Symfony2 log preview utility</li>
            <li>Implemented and validated all filters in Array_model</li>
            <li>Module generator can now resolve many-to-many relationships</li>
            <li>Implemented Upgradedb utility available at admin/upgradedb URL</li>
            <li>Introduced content journaling</li>
        </ul>
        <h2>0.2.2.13-LTS (14.05.2014)</h2>
        <ul>
            <li>Improved date and timestamp validation</li>
            <li>Improved PHP5 compatibility</li>
        </ul>
        <h2>0.2.2.12-LTS (10.04.2014)</h2>
        <ul>
            <li>Improved method Generic_model::applyFilters for string EQ filters</li>
        </ul>
        <h2>0.2.2.11-LTS (18.03.2014)</h2>
        <ul>
            <li>Upgraded SQL console module, fixed UTF-8 encoding in data view</li>
        </ul>
        <h2>0.2.2.10-LTS (27.02.2014)</h2>
        <ul>
            <li>Reviewed and improved users module, added possibility to check CAS user status directly from admin panel</li>
            <li>Minor fix in CAS driver, displaying correct error message for cases when user account is locally locked</li>
        </ul>
        <h2>0.2.2.9-LTS (03.02.2014)</h2>
        <ul>
            <li>Improved workaround in Uploads library to accept PDF files uploaded with wrong or non standard mime-type</li>
        </ul>
        <h2>0.2.2.8-LTS (20.01.2014)</h2>
        <ul>
            <li>Implemented workaround in Uploads library to accept ZIP/PDF files uploaded with wrong mime-type (as binary data)</li>
            <li>Updated mime config</li>
            <li>Minor changes in AdminCRUDController template</li>
        </ul>
         <h2>0.2.2.7 (07.01.2014)</h2>
        <ul>
            <li>Spreadsheet library method parseCSV now uses native mechanism for reading CSV files</li>
            <li>Improvements to CLI</li>
            <li>Improved widget compatibility</li>
            <li>Fixed an error that was introduced during query builder usage refractoring some minor versions ago, in User_model and Menu_model</li>
            <li>Added Array_model that allows building abstract models for CRUD from any source (XML, CSV, web services)</li>
            <li>Added BasicDataFeedableInterface and Moveable interfaces</li>
        </ul>
        <h2>0.2.2.6 (01.12.2013)</h2>
        <ul>
            <li>IMPORTANT: CBACL will now throw an error and block access when project ID is set but no API KEY is present</li>
            <li>Localized CAS driver error messages</li>
            <li>Code inspection and reformat</li>
            <li>Added Composer package description</li>
            <li>FormBuilder foreign key fix in MANY_TO_MANY relationship (for entries having no ID)</li>
        </ul>
        <h2>0.2.2.5 (11.11.2013)</h2>
        <ul>
            <li>Improved CRUD and controller templates</li>
            <li>Added CRUD import and export features</li>
            <li>Fixed a typo in Spreadsheet library</li>
            <li>User module now is got the possibility to set user password</li>
            <li>Improved form builder - select fields now can have null values</li>
            <li>Generic_model now is got "in" filter type, implemented MANY_TO_MANY filter in Datagrid table and filters</li>
            <li>Module generator now generates labels and filters for boolean elements</li>
            <li>Added YouTube helper</li>
            <li>Added home button to main menu, improved MenuRendor, improved default dashboard</li>
            <li>Added new validation rules: no_uppercase and no_lowercase</li>
            <li>Improved translator interface</li>
            <li>Fixed CAS logout</li>
            <li>EmailSender is got the possibility to overwrite the default config</li>
            <li>Addedd possibility to define FormBuilder field's options</li>
            <li>Updated CKE to the latest version 3.6.6.1</li>
        </ul>
        <h2>0.2.2.4 (15.08.2013)</h2>
        <ul>
            <li>Improved controller templates</li>
            <li>Added spreadsheet tests, fixed Spreadsheet library</li>
            <li>Added logs performance tests</li>
            <li>System backup now sends correct headers for SQL file</li>
            <li>Improved string_helper:niceuri()</li>
            <li>Added dmesg module</li>
            <li>Interface JavaScript refractored</li>
            <li>Remote models retested and improved</li>
            <li>Changed order of instructions in Generic_model::getAdvancedFeed()</li>
            <li>CAS upgraded to 1.3.2+</li>
            <li>Improved translator module - now all labels are automatically refreshed after save</li>
            <li>Extended and improved module generator</li>
        </ul>
        <h2>0.2.2.3 (20.05.2013)</h2>
        <ul>
            <li>Improved documentation and controller templates</li>
            <li>DataGrid minor change is setFilterValue</li>
            <li>Generic_model::getAdvancedFeed() now applies filter field mapings to order_by field</li>
            <li>Minor changes and code cleanup in TableUtility</li>
            <li>Minor changes in Spreadsheet library</li>
            <li>Cleanups and minor UI fixes</li>
        </ul>
        <h2>0.2.2.2 (07.03.2013)</h2>
        <ul>
            <li>Added new configuration options to Auth drivers: allowed_domains, allowed_usernames</li>
            <li>Added Gmail Auth driver</li>
            <li>Pages module bugfix</li>
            <li>Minor change to FormBuilder: if saveById returns an integer then it is considered the instance ID, otherwise the $this->db->insert_id() is called</li>
            <li>RTFEditor improvement - now it is possible to specify editor_styles_set_file in theme descriptor</li>
            <li>Removed URI components</li>
        </ul>
        <h2>0.2.2.1 (31.01.2013)</h2>
        <ul>
            <li>Implemented import of CodeIgniter logs</li>
            <li>Improved Auth drivers, updated CBACL gateway</li>
            <li>Upgraded jQuery UI to the latest version, added time picker to FormBuilder</li>
            <li>Implemented development_tools/switch_user allowing root users to switch accounts</li>
            <li>Extended configuration tests</li>
            <li>Minor fixes</li>
        </ul>
        <h2>0.2.2.0 (26.12.2012)</h2>
        <p>A mature version of 0.2.1</p>
        <ul>
            <li>System setup UI fix</li>
            <li>Auth $session_variable_preffix changed from pepis_cms to pepiscms</li>
            <li>Added auth drivers</li>
            <li>Auth::renewUserData is now private, use forceLogin or refreshSession instead</li>
            <li>Added possibility to map database table names</li>
            <li>Generic model got the possibility to change database on fly</li>
            <li>Added CRUD module generator</li>
            <li>Added cache revalidation when installing/changing module - this makes all module changes directly visible</li>
            <li>Fixed HTTP cache for thumbnails displayed in administration panel - improved page load speed</li>
            <li>Extended FormBuilder image upload callback</li>
            <li>Improved system translations</li>
            <li>System logs became a separate module and now got some analytical features for finding related accounts and user/IP statistics</li>
            <li>Simplified module installation, minor database change</li>
            <li>Improved grid move procedure</li>
            <li>Tested CLI support, now run any public controller: php index.php /controller_name/method_name/extra_component</li>
        </ul>
        <h2>0.2.1.2 (26.07.2012)</h2>
        <ul>
            <li>Maintenance update</li>
            <li>Fixed bug that always set English as default language</li>
            <li>Datagrid filters autosubmit has been disabled</li>
            <li>Improved Spreadsheet library</li>
        </ul>
        <h2>0.2.1.1 (26.06.2012)</h2>
        <ul>
            <li>Added timezone setting to configuration. IMPORTANT: UTC is no longer the default timezone! When upgrading please recompile the _pepiscms.php file.</li>
            <li>Interface fixes - improved popup animations, improved form SELECT styles, some more minor changes</li>
            <li>Improved datagrid pagination</li>
            <li>Improved Spreadsheet library</li>
        </ul>
        <h2>0.2.1.0 (18.06.2012)</h2>
        <ul>
            <li>Model loader can now load models from INSTALLATIONPATH/application/models</li>
            <li>Improved language loads for different locations</li>
            <li>User management as a separate module</li>
            <li>New Spreadsheet library for generating and parsing CVS and Excel files, generating XML files</li>
            <li>Form validation is got new methods for validating IMEI, bank account, SWIFT, PESEL and some more.</li>
            <li>Minor UI fixes</li>
            <li>Removed deprecated Validation and CacheControll</li>
            <li>Generic_model is now able to map filtered fields (to be used in case of &quot;ambiguous&quot; error).</li>
            <li>Added possibility to specify user login (optional). WARNING: User_model methods (register, update) are now changed!</li>
            <li>Added configuration check after user login</li>
        </ul>
        <h2>0.2.0.0 (11.01.2012)</h2>
        <ul>
            <li>Brand new user interface</li>
            <li>Improved database schema (partially backward compatible)</li>
            <li>Improved security, user session expires after one hour of inactivity, the user is now forced to use strong passwords, passwords expire in a given period, user account is being lock after a number of unsuccessful authorization attempts</li>
            <li>Improved data grid filters and forms</li>
            <li>Introduced module descriptors, including installation/uninstallation procedures</li>
            <li>Upgraded CKE Editor to the latest version</li>
            <li>Added CRUD controller for building CRUD modules with ease</li>
            <li>Added helpers and libraries for generating PDF/Excel files</li>
        </ul>
        <h2>0.1.5.0 (12.08.2011)</h2>
        <ul>
            <li>New api V5 compatible with CodeIgniter 2.0</li>
            <li>Introduced XML-RPC webservices</li>
            <li>Added SQL dump utility</li>
            <li>Improved user interface, icons and layout facelift</li>
            <li>SQL console and Translator now as builtin modules</li>
            <li>New siteconfig utility</li>
            <li>Rewritten page management</li>
            <li>Dynamic base_url</li>
            <li>Added URL helper functions</li>
            <li>Improved security and Auth component</li>
            <li>Compacted</li>
            <li>Improved upgrade utility</li>
        </ul>
        <h2>0.1.4.15 (02.05.2011)</h2>
        <ul>
            <li>Maintenance update</li>
            <li>Added compatibility functions that let modules written for API V5 to be run on 0.1.4</li>
            <li>Minor UI fixes</li>
            <li>Extended pagination class</li>
        </ul>
        <h2>0.1.4.13 (15.04.2011)</h2>
        <ul>
            <li>Fixed load language procedure to detect user language</li>
            <li>FormRenderable gets a new method for overloading the default error formatting delimiters</li>
            <li>Added new validation methods to form_validation: min, max, even, odd</li>
            <li>Added translation service for PHPTAL that takes translations from PepisCMS config files</li>
            <li>Lang::load now detects language both for front-end and backend</li>
            <li>Upgraded CKEditor to 3.5.3, FCK to 2.6.6, Tiny MCE 3.4.2, Fancybox 1.3.4, jQuery to 1.4.4</li>
            <li>DisplayPage dispatcher now uses object cache for retrieving information about the current page and site language, partial support for CMSPage also</li>
            <li>New installation script</li>
            <li>Module configuration gets a new config variable type - numeric. Numeric variables automatically transform "," into "."</li>
            <li>Added new APC page cache</li>
        </ul>

        <h2>0.1.4.11 (01.03.2011)</h2>
        <ul>
            <li>Added upload allowed types wildcard</li>
            <li>Added Dispatcher::getUriPrefix() and Dispatcher::getSiteLanguage() methods</li>
        </ul>

        <h2>0.1.4.8 (15.02.2011)</h2>
        <ul>
            <li>Added support for PHPTAL via Template class</li>
            <li>EnhancedController::getParam is now alias to URI::getParam</li>
            <li>URI::shift is implemented</li>
            <li>Updated jQuery UI Datepicker and it's style</li>
            <li>DataGrid and FormBuilder support now definitions and foreign keys</li>
            <li>Added &quot;apply&quot; button to FormBuilder. Note, if you use SimpleSessionMessage, the message must be read/assigned after the form is generated!</li>
            <li>Improved ModuleRunner debug</li>
            <li>Added complete Russian translation</li>
            <li>AdminModuleController::display now supports absolute file paths</li>
            <li>Improved installer</li>
            <li>Removed deprecated External Auth</li>
        </ul>

        <h2>0.1.4.2 (31.01.2011)</h2>
        <ul>
            <li>EnhancedController methods getControllerName() and getMethodName() implemented</li>
            <li>Extended and fixed system info</li>
            <li>Better support for Cyrillic alphabet, automatic generation of latin URLs using niceuri</li>
            <li>Backup restore has been fixed</li>
            <li>Backup restore now takes a backup of existing contents before making any changes, backup files can be found in application/backup/</li>
        </ul>
        <h2>0.1.4.1 (08.01.2011)</h2>
        <ul>
            <li>Maintenance update</li>
        </ul>
        <h2>0.1.4.0 (30.12.2010)</h2>
        <p>It took 7 months and 26 betas to release this version :)</p>
        <ul>
            <li>New authorization mechanism, now a user can belong to several groups, every single group is a collection of rights above certain entities</li>
            <li>Reorganized structure of AdminController constructor</li>
            <li>Improved file manager interface and implemented protected file access for intranet instances</li>
            <li>Extended upload allowed file extensions list and updated MIME types for zip files</li>
            <li>Module registration introduced. Every single module must be enabled by system administrator in order to be run. Module configuration files are now storied in application/config/modules/ folder (however, old method might work as well).</li>
            <li>System logs along with notifications for critical errors implemented</li>
            <li>DataGrid component now got the possibility od displaying user configurable filers and default data feed model for simple tables</li>
            <li>DataGrid default feed fix (posibility to apply = instead of LIKE)</li>
            <li>Database changes: all the database tables' engine is changed to MYISAM, from now on all the timestamps are storied using UTC timestamp</li>
            <li>Simplified database structure, removed view menu_view</li>
            <li>Dropped support for MySQL 4</li>
            <li>System information summary page added</li>
            <li>New more efficient HTML cache mechanism, the cache system is now initialized before CodeIgniter framework and requires less resources to run</li>
            <li>Added SMTP configuration and EmailSender library</li>
            <li>CKE Editor upgraded to version 3.5</li>
            <li>Fixed admin menu highlights</li>
            <li>Add possibility to add branding to the top of CMS page</li>
            <li>Modified Loader::config behavior. Now it is simpler to extend configuration as default config values are read from the library path</li>
            <li>Rewritten system Loader</li>
            <li>NOTE: All PHP timestamps are now in UTC (time() now returns UTC value), please use UTC_TIMESTAMP() for MySQL
            <li>NOTE: Please change $config['uri_protocol']	= "AUTO"; to $config['uri_protocol'] = "QUERY_STRING"; in your config file</li>
            <li>NOTE: $config['permitted_uri_chars'] must be changed to 'a-z 0-9~%.:_&=-', otherwise you can get "The URI you submitted has disallowed characters" error.</li>
            <li>NOTE: ModuleController getSitemapLinks() is now called in static manner</li>
            <li>NOTE: use $this->input->post('query') instead of $_POST</li>
            <li>NOTE: EnhancedController:getValue, PluginPage::getValue, PluginPage::assign, PluginPage::getValue now deprecated</li>
        </ul>
        <h2>0.1.3.13 (03.06.2010)</h2>
        <ul>
            <li>Login controller error causing wrong redirect in systems having dynamic base_url fixed.</li>
        </ul>
        <h2>0.1.3.12 (21.05.2010)</h2>
        <ul>
            <li>Updated niceuri plugin</li>
        </ul>
        <h2>0.1.3.11 (01.04.2010)</h2>
        <ul>
            <li>Maintenance update</li>
        </ul>
        <h2>0.1.3.10 (24.03.2010)</h2>
        <ul>
            <li>Now the system supports user defined config files for hooks and autoload</li>
            <li>Modified Hooks library to load user defined hooks</li>
            <li>Modified content models, now the database supports to host content for several web sites sharing the user tables</li>
        </ul>
        <h2>0.1.3.9 (24.02.2010)</h2>
        <ul>
            <li>Maintenance update</li>
        </ul>

        <h2>0.1.3.8 (23.01.2010)</h2>
        <ul>
            <li>New ajax based file manager</li>
            <li>Improved user interface, added search box and simple view for pages, new page edit view</li>
            <li>Basic configuration tests implemented (displayed in utilities and settings)</li>
            <li>Module runner improvements</li>
            <li>CKEditor and FCK upgraded</li>
        </ul>
    </div>
</div>