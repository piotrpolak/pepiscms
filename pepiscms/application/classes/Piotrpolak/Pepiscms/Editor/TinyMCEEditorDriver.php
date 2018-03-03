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
 * TinyMCE driver for RTF Editor
 *
 * @since 0.1
 */
class TinyMCEEditorDriver extends DefaultRTEditorDriver
{
    /**
     * @inheritdoc
     */
    public function setupDefaultConfig($descriptor)
    {
        $CI = &get_instance();

        $this->setConfig('content_css', base_url() . 'theme/' . $CI->config->item('current_theme') . '/' . $descriptor['editor_css_file']);
        $this->setConfig('body_id', $descriptor['editor_css_body_id']);
        $this->setConfig('body_class', $descriptor['editor_css_body_class']);
        $this->setConfig('width', '100%');
        $this->setConfig('height', 400);
        $this->setConfig('mode', 'exact');
    }

    /**
     * @inheritdoc
     */
    public function generate($text, $height, $instance)
    {
        $this->setConfig('height', $height);

        $r = '<script src="pepiscms/3rdparty/tiny_mce/tiny_mce.js"></script>' . "\n";
        $r .= '<script>' . "\n";
        $r .= 'tinyMCE.init({' . "\n";
        foreach ($this->config as $key => $value) {
            $r .= "\t" . $key . ' : "' . $value . '",' . "\n";
        }

        $r .= "\t" . 'elements : "' . $instance . '",' . "\n";
        $r .= "\t" . 'theme : "advanced",' . "\n";
        $r .= "\t" . 'plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",' . "\n";
        $r .= "\t" . 'theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",' . "\n";
        $r .= "\t" . 'theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",' . "\n";
        $r .= "\t" . 'theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",' . "\n";
        $r .= "\t" . 'theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",' . "\n";
        $r .= "\t" . 'theme_advanced_toolbar_location : "top",' . "\n";
        $r .= "\t" . 'theme_advanced_toolbar_align : "left",' . "\n";
        $r .= "\t" . 'theme_advanced_statusbar_location : "bottom",' . "\n";
        $r .= "\t" . 'theme_advanced_resizing : true' . "\n";

        $r .= '});' . "\n";
        $r .= '</script>' . "\n";
        $r .= '<textarea id="' . $instance . '" name="' . $instance . '">' . htmlspecialchars($text) . '</textarea>' . "\n";
        return $r;
    }
}
