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
 * Feedable interface used by Array_model
 *
 * The goal of the method provided in this interface is to return a raw response
 * that will be then cached and processed by Array_model getAdvancedFeed method
 *
 * @since 0.2.2.7
 */
interface BasicDataFeedableInterface
{
    /**
     * Returns a raw array of objects
     *
     * @param mixed $extra_param
     * @return array a raw array of objects
     */
    public function getBasicFeed($extra_param);
}
