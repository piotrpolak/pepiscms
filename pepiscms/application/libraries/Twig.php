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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Templating
 *
 * @since 0.2.3
 *
 * @see http://llanalewis.blogspot.com/2013/08/adding-template-engine-twig-on.html
 * @see https://github.com/bmatschullat/Twig-Codeigniter/blob/master/application/libraries/Twig.php
 */
class Twig extends ContainerAware
{
    /**
     * @var Twig_Environment
     */
    private static $twig_instance = null;

    /**
     * @var string|null
     */
    private $site_theme_basepath = null;

    /**
     * Sets site theme basepath - the path where base.html.tiwg is located
     *
     * @param null|string $site_theme_basepath
     */
    public function setSiteThemeBasepath($site_theme_basepath)
    {
        $this->site_theme_basepath = $site_theme_basepath;
    }

    /**
     * Returns site theme basepath - the path where base.html.tiwg is located
     *
     * @return null|string
     */
    public function getSiteThemeBasepath()
    {
        return $this->site_theme_basepath;
    }

    /**
     * Returns instance of Twig Environment in a singletone manner
     *
     * @return Twig_Environment
     */
    private function getTwig()
    {
        if (!self::$twig_instance) {
            $this->benchmark->mark('twig_initialization_start');
            $loader = new Twig_Loader_Filesystem();

            // Setting auto reload and debug modes based on current environment
            self::$twig_instance = new Twig_Environment($loader, array(
                'cache' => INSTALLATIONPATH . '/application/cache/twig/',
                'debug' => (ENVIRONMENT == 'development'),
                'auto_reload' => (ENVIRONMENT == 'development'),
            ));

            // This might be heavy
            $is_legacy_twig = Twig_Environment::VERSION[0] === '1';

            foreach (get_defined_functions() as $functions) {
                foreach ($functions as $function) {
                    if ($is_legacy_twig) {
                        // https://github.com/twigphp/Twig/blob/1.x/lib/Twig/Function.php
                        self::$twig_instance->addFunction($function, new Twig_Function_Function($function));
                    } else {
                        // https://github.com/twigphp/Twig/blob/2.x/lib/Twig/Function.php
                        self::$twig_instance->addFunction(new Twig_Function($function, $function));
                    }
                }
            }

            $this->benchmark->mark('twig_initialization_end');
        }

        return self::$twig_instance;
    }

    /**
     * Renders Twig template
     *
     * @param $path
     * @param $variables
     * @return mixed
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function render($path, &$variables)
    {
        $this->benchmark->mark('twig_render_start');

        $dirname = realpath(dirname($path));
        $basename = basename($path);

        self::getTwig()->getLoader()->setPaths(array($this->getSiteThemeBasepath(), $dirname));

        $rendered_html = self::getTwig()->render($basename, $variables);

        $this->benchmark->mark('twig_render_end');

        return $rendered_html;
    }
}
