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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Radio
 *
 * @since 1.0.0
 */
class Radio extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::RADIO;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($field['values'])) {
            if ($field['input_default_value'] === false) {
                $field['input_default_value'] = null;
            }
            $value = $value ? $value : $field['input_default_value'];
            $value = is_array($value) ? $value : array($value);

            foreach ($field['values'] as $key => $val) {
                $key_escaped = htmlspecialchars($key);

                $output_element .= '<span class="multipleInput radio"><input type="radio" name="' . $field['field'] .
                    '" id="' . $field['field'] . '[' . $key_escaped . ']" value="' . $key_escaped . '" ' .
                    (in_array($key, $value) ? ' checked="checked"' : '') .
                    ' class="text' . $extra_css_classes . '" /> <label for="' . $field['field'] . '[' . $key_escaped . ']">' .
                    $val . '</label></span>' . "\n";
            }
        }
        return $output_element;
    }
}
