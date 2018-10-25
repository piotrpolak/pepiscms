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
 * Colorpicker
 *
 * @since 1.0.0
 */
class Colorpicker extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::COLORPICKER;
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
        return '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $valueEscaped .
            '" class="text corolpicker ' . $extra_css_classes . '" maxlength="6" size="6" />' .
            '<script>$("#' . $field['field'] . '").colpick({layout:"hex", onChange:function(hsb,hex,rgb,el,bySetColor){$(el).css("border-right", "solid 20px #"+hex);if(!bySetColor) $(el).val(hex);},onSubmit:function(hsb,hex,rgb,el) {$(el).css("border-right", "solid 20px #"+hex);$(el).colpickHide();}}).keyup(function(){$(this).colpickSetColor(this.value);}).css("border-right", "solid 20px #"+$("#' . $field['field'] . '").val());</script>';
    }
}
