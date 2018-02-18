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
 * Textarea
 *
 * @since 1.0.0
 */
class Textarea extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::TEXTAREA;
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

        return '<textarea name="' . $field['field'] . '" id="' . $field['field'] . '" class="text' . $extra_css_classes .
            '" rows="8" ' . $extra_attributes . '>' . $valueEscaped . '</textarea>';
    }
}