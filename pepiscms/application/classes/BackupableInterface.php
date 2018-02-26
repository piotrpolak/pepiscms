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
 * Backupable interface specifying methods required by the backup system.
 *
 * Any model that implements backup procedures should implement BackupableInterface
 *
 * @since 0.1.0
 */
interface BackupableInterface
{
    /**
     * Does the backup projection for backup export
     *
     * @return mixed
     */
    public function doBackupProjection();

    /**
     * Restores backup from parameters
     *
     * @param $parameters
     * @param null $user_id
     * @return mixed
     */
    public function doBackupRestore(&$parameters, $user_id = null);

    /**
     * Prepares the storage for the import
     *
     * @return mixed
     */
    public function doBackupPrepare();
}
