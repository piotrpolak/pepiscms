<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* See core/pepiscms/application/config/config.php for values that can be overwritten */

/*
|--------------------------------------------------------------------------
| PepisCMS enabled languages
|--------------------------------------------------------------------------
*/
$config['enabled_languages'] = array('english', 'polish');

/*
|--------------------------------------------------------------------------
| Enables / disables object cache
|--------------------------------------------------------------------------
|
| Object cache significantly improves system performance but might cause
| problem in cloud environments.
|
*/
$config['cache_object_is_enabled'] = TEMPLATE_OBJECT_CACHE_OBJECT_IS_ENABLED;