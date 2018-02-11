<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

/**
 * Enhanced loader
 */
class PEPISCMS_Loader extends CI_Loader
{
    // Cache
    private $module_directory_cache = array();

    /**
     * Database Loader
     *
     * @param string $params
     * @param bool $return
     * @param null $active_record
     * @return object
     */
    public function database($params = '', $return = FALSE, $active_record = NULL)
    {
        // load our version of the CI_DB_Cache class. The database library checks
        // if this class is already loaded before instantiating it. Loading it now
        // makes sure our version is used when a controller enables query caching
        if (!class_exists('CI_DB_Cache')) {
            @include(APPPATH . 'core/MY_DB_Cache.php');
        }

        // Grab the super object
        $CI = &get_instance();

        // Do we even need to load the database class?
        if (class_exists('CI_DB') AND $return == FALSE AND $active_record == NULL AND isset($CI->db) AND is_object($CI->db)) {
            return FALSE;
        }

        require_once(BASEPATH . 'database/DB.php');

        if ($return === TRUE) {
            return DB($params, $active_record);
        }

        // Initialize the db variable.  Needed to prevent
        // reference errors with some configurations
        $CI->db = '';

        // Load the DB class
        $CI->db = &DB($params, $active_record);
    }

    /**
     * Loads a theme, a function complementary to view
     *
     * @param string $path
     * @param array $vars
     * @param boolean $return
     * @return object
     */
    public function theme($path, $vars = array(), $return = FALSE)
    {
        return $this->_ci_load(array(
                '_ci_path' => $path,
                '_ci_vars' => $this->_ci_object_to_array($vars),
                '_ci_return' => $return)
        );
    }

    /**
     * Model Loader
     *
     * This function lets users load and instantiate models.
     *
     * @param    string    the name of the class
     * @param    string    name for the model
     * @param    bool    database connection
     * @param    bool $hardfail
     * @return    void
     */
    public function model($model, $name = '', $db_conn = FALSE, $hardfail = TRUE)
    {
        // Keep this redundancy
        $original_model = $model;
        $original_name = $name;
        $original_db_conn = $db_conn;

        $CI = &get_instance();
        if (isset($CI->modulerunner) && $CI->modulerunner->getRunningModuleName()) {

            if (is_array($model)) {
                foreach ($model as $babe) {
                    $this->model($babe);
                }
                return;
            }

            if ($model == '') {
                return;
            }

            // Is the model in a sub-folder? If so, parse out the filename and path.
            if (strpos($model, '/') === FALSE) {
                $path = '';
            } else {
                $x = explode('/', $model);
                $model = end($x);
                unset($x[count($x) - 1]);
                $path = implode('/', $x) . '/';
            }

            if ($name == '') {
                $name = $model;
            }

            if (in_array($name, $this->_ci_models, TRUE)) {
                return;
            }

            $CI = &get_instance();
            if (isset($CI->$name)) {
                show_error('The model name you are loading is the name of a resource that is already being used: ' . $name);
            }

            $model = strtolower($model);

            $model_directory = FALSE;
            $model_directories = array(
                $this->resolveModuleDirectory($CI->modulerunner->getRunningModuleName()) . 'models/',
                INSTALLATIONPATH . 'application/models/'
            );

            foreach ($model_directories as $m_d) {
                if (file_exists($m_d . $path . $model . '.php')) {
                    $model_directory = $m_d;
                }
            }

            if ($model_directory) {
                if ($db_conn !== FALSE AND !class_exists('CI_DB')) {
                    if ($db_conn === TRUE)
                        $db_conn = '';

                    $CI->load->database($db_conn, FALSE, TRUE);
                }

                if (!class_exists('Model')) {
                    load_class('Model', FALSE);
                }

                require_once($model_directory . $path . $model . '.php');

                $model = ucfirst($model);

                $CI->$name = new $model();

                $this->_ci_models[] = $name;

                return;
            }
        }


        //parent::model( $original_model, $original_name, $original_db_conn );
        $this->_model($original_model, $original_name, $original_db_conn, $hardfail);
    }

