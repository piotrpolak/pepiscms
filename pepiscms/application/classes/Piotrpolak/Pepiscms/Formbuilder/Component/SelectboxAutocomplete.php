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
 * SelectboxAutocomplete
 *
 * @since 1.0.0
 */
class SelectboxAutocomplete extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::SELECTBOX_AUTOCOMPLETE;
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
        $output_element = '<input type="hidden" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $valueEscaped . '" />';

        // FIXME Remove POST workaround
        $field_ac_label = $field['field'] . '_ac_label';
        $output_element .= '<input type="text" name="' . $field_ac_label . '" id="' . $field_ac_label . '" value="' . (isset($_POST[$field_ac_label]) ? $_POST[$field_ac_label] : '') . '" class="text' . $extra_css_classes . '" />';
        $output_element .= '<script>$("#' . $field['field'] . '_ac_label").autocomplete({source: "' . $field['autocomplete_source'] . '", minLength: 1, select: function(event, ui) {
                if( !ui.item || ui.item == undefined ) return;
                $("#' . $field['field'] . '").val(""+ui.item.id);
            }});</script>';
        return $output_element;
    }
}
