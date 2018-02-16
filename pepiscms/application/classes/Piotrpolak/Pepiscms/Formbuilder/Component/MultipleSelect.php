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
 * MultipleSelectbox
 *
 * @since 1.0.0
 */
class MultipleSelectbox extends AbstractComponent
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
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '';
        if (is_array($field['values'])) {
            $valueEscaped = is_array($valueEscaped) ? $valueEscaped : array($valueEscaped);

            foreach ($field['values'] as $key => $val) {
                $output_element .= '<span class="multipleInput select"><input type="checkbox" name="' . $field['field'] .
                    '[' . $key . ']" id="' . $field['field'] . '[' . $key . ']" value="' . $key . '" ' .
                    (in_array($key, $valueEscaped) ? ' checked="checked"' : '') . ' /> <label for="' . $field['field'] . '[' .
                    $key . ']">' . $val . '</label></span>' . "\n";
            }
        }
        return $output_element;
    }
}