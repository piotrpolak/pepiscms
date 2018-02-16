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

    public function renderReadOnlyComponent($field, $valueEscaped, &$object, $extra_css_classes);

    /**
     * Renders component
     */
    public function renderComponent($field, $valueEscaped, &$object, $extra_css_classes);

    public function shouldAttachAdditionalJavaScript();
}