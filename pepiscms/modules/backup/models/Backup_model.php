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
 * Class Backup_model
 */
class Backup_model extends Generic_model
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable('backup');
        $this->setIdFieldName('id');

        // Required by saveById method
        $this->setAcceptedPostFields(array());
    }

}