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
 * MultipleCheckbox
 *
 * @since 1.0.0
 */
class MultipleCheckbox extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::MULTIPLECHECKBOX;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        \CI_Controller::get_instance()->load->helper('text');
        $output_element = '';
        if (is_array($field['values'])) {
            if ($field['input_default_value'] === false) {
                $field['input_default_value'] = null;
            }
            $value = $value ? $value : $field['input_default_value'];
            $value = is_array($value) ? array_merge(array(), $value) : array($value);

            foreach ($field['values'] as $key => $key_value) {
                $key_escaped = htmlspecialchars($key);

                $value_shortened = character_limiter($key_value, 45, '...');

                $output_element .= '<span class="multipleInput checkbox"><input type="checkbox" name="' .
                    $field['field'] . '[' . $key_escaped . ']" id="' . $field['field'] . '[' . $key_escaped . ']" value="' . $key_escaped .
                    '" ' . (in_array($key, $value) ? ' checked="checked"' : '') . ' /> ' .
                    '<label for="' . $field['field'] . '[' . $key_escaped . ']"' .
                    ($value_shortened != $key_value ? ' title="' . htmlspecialchars($key_value) . '"' : '') .
                    '> ' . htmlspecialchars($value_shortened) . '</label ></span > ' . "\n";
            }
        }
        return $output_element;
    }
}
