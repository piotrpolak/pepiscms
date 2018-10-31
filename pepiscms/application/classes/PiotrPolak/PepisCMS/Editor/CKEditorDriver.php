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

namespace PiotrPolak\PepisCMS\Editor;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CKE driver for  RTF Editor
 *
 * @since 0.1
 */
class CKEditorDriver extends DefaultRTEditorDriver
{
    private $js_included = false;

    /**
     * @inheritdoc
     */
    public function setupDefaultConfig($descriptor)
    {
        // Refer to http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html

        if ($this->isFull()) {
            $custom_config = base_url() . 'pepiscms/js/cke_full_config.js';
        } else {
            $custom_config = base_url() . 'pepiscms/js/cke_config.js';
        }

        $style_set = '[]';
        if ($descriptor['editor_styles_set_file']) {
            $style_set = 'my_styles:' . $descriptor['editor_styles_set_file'];
        }

        $this->setConfig('contentsCss', $descriptor['editor_css_file'])
            ->setConfig('baseHref', base_url())
            ->setConfig('height', 400)
            ->setConfig('filebrowserBrowseUrl', base_url() . 'admin/ajaxfilemanager/editorbrowse/')
            ->setConfig('filebrowserImageBrowseUrl', base_url() . 'admin/ajaxfilemanager/editorbrowse/')
            ->setConfig('filebrowserFlashBrowseUrl', base_url() . 'admin/ajaxfilemanager/editorbrowse/')
            ->setConfig('bodyId', $descriptor['editor_css_body_id'])
            ->setConfig('bodyClass', $descriptor['editor_css_body_class'])
            ->setConfig('customConfig', $custom_config)
            ->setConfig('forcePasteAsPlainText', 'true')
            ->setConfig('pasteFromWordRemoveStyle', 'true')
            ->setConfig('stylesSet', $style_set);
    }

    /**
     * @inheritdoc
     */
    public function setFull($is_full = true)
    {
        if ($is_full) {
            $custom_config = base_url() . 'pepiscms/js/cke_full_config.js';
        } else {
            $custom_config = base_url() . 'pepiscms/js/cke_config.js';
        }

        $this->setConfig('customConfig', $custom_config);

        parent::setFull($is_full);
    }

    /**
     * @inheritdoc
     */
    public function generate($text, $height, $instance)
    {
        $this->setConfig('height', $height);

        $r = '';
        if (!$this->js_included) {
            $this->js_included = true;
            $r = '<script src="pepiscms/3rdparty/ckeditor/ckeditor.js"></script>' . "\n";
        }
        $r .= '<textarea id="' . $instance . '" name="' . $instance . '" class="rte">' . htmlspecialchars($text) . '</textarea>' . "\n";
        $r .= '<script>' . "\n";

        foreach ($this->config as $key => $value) {
            $r .= "\t" . 'CKEDITOR.config.' . $key . ' = "' . $value . '";' . "\n";
        }

        $r .= "\t" . 'CKEDITOR.replace( \'' . $instance . '\' );' . "\n";
        $r .= '</script>' . "\n";
        return $r;
    }
}
