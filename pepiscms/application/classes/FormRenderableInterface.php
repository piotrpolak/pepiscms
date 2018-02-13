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
 * Form renderable interface that specifies methods that must be implemented by
 * any form template renderer
 *
 * Usually you don't use this interface directly but extend DefaultFormRenderer
 */
interface FormRenderableInterface
{
    /**
     * Renders form out of the formbuilder
     *
     * @param FormBuilder $formbuilder
     * @param $success
     * @return string
     */
    public function render($formbuilder, $success);

    /**
     * Sets error delimiters
     *
     * @param string $prefix
     * @param string $suffix
     *
     */
    public function setErrorDelimiters($prefix, $suffix);

    /**
     * @return FormBuilder
     */
    public function getFormBuilder();
}
