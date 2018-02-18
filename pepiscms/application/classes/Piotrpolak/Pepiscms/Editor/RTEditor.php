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
 * RTF Editor driver interface
 *
 * @since 0.1.0
 */
interface RTEditor
{

    /**
     * Initializes default config
     *
     * @param $descriptor
     * @return bool
     */
    public function setupDefaultConfig($descriptor);

    /**
     * Generates RTF editor HTML
     *
     * @param string $text
     * @param int $height
     * @param string $instance
     * @return string
     */
    public function generate($text, $height, $instance);

    /**
     * Sets a config variable value
     *
     * @param $field_name
     * @param $value
     * @return mixed
     */
    public function setConfig($field_name, $value);

    /**
     * Returns a single config variable
     *
     * @param string $field_name
     * @return string
     */
    public function getConfig($field_name);

    /**
     * Returns full config array
     *
     * @return array
     */
    public function getConfigArray();

    /**
     * Sets editor into full mode
     *
     * @param bool $is_full
     */
    public function setFull($is_full = TRUE);

    /**
     * Returns whether the editor is in full mode
     *
     * @return bool
     */
    public function isFull();
}