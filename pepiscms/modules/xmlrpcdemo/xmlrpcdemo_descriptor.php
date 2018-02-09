<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Class XmlrpcdemoDescriptor
 */
class XmlrpcdemoDescriptor extends ModuleDescriptor
{
    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return 'XML-RPC service';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return 'XML-RPC service';
    }
}
