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

namespace PiotrPolak\PepisCMS\Formbuilder\Component;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Image
 *
 * @since 1.0.0
 */
class Image extends File
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::IMAGE;
    }
}
