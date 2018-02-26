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
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        $output_element = '<input type="file" name="' . $field['field'] . '" id="' . $field['field'] .
            '" class="inputImage' . ($valueEscaped ? ' hidden' : '') . '" />';
        $output_element .= $this->generateFilePreview($field, $valueEscaped, true);

        $output_element .= '<input type="hidden" name="form_builder_files[' . $field['field'] . ']" value="' . $valueEscaped . '" />' . "\n" .
            '<input type="hidden" name="form_builder_files_remove[' . $field['field'] . ']" value="0" />' . "\n";
        return $output_element;
    }

    /**
     * @inheritdoc
     */
    public function renderReadOnlyComponent($field, $value, $valueEscaped, &$object)
    {
        return $this->generateFilePreview($field, $valueEscaped, false);
    }

    /**
     * @param $valueEscaped
     * @return array|bool|mixed|string
     */
    private function getExtension($valueEscaped)
    {
        $extension = explode('.', $valueEscaped);
        if (count($extension) > 1) {
            $extension = end($extension);
            $extension = strtolower($extension);
        } else {
            $extension = false;
        }
        return $extension;
    }

    /**
     * @param $extension
     * @return string
     */
    private function findImagePath($extension)
    {
        $extensions = array(
            $extension,
            substr($extension, 0, 3)
        );

        foreach ($extensions as $ext) {
            if (file_exists(APPPATH . '/../theme/file_extensions/file_extension_' . $ext . '.png')) {
                return 'pepiscms/theme/file_extensions/file_extension_' . $ext . '.png';
            }
        }

        return 'pepiscms/theme/img/ajaxfilemanager/broken_image_50.png';
    }

    /**
     * @param $field
     * @param $valueEscaped
     * @param $display_delete_file_link
     * @return string
     */
    private function generateFilePreview($field, $valueEscaped, $display_delete_file_link)
    {
        \CI_Controller::get_instance()->load->helper('number');
        $output_element = '';
        if ($valueEscaped) {
            $extension = $this->getExtension($valueEscaped);

            $is_real_image = false;
            if (in_array($extension, array('jpg', 'jpeg', 'png', 'bmp', 'tiff'))) {
                $is_real_image = true;
            }

            if ($is_real_image) {
                $image_path = 'admin/ajaxfilemanager/absolutethumb/100/' . $field['upload_display_path'] . $valueEscaped;
            } else {
                $image_path = $this->findImagePath($extension);
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

            if ($display_delete_file_link) {
                $output_element .= '<a href="#" class="remove_form_image" rel="' . $field['field'] . '" title="' .
                    \CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '">' .
                    \CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '</a><br>' . "\n";
            }

            $output_element .= strtoupper($extension) . ' ' . $file_size . '<br><br>' . $last_modified_at . '</div>' .
                '</div>';
        }
        return $output_element;
    }
}
