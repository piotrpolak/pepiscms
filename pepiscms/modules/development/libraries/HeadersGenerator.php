<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * An utility for generating file headers.
 *
 * @since 1.0.0
 */
class HeadersGenerator
{
    public function generate()
    {
        $instance_map = array('loader' => 'load');

        $core_components = $this->fetchCoreComponents(array(
            'codeigniter', 'common', 'controller', 'driver', 'exceptions', 'hooks', 'log', 'model', 'router', 'utf8'
        ));
        $model_components = $this->fetchModelComponents();
        $pepiscms_components = $this->fetchPepisCMSComponents();

        // The order matters, $pepiscms_components should be before $core_components
        $all_components = $model_components + $pepiscms_components + $core_components;


        $output = "<?php die('This is automatically generated file and should not be runned nor included');\n/**\n";
        $output .= " * @property CI_DB_query_builder \$db\n";

        foreach ($all_components as $key => $class_name) {
            if (isset($instance_map[$key])) {
                $instance_name = $instance_map[$key];
            } else {
                $instance_name = $key;
            }

            $output .= " * @property " . ($class_name) . ' $' . ($instance_name) . "\n";
        }

        $output .= " */\n";
        $output .= "class CI_Controller {\n\n";
        $output .= "    /**\n";
        $output .= "     * @return CI_Controller\n";
        $output .= "     */\n";
        $output .= "    public static function get_instance() {}\n";
        $output .= "}\n";
        $output .= "class CI_Model extends CI_Controller {}\n";
        $output .= "class ContainerAware extends CI_Controller {}\n";

        $output .= "/**\n";
        $output .= "* @return CI_Controller\n";
        $output .= "*/\n";
        $output .= "function get_instance() {}\n";

        return $output;
    }

    /**
     * @return array
     */
    private function fetchCoreComponents($excuded_instance_names)
    {
        $components = array();
        $core_libraries_paths = '{' . BASEPATH . 'libraries/*.php,' . BASEPATH . 'core/*.php}'; // Files that should be prefixed with CI_
        $core_library_files = glob($core_libraries_paths, GLOB_BRACE);
        foreach ($core_library_files as $path) {
            $name = pathinfo($path);
            $instance_name = strtolower($name['filename']);
            if (in_array($instance_name, $excuded_instance_names)) {
                continue;
            }
            $components[$instance_name] = 'CI_' . $name['filename'];
        }
        return $components;
    }

    /**
     * @return array
     */
    private function fetchModelComponents()
    {
        $components = array();
        $core_libraries_paths = '{' . APPPATH . 'models/*.php,modules/*/models/*.php,application/models/*.php}';
        $core_library_files = glob($core_libraries_paths, GLOB_BRACE);
        foreach ($core_library_files as $path) {
            $name = pathinfo($path);
            $instance_name = $name['filename'];
            $components[$instance_name] = $name['filename'];
        }
        return $components;
    }


    /**
     * @return array
     */
    private function fetchPepisCMSComponents()
    {
        $components = array();
        $core_libraries_paths = '{' . APPPATH . 'libraries/*.php,' . APPPATH . 'core/*.php,modules/*/libraries/*.php,application/libraries/*.php}';
        $core_library_files = glob($core_libraries_paths, GLOB_BRACE);
        foreach ($core_library_files as $path) {
            $name = pathinfo($path);
            $instance_name = strtolower(str_replace('PEPISCMS_', '', $name['filename']));
            $components[$instance_name] = $name['filename'];
        }
        return $components;
    }
}
