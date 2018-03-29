<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Html_customization_model
 */
class Html_customization_model extends PEPISCMS_Model implements EntitableInterface
{
    /**
     * {@inheritdoc}
     */
    public function saveById($id, $data)
    {
        array_merge($data, (array)$this->getById(false));
        $config_path = INSTALLATIONPATH . 'application/config/_html_customization.php';

        $this->load->library('ConfigBuilder');
        return $this->configbuilder->writeConfig($config_path, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        @include(APPPATH . 'config/_html_customization.php');
        return (object)$config;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return true;
    }
}
