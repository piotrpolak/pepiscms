<?php

class TwigTest extends PepisCMS_TestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        require_once(PROJECT_BASE . 'pepiscms/application/libraries/Twig.php');
    }

    public function test_default()
    {
        $twig = new Twig();
        $twig->setSiteThemeBasepath(__DIR__ . '/twig_templates/');

        $this->assertEquals('Hello WORLD!', $twig->render('index.twig', array('name' => 'World')));
    }
}
