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
 * File
 *
 * @since 1.0.0
 */
class File extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::FILE;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '<input type="file" name="' . $field['field'] .
            '" id="' . $field['field'] . '" class="inputFile' . ($valueEscaped ? ' hidden' : '') . '" />';
        if ($valueEscaped) {
            $exploded_path = explode('/', $valueEscaped);
            $output_element .= '<div class="form_image"><a href="' . $field['upload_path'] . $valueEscaped . '">' .
                end($exploded_path) . '</a>' .
                '<a href="#" class="remove_form_file" rel="' . $field['field'] . '" title="' .
                \CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '">' .
                \CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '</a></div>';
        }

        $output_element .= '<input type="hidden" name="form_builder_files[' . $field['field'] . ']" value="' . $valueEscaped . '" />' . "\n" .
            '<input type="hidden" name="form_builder_files_remove[' . $field['field'] . ']" value="0" />' . "\n";

        return $output_element;
    }
}