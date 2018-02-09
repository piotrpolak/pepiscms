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

class Array_model_tester extends Array_model
{
    private $basic_feed = array();

    public function setBasicFeed(Array $basic_feed)
    {
        $this->basic_feed = $basic_feed;
    }

    /**
     * Returns a raw array of objects
     *
     * @param mixed $extra_param
     * @return array a raw array of objects
     */
    public function getBasicFeed($extra_param)
    {
        return $this->basic_feed;
    }
}