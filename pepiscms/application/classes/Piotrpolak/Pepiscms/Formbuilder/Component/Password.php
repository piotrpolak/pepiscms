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
 * Password
 *
 * @since 1.0.0
 */
class Password extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function getComponentId()
    {
        return \FormBuilder::PASSWORD;
    }

    /**
     * @inheritDoc
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes)
    {
        return '<input type="password" name="' . $field['field'] . '" id="' . $field['field'] .
            '" value="" autocomplete="off" class="text' . $extra_css_classes . '" />';
    }

    /**
     * @inheritDoc
     */
    public function renderReadOnlyComponent($field, $value, $valueEscaped, &$object)
    {
        return parent::renderReadOnlyComponent($field, '', '', $object);
    }

    /**
     * @inheritDoc
     */
    public function shouldRenderHiddenForReadOnly()
    {
        return false;
    }
}
