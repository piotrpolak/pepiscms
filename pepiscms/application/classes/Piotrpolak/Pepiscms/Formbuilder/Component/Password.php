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

namespace Piotrpolak\Pepiscms\Formbuilder\Component;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Password
 *
 * @since 1.0.0
 */
class Password extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::PASSWORD;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        return '<input type="password" name="' . $field['field'] . '" id="' . $field['field'] .
            '" value="" autocomplete="off" class="text' . $extra_css_classes . '" />';

    }
}