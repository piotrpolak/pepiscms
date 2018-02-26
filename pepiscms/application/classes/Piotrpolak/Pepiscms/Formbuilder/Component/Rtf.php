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
 * Rtf
 *
 * @since 1.0.0
 */
class Rtf extends AbstractComponent
{
    private $isFull = false;

    /**
     * Rtf constructor.
     * @param bool $isFull
     */
    public function __construct($isFull = false)
    {
        $this->isFull = $isFull;
    }


    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        if ($this->isFull) {
            return \FormBuilder::RTF_FULL;
        }

        return \FormBuilder::RTF;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        \CI_Controller::get_instance()->load->library('RTFEditor');
        \CI_Controller::get_instance()->rtfeditor->setupDefaultConfig();
        if ($this->isFull) {
            \CI_Controller::get_instance()->rtfeditor->setFull();
        }

        if (isset($field['options']['rtf'])) {
            foreach ($field['options']['rtf'] as $option_key => $option_value) {
                \CI_Controller::get_instance()->rtfeditor->setConfig($option_key, $option_value);
            }
        }

        // FIXME Set user defined default value
        return \CI_Controller::get_instance()->rtfeditor->generate($value, 500, $field['field']);
    }
}
