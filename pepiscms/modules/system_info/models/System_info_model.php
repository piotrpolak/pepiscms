<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

class System_info_model extends PEPISCMS_Model
{
    public function getFreeSpace()
    {
        return disk_free_space(INSTALLATIONPATH);
    }

    public function getOccupiedSpace($relative_dir = '')
    {
        $this->load->helper('file');
        $files = get_filenames(INSTALLATIONPATH . $relative_dir, TRUE);
        $filesize_all = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                $filesize_all += filesize($file);
            }
        }

        return $filesize_all;
    }
}