<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Symfony bridge proxy
 * Allows to communicate with Symfony2 Application Kernel
 *
 * Method naming convention is intentionally camelCase, the same as in Symfony2
 *
 * @version 1.1
 *
 * To enable Symfony bridge
 *
 * composer require piotrpolak/codeigniter-symfony2-bridge
 *
 *
 * To get a Symfony2 Service
 *
 * $this->load->moduleLibrary('symfony2_bridge', 'Symfony2_bridge');
 * $result = $this->symfony2_bridge->getContainer()->get('my_service')->businessLogicServiceMethod('primityve parameter'));
 *
 * $em = $this->symfony2_bridge->getContainer()->get('doctrine')->getManager();
 *
 * To use Symfony autoload and to receive a class
 *
 *
 * $this->load->moduleLibrary('symfony2_bridge', 'Symfony2_bridge');
 * $container = $this->symfony2_bridge->getContainer(); // Initializes Symfony2 PSR class loader
 * $imageHelper]= new \Pepis\UtilitiesBundle\Utilities\ImageHelper();
 */
class Symfony2_bridge
{
    /**
     * @var \PiotrPolak\CodeIgniterSymfonyBridge\Bridge
     */
    private $bridge;

    /**
     * @var string
     */
    private $symfony2_root_dir;

    /**
     * Allowed params: root_dir
     *
     * @param Array $params
     */
    public function __construct($params = array())
    {
        $symfony2_root_dir = INSTALLATIONPATH . '../../app/';
        if (isset($params['root_dir'])) {
            $symfony2_root_dir = $params['root_dir'];
        }
        $this->symfony2_root_dir = $symfony2_root_dir;
    }

    /**
     * Tells whether all dependencies are present and the feature is fully enabled.
     */
    public function isFullyEnabled()
    {
        return class_exists('\PiotrPolak\CodeIgniterSymfonyBridge\Bridge');
    }

    /**
     * Returns Symfony application kernel
     *
     * @see http://api.symfony.com/2.7/Symfony/Component/HttpKernel/Kernel.html
     * @return AppKernel|null
     * @throws \PiotrPolak\CodeIgniterSymfonyBridge\Exception\KernelInitializationException
     * @throws Exception
     */
    public function getKernel()
    {
        if (!$this->isFullyEnabled()) {
            throw new \RuntimeException('Symfony2_bridge is not enabled. Please refer to README.md');
        }
        return $this->getBridge()->getKernel();
    }

    /**
     * Returns Symfony2 container
     *
     * @return ContainerInterface
     * @throws \PiotrPolak\CodeIgniterSymfonyBridge\Exception\KernelInitializationException
     * @throws Exception
     */
    public function getContainer()
    {
        if (!$this->isFullyEnabled()) {
            throw new \RuntimeException('Symfony2_bridge is not enabled. Please refer to README.md');
        }
        return $this->getBridge()->getKernel()->getContainer();
    }

    /**
     * @return \PiotrPolak\CodeIgniterSymfonyBridge\Bridge
     */
    private function getBridge()
    {
        if ($this->bridge === null) {
            $is_production = ENVIRONMENT != 'development';
            $this->bridge = new \PiotrPolak\CodeIgniterSymfonyBridge\Bridge($this->symfony2_root_dir, $is_production);
        }

        return $this->bridge;
    }
}
