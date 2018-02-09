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
 * Remote application model
 *
 * @since 0.1.5
 */
class Remote_application_model extends Generic_model
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable($this->config->item('database_table_remote_applications'));
        $this->addAcceptedPostField(array('name', 'description', 'maintainer_email', 'api_key', 'api_secret', 'status'));
    }

    /**
     * Tells whether remote applications are supported and installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->db->table_exists($this->getTable());
    }

    /**
     * Returns API Secret associated to the given key, cached
     *
     * @param $api_key
     * @return bool|string
     */
    public function getSecretByKeyCached($api_key)
    {
        $this->load->library('Cachedobjectmanager');

        $object_name = 'api_key_' . $api_key;
        $object = $this->cachedobjectmanager->getObject($object_name,
            CI_Controller::get_instance()->config->item('webservice_definition_cache_ttl'), 'webservice');

        if ($object === FALSE) {
            $object = $this->getSecretByKey($api_key);
            $this->cachedobjectmanager->setObject($object_name, $object, 'webservice');
        }
        return $object;
    }

    /**
     * Returns API Secret associated to the given key
     *
     * @param $api_key
     * @return bool|string
     */
    public function getSecretByKey($api_key)
    {
        $row = $this->db->select('api_secret')
            ->from($this->getTable())
            ->where('api_key', $api_key)
            ->where('status >', 0)
            ->get()
            ->row();

        if ($row) {
            return $row->api_secret;
        }
        return FALSE;
    }

}
