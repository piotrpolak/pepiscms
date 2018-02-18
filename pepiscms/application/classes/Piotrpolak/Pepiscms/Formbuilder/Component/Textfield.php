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
 * Textfield
 *
 * @since 1.0.0
 */
class Textfield extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::TEXTFIELD;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        $extra_attributes = '';
        if (preg_match("/(exact_length|max_length)\[(.*)\]/", $field['validation_rules'], $match)) {
            $extra_attributes = 'maxlength="' . $match[2] . '"';
        }

        return '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $valueEscaped .
            '" class="text' . $extra_css_classes . '" ' . $extra_attributes . ' />';
    }
}