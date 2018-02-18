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

namespace Piotrpolak\Pepiscms\Editor;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RTF Editor abstract class, base for any driver
 *
 * @since 0.1
 */
abstract class DefaultRTEditorDriver implements RTEditor
{
    protected $config = array();
    protected $is_full = FALSE;

    /**
     * @inheritdoc
     */
    public function setConfig($field_name, $value)
    {
        $this->config[$field_name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getConfig($field_name)
    {
        return $this->config[$field_name];
    }

    /**
     * @inheritdoc
     */
    public function getConfigArray()
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function setFull($is_full = TRUE)
    {
        $this->is_full = $is_full;
    }

    /**
     * @inheritdoc
     */
    public function isFull()
    {
        return $this->is_full;
    }

}
