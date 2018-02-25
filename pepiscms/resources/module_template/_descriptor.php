<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * {module_name} descriptor class
 * 
 * @author {author}
 * @date {date}
 * @classTemplateVersion 20180225
 */
class {module_class_name}Descriptor extends ModuleDescriptor {

    /**
     * Cached variable
     * 
     * @var String 
     */
    private $module_name;
    
    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->module_name = strtolower(str_replace('Descriptor', '', __CLASS__));
        $this->load->moduleLanguage( $this->module_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($language)
    {
        $this->load->moduleLanguage($this->module_name);
        return $this->lang->line($this->module_name . '_module_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language)
    {
        $this->load->moduleLanguage($this->module_name);
        $description_label = $this->module_name . '_module_description';
        $description = $this->lang->line($this->module_name . '_module_description');
        if( $description == $description_label )
        {
            return '';
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayedInMenu()
    {
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, FALSE) . '/resources/install.sql';
        if (!file_exists($path))
        {
            return FALSE;
        }
        $this->db->query(file_get_contents($path));
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        $path = $this->load->resolveModuleDirectory($this->module_name, FALSE) . '/resources/uninstall.sql';
        if (!file_exists($path))
        {
            return FALSE;
        }
        $this->db->query(file_get_contents($path));
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminSubmenuElements($language)
    {
        return FALSE;
        
//        return array(
//			array(
//				'controller' => $this->module_name,
//				'method' => 'edit',
//				'label' => $this->lang->line($this->module_name.'_add'),
//				'description' => ''
//			),
//		);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminDashboardElements($language)
    {
        return FALSE;

//        return array(
//            array(
//                'controller' => $this->module_name,
//                'method' => 'edit',
//                'label' => $this->lang->line($this->module_name . '_add'),
//                'description' => '',
//                //'icon' => module_resources_url($this->module_name).'cache_32.png', //module_icon_url($this->module_name),
//                //'url' => 'http://www.disneyland.pl/', // URL can be used instead of controller and method
//                //'target' => '_blank',
//                //'group' => 'dashboard_group_default',
//            ),
//        );
    }

}
