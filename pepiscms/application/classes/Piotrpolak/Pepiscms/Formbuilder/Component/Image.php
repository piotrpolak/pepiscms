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
 * Image
 *
 * @since 1.0.0
 */
class Image extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::IMAGE;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        \CI_Controller::get_instance()->load->helper('number');

        $output_element = '<input type="file" name="' . $field['field'] . '" id="' . $field['field'] .
            '" class="inputImage' . ($valueEscaped ? ' hidden' : '') . '" />';
        if ($valueEscaped) {
            // Determining image extension
            $image_extension = explode('.', $valueEscaped);
            if (count($image_extension) > 1) {
                $image_extension = end($image_extension);
                $image_extension = strtolower($image_extension);
            } else {
                $image_extension = FALSE;
            }

            $image_path = 'pepiscms/theme/img/ajaxfilemanager/broken_image_50.png';

            $is_real_image = FALSE;
            if (in_array($image_extension, array('jpg', 'jpeg', 'png', 'bmp', 'tiff'))) {
                $is_real_image = TRUE;
            }

            if ($is_real_image) {
                $image_path = 'admin/ajaxfilemanager/absolutethumb/100/' . $field['upload_display_path'] . $valueEscaped;
            } else if (file_exists(APPPATH . '/../theme/file_extensions/file_extension_' . $image_extension . '.png')) {
                $image_path = 'pepiscms/theme/file_extensions/file_extension_' . $image_extension . '.png';
            }

            $output_element .= '<div class="form_image">' . "\n"; // Leave it as it is..
            $output_element .= '    <div>' . "\n";


            $output_element .= '        <a href="' . $field['upload_display_path'] . $valueEscaped . '"' .
                ($is_real_image ? 'class=" image"' : 'class=" image_like" target="_blank"') . '><img src="' .
                $image_path . '" alt="" /></a>' . "\n" .
                '    </div>' . "\n" .
                '<div class="summary">';

            $file_size = '';
            $last_modified_at = \CI_Controller::get_instance()->lang->line('formbuilder_file_not_found');
            if (file_exists($field['upload_path'] . $valueEscaped)) {
                $file_size = byte_format(filesize($field['upload_path'] . $valueEscaped));
                $filemtime = filemtime($field['upload_path'] . $valueEscaped);
                $last_modified_at = date('Y-m-d', $filemtime) . '<br>' . date('H:i:s', $filemtime);
            }

            $output_element .= '<a href="#" class="remove_form_image" rel="' . $field['field'] . '" title="' .
                \CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '">' .
                \CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '</a><br>' . "\n" .
                strtoupper($image_extension) . ' ' . $file_size . '<br><br>' . $last_modified_at . '</div>' .
                '</div>';

        }

        $output_element .= '<input type="hidden" name="form_builder_files[' . $field['field'] . ']" value="' . $valueEscaped . '" />' . "\n" .
            '<input type="hidden" name="form_builder_files_remove[' . $field['field'] . ']" value="0" />' . "\n";
        return $output_element;
    }
}