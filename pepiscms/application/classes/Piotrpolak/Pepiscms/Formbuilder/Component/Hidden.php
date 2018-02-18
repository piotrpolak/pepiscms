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
 * Hidden
 *
 * @since 1.0.0
 */
class Hidden extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::HIDDEN;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($valueEscaped)) {
            // This only happens for multiple checkbox when the field is not editable
            foreach ($valueEscaped as $a_value) {
                $output_element .= '<input type="hidden" name="' . $field['field'] . '[]" id="' . $field['field'] . '_' .
                    str_replace('"', '', $a_value) . '" value="' . $a_value . '" />';
            }
        } else {
            $output_element = '<input type="hidden" name="' . $field['field'] . '" id="' . $field['field'] .
                '" value="' . $valueEscaped . '" />';
        }
        return $output_element;
    }
}