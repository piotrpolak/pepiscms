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
 * Class Xmlrpcconsumer_demoDescriptor
 */
class Xmlrpcconsumer_demoDescriptor extends ModuleDescriptor
{
    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        return 'XML-RPC consumer demo';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        return 'XML-RPC consumer demo';
    }

}
