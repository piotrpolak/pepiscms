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
 * Date
 *
 * @since 1.0.0
 */
class Date extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::DATE;
    }

    /**
     * @inheritDoc
     */
    public function shouldAttachAdditionalJavaScript()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        return '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' .
            $valueEscaped . '" class="text date' . $extra_css_classes . '" />' .
            '<script>$("#' . $field['field'] . '").datepicker({dateFormat: "yy-mm-dd", changeYear: true, changeMonth: true, yearRange: \'' . (date('Y') - 100) . ':c+10\' });</script>';
    }
}
