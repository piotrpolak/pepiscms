<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database driver. e.g.: mysqli.
|			Currently supported:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Query Builder class
|	['pconnect'] true/false - Whether to use a persistent connection
|	['db_debug'] true/false - Whether database errors should be displayed.
|	['cache_on'] true/false - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['encrypt']  Whether or not to use an encrypted connection.
|	['compress'] Whether or not to use client compression (MySQL only)
|	['stricton'] true/false - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|	['failover'] array - A array with 0 or more data for connections if the main should fail.
|	['save_queries'] true/false - Whether to "save" all executed queries.
| 				NOTE: Disabling this will also effectively disable both
| 				$this->db->last_query() and profiling of DB queries.
| 				When you run a query, with this setting set to true (default),
| 				CodeIgniter will store the SQL statement for debugging purposes.
| 				However, this may cause high memory usage, especially if you run
| 				a lot of SQL queries ... disable this to avoid that problem.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $query_builder variables lets you determine whether or not to load
| the query builder class.
*/

$active_group = 'default';
$query_builder = true;


if (!function_exists('__pepiscms_database_safefallback')) {
    function __pepiscms_database_safefallback($env_variable_name, $default = "")
    {
        $env_value = getenv($env_variable_name);
        if (!empty($env_value)) {
            return $env_value;
        }

        return $default;
    }
}

$db['default'] = array(
    'dsn'           => '',
    'hostname'      => __pepiscms_database_safefallback('PEPIS_CMS_DATABASE_HOSTNAME', 'TEMPLATE_DB_HOST'),
    'username'      => __pepiscms_database_safefallback('PEPIS_CMS_DATABASE_USERNAME', 'TEMPLATE_DB_USERNAME'),
    'password'      => __pepiscms_database_safefallback('PEPIS_CMS_DATABASE_PASSWORD', 'TEMPLATE_DB_PASSWORD'),
    'database'      => __pepiscms_database_safefallback('PEPIS_CMS_DATABASE_DATABASE', 'TEMPLATE_DB_DATABASE'),
    'dbdriver'      => 'TEMPLATE_DB_DRIVER',
    'port'          => TEMPLATE_DB_PORT,
    'dbprefix'      => '',
    'pconnect'      => false,
    'db_debug'      => (ENVIRONMENT != 'production'),
    'cache_on'      => false,
    'cachedir'      => '',
    'char_set'      => 'utf8',
    'dbcollat'      => 'utf8_general_ci',
    'swap_pre'      => '',
    'encrypt'       => false,
    'compress'      => false,
    'stricton'      => false,
    'failover'      => array(),
    'save_queries'  => true
);
