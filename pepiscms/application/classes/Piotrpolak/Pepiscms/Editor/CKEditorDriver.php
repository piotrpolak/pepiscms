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

namespace Piotrpolak\Pepiscms\Editor;

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

        $this->setConfig('contentsCss', $descriptor['editor_css_file']);
        $this->setConfig('baseHref', base_url());
        $this->setConfig('height', 400);

        $this->setConfig('filebrowserBrowseUrl', base_url() . 'admin/ajaxfilemanager/editorbrowse/');
        $this->setConfig('filebrowserImageBrowseUrl', base_url() . 'admin/ajaxfilemanager/editorbrowse/');
        $this->setConfig('filebrowserFlashBrowseUrl', base_url() . 'admin/ajaxfilemanager/editorbrowse/');
        $this->setConfig('bodyId', $descriptor['editor_css_body_id']);
        $this->setConfig('bodyClass', $descriptor['editor_css_body_class']);

        if ($this->isFull()) {
            $this->setConfig('customConfig', base_url() . 'pepiscms/js/cke_full_config.js');
        } else {
            $this->setConfig('customConfig', base_url() . 'pepiscms/js/cke_config.js');
        }

        $this->setConfig('forcePasteAsPlainText', 'true');
        $this->setConfig('pasteFromWordRemoveStyle', 'true');


        if ($descriptor['editor_styles_set_file']) {
            $this->setConfig('stylesSet', 'my_styles:' . $descriptor['editor_styles_set_file']);
        } else {
            $this->setConfig('stylesSet', '[]');
        }
    }

    /**
     * @inheritdoc
     */
    public function setFull($is_full = true)
    {
        if ($is_full) {
            $this->setConfig('customConfig', base_url() . 'pepiscms/js/cke_full_config.js');
        } else {
            $this->setConfig('customConfig', base_url() . 'pepiscms/js/cke_config.js');
        }
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
            $r = '<script type="text/javascript" src="pepiscms/3rdparty/ckeditor/ckeditor.js"></script>' . "\n";
        }
        $r .= '<textarea id="' . $instance . '" name="' . $instance . '" class="rte">' . htmlspecialchars($text) . '</textarea>' . "\n";
        $r .= '<script type="text/javascript">' . "\n";

        foreach ($this->config as $key => $value) {
            $r .= "\t" . 'CKEDITOR.config.' . $key . ' = "' . $value . '";' . "\n";
        }

        $r .= "\t" . 'CKEDITOR.replace( \'' . $instance . '\' );' . "\n";
        $r .= '</script>' . "\n";
        return $r;
    }
}