    /**
     * Original loader method with hardfail added
     *
     * @param $model
     * @param string $name
     * @param bool $db_conn
     * @param bool $hardfail
     */
    private function _model($model, $name = '', $db_conn = FALSE, $hardfail = TRUE)
    {
        if (is_array($model)) {
            foreach ($model as $babe) {
                $this->model($babe);
            }
            return;
        }

        if ($model == '') {
            return;
        }

        $path = '';

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($model, '/')) !== FALSE) {
            // The path is in front of the last slash
            $path = substr($model, 0, $last_slash + 1);

            // And the model name behind it
            $model = substr($model, $last_slash + 1);
        }

        if ($name == '') {
            $name = $model;
        }

        if (in_array($name, $this->_ci_models, TRUE)) {
            return;
        }

        $CI = &get_instance();
        if (isset($CI->$name)) {
            show_error('The model name you are loading is the name of a resource that is already being used: ' . $name);
        }

        //$model = strtolower($model);

        foreach ($this->_ci_model_paths as $mod_path) {
            if (!file_exists($mod_path . 'models/' . $path . $model . '.php')) {
                continue;
            }

            if ($db_conn !== FALSE AND !class_exists('CI_DB')) {
                if ($db_conn === TRUE) {
                    $db_conn = '';
                }

                $CI->load->database($db_conn, FALSE, TRUE);
            }

            if (!class_exists('CI_Model')) {
                load_class('Model', 'core');
            }

            require_once($mod_path . 'models/' . $path . $model . '.php');

            $model = ucfirst($model);

            $CI->$name = new $model();

            $this->_ci_models[] = $name;
            return;
        }

        if ($hardfail) {
            // couldn't find the model
            show_error('Unable to locate the model you have specified: ' . $model);
        }
    }

    /**
     * Loads config of the specified module. If the module param is not specified, it will load the config of the current module
     *
     * @param string $module_name
     */
    public function moduleConfig($module_name)
    {
        $CI = &get_instance();
        $CI->config->loadModuleConfig($module_name);
    }

    /**
     * Load module language.
     *
     * @param string $langfile
     * @param string|bool $idiom
     * @return bool
     */
    public function language($langfile, $idiom = FALSE)
    {
        $CI = &get_instance();
        if (isset($CI->modulerunner) && ($module_name = $CI->modulerunner->getRunningModuleName())) {
            if ($CI->lang->loadForModule($langfile, $idiom, FALSE, $module_name)) {
                return TRUE;
            }
        }
        return $CI->lang->load($langfile, $idiom);
    }

    /**
     * Load module language
     *
     * @param string|bool $module_name
     * @param string|bool $langfile
     * @param string|bool $idiom
     * @return mixed
     */
    public function moduleLanguage($module_name = FALSE, $langfile = FALSE, $idiom = FALSE)
    {
        if (!$idiom) {
            if (class_exists('Dispatcher') && ($d_lang = Dispatcher::getSiteLanguage())) {
                $idiom = $d_lang->ci_language;
            }
        }

        $CI = &get_instance();

        if (!$module_name && isset($CI->modulerunner)) {
            $module_name = $CI->modulerunner->getRunningModuleName();
        }

        if (!$langfile) {
            $langfile = $module_name;
        }

        return $CI->lang->loadForModule($langfile, $idiom, FALSE, $module_name);
    }

    /**
     * Class Loader
     *
     * This function lets users load and instantiate classes.
     * It is designed to be called from a user's app controllers.
     *
     * @param string $library
     * @param null $params
     * @param null $object_name
     * @return bool|PEPISCMS_Loader|object
     */
    public function library($library = '', $params = NULL, $object_name = NULL)
    {
        if (empty($library)) {
            return $this;
        } elseif (is_array($library)) {
            foreach ($library as $key => $value) {
                if (is_int($key)) {
                    $this->library($value, $params);
                } else {
                    $this->library($key, $params, $value);
                }
            }

            return $this;
        }

        $CI = &get_instance();
        $isLoaded = FALSE;
        if (isset($CI->modulerunner) && $CI->modulerunner->getRunningModuleName()) {
            $isLoaded = $this->moduleLibrary($CI->modulerunner->getRunningModuleName(), $library, $params);
        }
        if ($isLoaded) {
            return TRUE;
        }
        return parent::library($library, $params, $object_name);
    }

    /**
     * Loads module library.
     *
     * @param string $module_name
     * @param string $library
     * @param mixed $params
     * @return bool|void
     */
    public function moduleLibrary($module_name, $library, $params = NULL)
    {
        if ($library == '') {
            return FALSE;
        }

        if (!is_null($params) AND !is_array($params)) {
            $params = NULL;
        }

        // Get the class name
        $class = str_replace('.php', '', trim($library, '/'));

        // We'll test for both lowercase and capitalized versions of the file name
        foreach (array(ucfirst($class), strtolower($class)) as $class) {
            // Lets search for the requested library file and load it.
            $is_duplicate = FALSE;
            $library_file_path = $this->resolveModuleDirectory($module_name) . 'libraries/' . $class . '.php';

            if (file_exists($library_file_path)) {
                // Safety:  Was the class already loaded by a previous call?
                if (in_array($library_file_path, $this->_ci_classes)) {
                    $is_duplicate = TRUE;
                    log_message('debug', $class . " class already loaded. Second attempt ignored.");
                    return;
                }

                include_once($library_file_path);
                $this->_ci_init_library($class, '', $params);
                return TRUE;
            }
        } // END FOREACH
        return FALSE;
    }

    /**
     * Loads module model.
     *
     * @param string $module_name
     * @param string $model
     * @param string $name
     * @param string|bool $db_conn
     */
    public function moduleModel($module_name, $model, $name = '', $db_conn = FALSE)
    {
        $CI = &get_instance();
        $current_module_name = FALSE;
        if (isset($CI->modulerunner)) {
            $current_module_name = $CI->modulerunner->getRunningModuleName();
        } else {
            $CI->load->library('ModuleRunner');
        }
        $CI->modulerunner->setRunningModuleName($module_name);
        $this->model($model, $name, $db_conn);
        $CI->modulerunner->setRunningModuleName($current_module_name);
    }

    /**
     * Tells whether the module is installed in user space
     * If it is in system space or does not exist the function will return FALSE
     *
     * @param string $module_name
     * @return bool
     */
    public function isModuleInUserSpace($module_name)
    {
        $user_module_directory = 'modules/';

        return file_exists($user_module_directory . $module_name);
    }

    /**
     * Resolves module path
     * Priority: user space, core space
     *
     * @param string $module_name
     * @param boolean $web_path
     *
     * @return string|bool
     */
    public function resolveModuleDirectory($module_name, $web_path = FALSE)
    {
        if (isset($this->module_directory_cache[$module_name][$web_path])) {
            // Some kind of runtime cache
            return $this->module_directory_cache[$module_name][$web_path];
        }

        $core_module_directory = APPPATH . '../modules/';
        $user_module_directory = 'modules/';

        if ($this->isModuleInUserSpace($module_name)) {
            $module_directory = $user_module_directory;
        } elseif (file_exists($core_module_directory . $module_name)) {
            if ($web_path) {
                $module_directory = 'pepiscms/modules/';
            } else {
                $module_directory = $core_module_directory;
            }
        } else {
            $this->module_directory_cache[$module_name][$web_path] = FALSE;
            return FALSE;
        }

        $this->module_directory_cache[$module_name][$web_path] = $module_directory . $module_name . '/';
        return $this->module_directory_cache[$module_name][$web_path];
    }
}
