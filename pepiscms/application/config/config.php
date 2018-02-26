<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once BASEPATH . '../application/config/config.php';

$config['uri_protocol'] = 'QUERY_STRING';
$config['url_suffix'] = ".html";
$config['enable_hooks'] = true;
$config['subclass_prefix'] = 'MY_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_&=@-';
$config['log_path'] = INSTALLATIONPATH . 'application/logs/';
$config['encryption_key'] = md5(INSTALLATIONPATH); // Useful if you have multiple instances on the same server
$config['composer_autoload'] = VENDOR_PATH . 'autoload.php';
$config['subclass_prefix'] = 'PEPISCMS_';

// PepisCMS Specific

/*
|--------------------------------------------------------------------------
| PepisCMS available languages list
|--------------------------------------------------------------------------
*/
$config['languages'] = array(
    'english' => array('english', 'English', 'en.png', 'en'),
    'polish' => array('polish', 'Polski', 'pl.png', 'pl'),
);
// spanish and romanian disabled, low quality translations

/*
|--------------------------------------------------------------------------
| PepisCMS enabled languages
|--------------------------------------------------------------------------
*/
$config['enabled_languages'] = array('english', 'polish');


/*
|--------------------------------------------------------------------------
| Uploads Path
|--------------------------------------------------------------------------
*/
$config['uploads_path'] = 'uploads/';


/*
|--------------------------------------------------------------------------
| Theme Path
|--------------------------------------------------------------------------
|
| Default theme path
|
*/
$config['theme_path'] = 'theme/';


/*
|--------------------------------------------------------------------------
| Upload allowed file extensions
|--------------------------------------------------------------------------
*/
$config['upload_allowed_types'] = 'mov|torrent|dwg|dxf|pln|psd|ai|svg|wks|mdb|sql|accdb|drw|eps|ps|log|msg|wpd|wps|efx|gif|bmp|tiff|vcf|tif|jpg|png|zip|zipx|7z|rar|doc|docx|xls|xlsx|cvs|pdf|txt|html|htm|mp3|wav|avi|html|htm|rtf|swf|css|xml|ppt|pps|pptx';


/*
|--------------------------------------------------------------------------
| Module URI separator
|--------------------------------------------------------------------------
|
| modulename-method-param1-param2.html
| This setting will be replaced by slash in future versions
|
*/
$config['module_uri_separator'] = '/';


/*
|--------------------------------------------------------------------------
| Is profiler enabled
|--------------------------------------------------------------------------
|
| Profiler helps you to determine bottlenecks of your application
|
*/
$config['enable_profiler'] = false;

/*
|--------------------------------------------------------------------------
| Maximum password age
|--------------------------------------------------------------------------
|
| Maximum password age in seconds, default 0
|
*/
$config['security_maximum_password_age_in_seconds'] = 0;

/*
|--------------------------------------------------------------------------
| Number of consecutive unsuccessfull authorizations to lock account
|--------------------------------------------------------------------------
|
| If somebody tries to login using your email, the account will be locked after
| some number of attempts. 0 meaning this option is disabled.
|
*/
$config['security_number_of_unsuccessfull_authorizations_to_lock_account'] = 0;

/*
|--------------------------------------------------------------------------
| Minimum allowed password strength
|--------------------------------------------------------------------------
| Combined along with security_minimum_allowed_password_length
|
| 0 Blank
| 1 Normal
| 2 Medium
| 3 Strong
|
*/
$config['security_minimum_allowed_password_strength'] = 0;

/*
|--------------------------------------------------------------------------
| Minimum allowed password length
|--------------------------------------------------------------------------
|
| 4 is the minimum, for enterprise systems 12 is reccommended
|
*/
$config['security_minimum_allowed_password_length'] = 4;

/*
|--------------------------------------------------------------------------
| User session must match the original IP
|--------------------------------------------------------------------------
|
| When set to true, the user session will be terminated if the IP does not match
| Warning: when using mobile internet it is common that the IP of the client varies!
|
*/
$config['security_session_must_match_ip'] = false;


/*
|--------------------------------------------------------------------------
| Main page module
|--------------------------------------------------------------------------
|
| Specifies module to be served as mainpage
|
*/
$config['mainpage_module'] = false;

/*
|--------------------------------------------------------------------------
| Main page module method
|--------------------------------------------------------------------------
|
| Specifies module method to be served as mainpage
|
*/
$config['mainpage_module_method'] = false;


// PepisCMS bypass
if (!file_exists(INSTALLATIONPATH . 'application/config/config.php')) {
    if (!is_cli() && strpos($_SERVER['REQUEST_URI'], '/installer/') === false) {
        header('Location: ./installer/');
        die();
    }
} else {
    require_once INSTALLATIONPATH . 'application/config/config.php';
}
