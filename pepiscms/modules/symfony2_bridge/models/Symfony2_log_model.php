<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Symfony2_log_model
 */
class Symfony2_log_model extends Array_model
{
    /**
     * Base path to logs
     *
     * @var string
     */
    private $log_base_path = '';

    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCacheTtl(120);
    }

    /**
     * Sets log base path
     *
     * @param $log_base_path
     */
    public function setLogBasePath($log_base_path)
    {
        $this->log_base_path = $log_base_path;
    }


    /**
     * Returns log base path
     *
     * @return string
     */
    public function getLogBasePath()
    {
        return $this->log_base_path;
    }


    /**
     * Returns a raw array of objects
     *
     * @param mixed $extra_param
     * @return array a raw array of objects
     */
    public function getBasicFeed($extra_param)
    {
        $data = array();

        $pattern = '/\[(?P<datetime>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*[^ ]+) (?P<context>[^ ]+) (?P<extra>[^ ]+)/';

        $environments = array('prod', 'dev');

        $lines = array();
        foreach ($environments as $environment) {

            $log_file = $this->log_base_path . $environment . '.log';
            if (!file_exists($log_file)) {
                continue;
            }

            $lines = $this->tailAFile($log_file, 500);
            if (!$lines) continue;

            while ($line = array_shift($lines)) {
                $array = false;
                preg_match($pattern, $line, $array);

                // Protection against empty values
                if (!$array) continue;

                $obj = new stdClass();
                $obj->datetime = $array['datetime'];
                $obj->logger = $array['logger'];
                $obj->level = $array['level'];
                $obj->message = $array['message'];
                $obj->context = $array['context'];
                $obj->extra = $array['extra'];
                $obj->environment = $environment;
                $data[] = $obj;
            }
        }

        return $data;
    }


    /**
     * Return last rows of the file as an array
     *
     * @param $path
     * @param int $count
     * @return array
     */
    protected function tailAFile($path, $count = 10)
    {
        if (!file_exists($path)) {
            return FALSE;
        }

        $handle = fopen($path, "r");
        if (!$handle) {
            return FALSE;
        }

        $filesize = filesize($path);
        if (!$filesize) {
            return array();
        }
        $limit = -filesize($path) - 2;

        $linecounter = $count;
        $pos = -2;
        $beginning = false;
        $text = array();
        while ($pos > $limit) {
            $t = " ";
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$count - $linecounter - 1] = fgets($handle);
            if ($beginning || $linecounter < 1) {
                break;
            }
        }
        fclose($handle);
        return array_reverse($text);
    }
}