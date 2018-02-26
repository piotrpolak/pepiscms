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
 * MultipleSelectbox
 *
 * @since 1.0.0
 */
class MultipleSelect extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::MULTIPLESELECT;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($field['values'])) {
            $value = is_array($value) ? $value : array($value);

            foreach ($field['values'] as $key => $val) {
                $key_escaped = htmlspecialchars($key);

                $output_element .= '<span class="multipleInput select"><input type="checkbox" name="' . $field['field'] .
                    '[' . $key_escaped . ']" id="' . $field['field'] . '[' . $key_escaped . ']" value="' . $key_escaped . '" ' .
                    (in_array($key, $value) ? ' checked="checked"' : '') . ' /> <label for="' . $field['field'] . '[' .
                    $key_escaped . ']">' . $val . '</label></span>' . "\n";
            }
        }
        return $output_element;
    }
}
