<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Whether the system log should report E_USER_DEPRECATED
 */
$config['debug_log_php_deprecated'] = FALSE;

/**
 * Whether the system log should report E_USER_WARNING and E_WARNING
 */
$config['debug_log_php_warning'] = FALSE;

/**
 * Whether the system log should report E_USER_ERROR
 */
$config['debug_log_php_error'] = FALSE;

/**
 * Email address of system maintainer/administrator
 * If different from FALSE, the system will try to deliver error reports to this
 * address
 *
 * You can specify several adresses, separate them by spaces
 */
$config['debug_maintainer_email_address'] = FALSE; // Replace it with 'developer@yourdomain.com'