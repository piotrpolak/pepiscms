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
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($field['values'])) {
            $output_element .= '<select name="' . $field['field'] . '" id="' . $field['field'] . '" class="text' .
                $extra_css_classes . '" >' . "\n";

            $was_selected = false;
            $selected_code = '';
            foreach ($field['values'] as $key => $val) {
                $key_escaped = htmlspecialchars($key);

                if (!$was_selected && $value == $key) { // This is required to support null values, we can not use === operator neither to cast the values
                    $was_selected = true;
                    $selected_code = ' selected="selected"';
                }
                $output_element .= '<option value="' . $key_escaped . '" ' . $selected_code . '>' . htmlspecialchars($val) . '</option>' . "\n";
                $selected_code = '';
            }
            $output_element .= '</select>' . "\n";
        }
        return $output_element;
    }

    /**
     * @inheritDoc
     */
    public function renderReadOnlyComponent($field, $value, $valueEscaped, &$object)
    {
        $output_element = '<div class="input_hidden_description">';
        if (isset($field['values'][$value])) {
            $output_element .= $field['values'][$value];
        } else {
            $output_element .= '-';
        }
        $output_element .= '</div>';

        return $output_element;
    }
}
