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
 * Selectbox
 *
 * @since 1.0.0
 */
class Selectbox extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::SELECTBOX;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($field['values'])) {
            //FIXME Set user defined default value in validation
            $output_element .= '<select name="' . $field['field'] . '" id="' . $field['field'] . '" class="text' . $extra_css_classes . '" >' . "\n";
            $was_selected = FALSE;
            $selected_code = '';
            foreach ($field['values'] as $key => $val) {
                if (!$was_selected && $valueEscaped == $key) // This is required to support null values, we can not use === operator neither to cast the values
                {
                    $was_selected = TRUE;
                    $selected_code = ' selected="selected"';
                }
                $output_element .= '<option value="' . $key . '" ' . $selected_code . '>' . $val . '</option>' . "\n";
                $selected_code = '';
            }
            $output_element .= '</select>' . "\n";
        }
        return $output_element;
    }
}