<?php //NO 'No direct script access allowed'


/*
 * When set to true, using Memcached
 */
$config['memcache_enabled'] = FALSE;


/*
 * Server host and port
 */
$config['memcache_server_host'] = 'localhost';
$config['memcache_server_port'] = 11211;


/*
 * Secret key used to create pseudo-namespaces
 */
$config['memcache_variable_prefix'] = 'TEMPLATE_SECRET_KEY';