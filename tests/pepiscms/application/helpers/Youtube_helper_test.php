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

class Youtube_helper_test extends PepisCMS_TestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        require_once(PROJECT_BASE . 'pepiscms/application/helpers/youtube_helper.php');
    }

    public function test_youtube_get_id_by_url_full()
    {
        $this->assertEquals('9ja8kgTnWo8', youtube_get_id_by_url('https://www.youtube.com/watch?v=9ja8kgTnWo8'));
        $this->assertEquals('9ja8kgTnWo8', youtube_get_id_by_url('http://www.youtube.com/watch?v=9ja8kgTnWo8'));
    }

    public function test_youtube_get_id_by_url_short()
    {
        $this->assertEquals('VPEU3A4wrWY', youtube_get_id_by_url('https://youtu.be/VPEU3A4wrWY'));
        $this->assertEquals('VPEU3A4wrWY', youtube_get_id_by_url('http://youtu.be/VPEU3A4wrWY'));
    }

    public function test_youtube_get_id_by_url_embed()
    {
        $this->assertEquals('z_AcaJuinCs', youtube_get_id_by_url('https://www.youtube.com/embed/z_AcaJuinCs'));
        $this->assertEquals('z_AcaJuinCs', youtube_get_id_by_url('http://www.youtube.com/embed/z_AcaJuinCs'));
    }

}