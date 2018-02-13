<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * Floating renderer for FormBuilder
 *
 * Floating form renders inputs in the same row.
 * @since 0.1.5
 */
class FloatingFormRenderer extends DefaultFormRendererInterface
{

    public function __construct($template_absolute_path = FALSE)
    {
        parent::__construct(APPPATH . 'views/templates/formbuilder_floating.php');
    }
}
