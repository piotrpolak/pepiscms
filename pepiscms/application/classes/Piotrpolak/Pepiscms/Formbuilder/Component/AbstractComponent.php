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
 * AbstractComponent
 *
 * @since 1.0.0
 */
abstract class AbstractComponent implements ComponentInterface
{
    /**
     * @inheritDoc
     */
    public function renderReadOnlyComponent($field, $valueEscaped, &$object, $extra_css_classes)
    {
        return $valueEscaped;
    }

    /**
     * @inheritDoc
     */
    public function shouldAttachAdditionalJavaScript()
    {
        return FALSE;
    }
}