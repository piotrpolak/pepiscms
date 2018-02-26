<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Whether the system log should report E_USER_DEPRECATED
 */
$config['debug_log_php_deprecated'] = false;

/**
 * Whether the system log should report E_USER_WARNING and E_WARNING
 */
$config['debug_log_php_warning'] = false;

/**
 * Whether the system log should report E_USER_ERROR
 */
$config['debug_log_php_error'] = false;

/**
 * Email address of system maintainer/administrator
 * If different from FALSE, the system will try to deliver error reports to this
 * address
 *
 * You can specify several adresses, separate them by spaces
 */
$config['debug_maintainer_email_address'] = false; // Replace it with 'developer@yourdomain.com'
