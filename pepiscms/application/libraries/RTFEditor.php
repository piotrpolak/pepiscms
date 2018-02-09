<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * RTF Editor
 *
 * @since 0.1
 */
class RTFEditor
{
    private $type;
    private $driver;
    private $available_editors = array('cke' => 'CKEditorDriver');

    /**
     * Default constructor, empty
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->type = isset($params[0]) ? $params[0] : 'cke'; // Default one

        if (isset($this->available_editors[$this->type])) {
            $driver_name = $this->available_editors[$this->type];
            $this->driver = new $driver_name();
        } else {
            $this->driver = new CKEditorDriver(); // DEFAULT ONE
        }
    }

    /**
     * Returns list of available editors
     *
     * @return array
     */
    public function getAvailableEditors()
    {
        return $this->available_editors;
    }

    /**
     * Sets the list of available editors, 'shortcut' to class name
     *
     * @param array $available_editors
     * @return bool
     */
    public function setAvailableEditors($available_editors)
    {
        if (!is_array($available_editors)) {
            return FALSE;
        }

        $this->available_editors = array();

        foreach ($available_editors as $key => $value) {
            if ($key && $value) {
                $this->available_editors[$key] = $value;
            }
        }
        return TRUE;
    }

    /**
     * Sets config variable
     *
     * @param string $field_name
     * @param string $value
     */
    public function setConfig($field_name, $value)
    {
        $this->driver->setConfig($field_name, $value);
    }

    /**
     * Returns config variable
     *
     * @param string $field_name
     * @return string
     */
    public function getConfig($field_name)
    {
        return $this->driver->getConfig($field_name);
    }

    /**
     * Initializes default config
     *
     * @return bool
     */
    public function setupDefaultConfig()
    {
        $CI = &get_instance();

        $CI->load->config('editor');
        $descriptor['editor_css_file'] = $CI->config->item('editor_css_file');
        $descriptor['editor_css_body_id'] = $CI->config->item('editor_css_body_id');
        $descriptor['editor_css_body_class'] = $CI->config->item('editor_css_body_class');
        $descriptor['editor_styles_set_file'] = $CI->config->item('editor_styles_set_file');

        $theme_descriptor_path = './theme/' . $CI->config->item('current_theme') . '/descriptor.php';
        if (file_exists($theme_descriptor_path)) {
            require($theme_descriptor_path);
        }

        $css_file = base_url() . 'theme/' . $CI->config->item('current_theme') . '/' . $descriptor['editor_css_file'];
        if (strpos($descriptor['editor_css_file'], 'http://') !== FALSE || strpos($descriptor['editor_css_file'], 'https://') !== FALSE) {
            $css_file = $descriptor['editor_css_file'];
        } elseif ($descriptor['editor_css_file'][0] == '/') {
            $css_file = base_url() . $descriptor['editor_css_file'];
        }

        $descriptor['editor_css_file'] = $css_file;

        if ($descriptor['editor_styles_set_file']) {
            $styles_set_file = base_url() . 'theme/' . $CI->config->item('current_theme') . '/' . $descriptor['editor_styles_set_file'];
            if (strpos($descriptor['editor_styles_set_file'], 'http://') !== FALSE || strpos($descriptor['editor_styles_set_file'], 'https://') !== FALSE) {
                $styles_set_file = $descriptor['editor_styles_set_file'];
            } elseif ($descriptor['editor_styles_set_file'][0] == '/') {
                $styles_set_file = base_url() . $descriptor['editor_styles_set_file'];
            }

            $descriptor['editor_styles_set_file'] = $styles_set_file;
        }

        return $this->driver->setupDefaultConfig($descriptor);
    }

    /**
     * Generates RTF editor
     *
     * @param string $text
     * @param int $height
     * @param string $instance
     * @return string HTML
     */
    public function generate($text, $height = 500, $instance = 'editor')
    {
        return $this->driver->generate($text, $height, $instance);
    }

    /**
     * Sets editor into full mode
     *
     * @param bool $is_full
     */
    public function setFull($is_full = TRUE)
    {
        $this->driver->setFull($is_full);
    }

    /**
     * Returns whether the editor is in full mode
     *
     * @return bool
     */
    public function isFull()
    {
        return $this->driver->isFull();
    }

}

/**
 * RTF Editor driver interface
 *
 * @since 0.1
 */
interface RTEditor
{

    /**
     * Initializes default config
     *
     * @param $descriptor
     * @return bool
     */
    public function setupDefaultConfig($descriptor);

    /**
     * Generates RTF editor HTML
     *
     * @param string $text
     * @param int $height
     * @param string $instance
     * @return string
     */
    public function generate($text, $height, $instance);

    /**
     * Sets a config variable value
     *
     * @param $field_name
     * @param $value
     * @return mixed
     */
    public function setConfig($field_name, $value);

    /**
     * Returns a single config variable
     *
     * @param string $field_name
     * @return string
     */
    public function getConfig($field_name);

    /**
     * Returns full config array
     *
     * @return array
     */
    public function getConfigArray();

    /**
     * Sets editor into full mode
     *
     * @param bool $is_full
     */
    public function setFull($is_full = TRUE);

    /**
     * Returns whether the editor is in full mode
     *
     * @return bool
     */
    public function isFull();
}

/**
 * RTF Editor abstract class, base for any driver
 *
 * @since 0.1
 */
abstract class DefaultRTEditorDriver implements RTEditor
{

    protected $config = array();
    protected $is_full = FALSE;

    /**
     * @inheritdoc
     */
    public function setConfig($field_name, $value)
    {
        $this->config[$field_name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getConfig($field_name)
    {
        return $this->config[$field_name];
    }

    /**
     * @inheritdoc
     */
    public function getConfigArray()
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function setFull($is_full = TRUE)
    {
        $this->is_full = $is_full;
    }

    /**
     * @inheritdoc
     */
    public function isFull()
    {
        return $this->is_full;
    }

}

/**
 * CKE driver for  RTF Editor
 *
 * @since 0.1
 */
class CKEditorDriver extends DefaultRTEditorDriver
{
    private $js_included = FALSE;

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
    public function setFull($is_full = TRUE)
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
            $this->js_included = TRUE;
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

        $r = '<script type="text/javascript" src="pepiscms/3rdparty/tiny_mce/tiny_mce.js"></script>' . "\n";
        $r .= '<script type="text/javascript">' . "\n";
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
