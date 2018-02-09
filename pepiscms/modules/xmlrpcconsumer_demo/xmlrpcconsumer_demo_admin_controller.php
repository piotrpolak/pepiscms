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
 * Class XmlrpctestAdmin
 */
class XmlrpctestAdmin extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('WebserviceConsumer');
        $this->load->model('Example_model');

        $time = time();

        // Initialize
        $example = new Example_model();


        // All the calls must be done in try cach block
        try {
            // Simple hello method that returns formated string
            $result = $example->hello('Piotr', 'Polak');
            echo '<h1>' . $result . '</h1>';

            // Returns remote system's database version
            $result = $example->getDbVersion();
            echo '<p>Remote DBMS version: ' . $result . '</p>';

            $result = $example->getDbVersion();
            echo '<p>Local DBMS version: ' . $this->db->version() . '</p>';

            $result = $example->getOwnIp();
            echo '<p>IP: ' . $result . '</p>';

            $result = $example->time();
            echo '<p>Time on the server: ' . date('Y-m-d, h:i:s', $result) . '</p>';

            echo '<p>Local time: ' . date('Y-m-d, h:i:s') . '</p>';

            $example->bindToPrices();
            $result = $example->getPricesByPhoneIdAndCompanyBranchId(201, 1);
            echo '<p>Price of item #201: ' . $result . ' PLN</p>';
        } catch (RemoteAuthorizationException $e) {
            echo 'API key or secret is not valid.';
        } catch (RemoteException $e) {
            echo $e->getMessage();
        }


        $time = time() - $time;
        echo '<p>Executed in: ' . ($time) . 's</p>';
        die();
    }

}
