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

namespace PiotrPolak\PepisCMS\Formbuilder\Component;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Form builder component main interface
 */
interface ComponentInterface
{
    /**
     * Returns component ID
     *
     * @return int
     */
    public function getComponentId();

    /**
     * Renders component for a read only forms
     *
     * @param $field
     * @param $value
     * @param $valueEscaped
     * @param $object
     * @return mixed
     */
    public function renderReadOnlyComponent($field, $value, $valueEscaped, &$object);

    /**
     * Renders component
     *
     * @param $field
     * @param $value
     * @param $valueEscaped
     * @param $object
     * @param $extra_css_classes
     * @return mixed
     */
    public function renderComponent($field, $value, $valueEscaped, &$object, $extra_css_classes);

    /**
     * Tells whether additional JS should be attached
     *
     * @return bool
     */
    public function shouldAttachAdditionalJavaScript();

    /**
     * Tells whether renderer should render a hidden field for read only mode.
     *
     * @return bool
     */
    public function shouldRenderHiddenForReadOnly();
}
