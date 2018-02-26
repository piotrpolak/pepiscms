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
 * Floating renderer for FormBuilder
 *
 * Floating form renders inputs in the same row.
 * @since 0.1.5
 */
class FloatingFormRenderer extends DefaultFormRenderer
{
    public function __construct($template_absolute_path = false)
    {
        parent::__construct(APPPATH . 'views/templates/formbuilder_floating.php');
    }
}
