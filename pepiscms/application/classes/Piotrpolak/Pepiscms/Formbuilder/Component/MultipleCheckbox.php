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
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($field['values'])) {
            // TODO Check validation - when field is required and no value is checked,
            // TODO the form builder selects all the fields (for example in system installer)

            // Setting default value
            if ($field['input_default_value'] === FALSE) {
                $field['input_default_value'] = NULL;
            }
            $valueEscaped = $valueEscaped ? $valueEscaped : $field['input_default_value'];
            $valueEscaped = is_array($valueEscaped) ? array_merge(array(), $valueEscaped) : array($valueEscaped);

            foreach ($field['values'] as $key => $val) {
                $output_element .= '<span class="multipleInput checkbox"><input type="checkbox" name="' .
                    $field['field'] . '[' . $key . ']" id="' . $field['field'] . '[' . $key . ']" value="' . $key .
                    '" ' . (in_array($key, $valueEscaped) ? ' checked="checked"' : '') . ' /> <label for="' . $field['field'] .
                    '[' . $key . ']">' . $val . '</label></span>' . "\n";
            }
        }
        return $output_element;
    }
}