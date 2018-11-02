<?php

class Twig_test extends PepisCMS_TestCase
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

        $variables = array('name' => 'World');
        $this->assertEquals('Hello WORLD!', $twig->render('index.twig', $variables));
    }
}
