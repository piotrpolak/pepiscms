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
 * Checkbox
 *
 * @since 1.0.0
 */
class Checkbox extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::CHECKBOX;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        /// $value takes default value or object value but not POST
        // $this->object->$field['field'] takes object from DB or post
        if (!$valueEscaped) {
            $valueEscaped = 1;
        }
        $is_checked = $valueEscaped && isset($object->$field['field']) ? $valueEscaped == $object->$field['field'] : $valueEscaped == $field['input_default_value'];
        return '<input type="checkbox" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $valueEscaped . '"' . ($is_checked ? ' checked="checked"' : '') . ' class="' . $extra_css_classes . '"/>';
    }
}