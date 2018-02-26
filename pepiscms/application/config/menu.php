<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['menu'] = array(
    'mainmenu' => array(
        array(
            'controller' => 'about',
            'method' => 'dashboard',
            'icon_path' => 'pepiscms/theme/img/about/dashboard_16.png',
            'show_label' => false,
        ),
        array(
            'controller' => 'pages',
            'method' => 'index',
            'label' => 'menu_pages',
            'append_language_code' => true,
            'append_parameters' => array('language_code', 'view'),
            'icon_path' => 'pepiscms/theme/img/pages/page_16.png',
        ),
        array(
            'controller' => 'ajaxfilemanager',
            'method' => 'browse',
            'label' => 'menu_filemanager',
            'icon_path' => 'pepiscms/theme/img/ajaxfilemanager/ajaxfilemanager_16.png',
        ),
        array(
            'controller' => 'utilities',
            'label' => 'menu_utilities',
            'icon_path' => 'pepiscms/theme/img/utilities/utilities_16.png',
        ),
    )
);
