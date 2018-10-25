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
 * AbstractComponent
 *
 * @since 1.0.0
 */
abstract class AbstractComponent implements ComponentInterface
{
    /**
     * @inheritDoc
     */
    public function renderReadOnlyComponent($field, $value, $valueEscaped, &$object)
    {
        $output_element = '';
        if (is_array($valueEscaped)) {
            // Multiple select etc
            foreach ($valueEscaped as $v) {
                if (isset($field['values'][$v])) {
                    $output_element .= '<span class="multipleInput">' . htmlspecialchars($field['values'][$v]) . '</span>' . "\n";
                }
            }
        } else {
            if (isset($field['values'][$valueEscaped])) {
                $output_element = '<div class="input_hidden_description">' . htmlspecialchars($field['values'][$valueEscaped]) . '</div>';
            } else {
                $value_html = $valueEscaped;
                if (!$value_html && $value_html !== 0) {
                    $value_html = '-';
                }
                $output_element = '<div class="input_hidden_description">' . $value_html . '</div>';
            }
        }

        return $output_element;
    }

    /**
     * @inheritDoc
     */
    public function shouldAttachAdditionalJavaScript()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldRenderHiddenForReadOnly()
    {
        return true;
    }
}
