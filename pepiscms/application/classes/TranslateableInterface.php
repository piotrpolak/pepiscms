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
 * Translateable interface specifying methods used forms that save data on translateable entities.
 *
 * Used to generate multilanguage forms.
 *
 * @since 0.1.5
 */
interface TranslateableInterface
{
    /**
     * Returns an array containing translatable field names
     *
     * @return array
     */
    public function getTranslateableFieldNames();
}
