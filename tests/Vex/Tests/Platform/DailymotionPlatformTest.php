<?php

namespace Vex\Tests\Platform;

use Vex\Platform\DailymotionPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class DailymotionPlatformTest extends ApiPlatformTestCase
{
    protected function getPlatform($adapter)
    {
        return new DailymotionPlatform($adapter);
    }

    public function testGetName()
    {
        $platform = $this->getPlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('dailymotion', $platform->getName());
    }


    public function supportUrlProvider()
    {
        return array(
            array('http://www.google.fr/', false),
            array('http://www.dailymotion.com/foo', true),
        );
    }

    public function pageProvider()
    {
        $url = 'http://www.dailymotion.com/video/xw7s8w_jormungand-episode-23-vostfr_shortfilms';
        $api_result = '{"title":"Mario Kart (R\u00e9mi Gaillard)","embed_html":"<iframe frameborder=\"0\" width=\"480\" height=\"360\" src=\"http:\/\/www.dailymotion.com\/embed\/video\/x7lni3\"><\/iframe>","duration":136,"thumbnail_url":"http:\/\/s1.dmcdn.net\/uUyF.jpg"}';
        $player = '<iframe frameborder="0" width="560" height="315" src="http://www.dailymotion.com/embed/video/xw7s8w"></iframe>';
        $other_player = '<iframe frameborder="0" width="520" height="280" src="http://www.dailymotion.com/embed/video/xw7s8w"></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, $api_result, $player, null, null, null, array()),
            array($url, $api_result, $player, null, 136, 'http://s1.dmcdn.net/uUyF.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, $api_result, $player, null, 136, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, $api_result, $player, null, null, 'http://s1.dmcdn.net/uUyF.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, $api_result, $player, 'Mario Kart (Rémi Gaillard)', 136, 'http://s1.dmcdn.net/uUyF.jpg', array('with_thumb' => true, 'with_duration' => true, 'with_title' => true)),
            array($url, $api_result, $other_player, null, 136, null, array('with_duration' => true, 'width' => 520, 'height' => 280)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url, api result
            array('http://www.dailymotion.com/video/', null),
            array('http://www.dailymotion.com/video/foo', 'invalid json'),
        );
    }
}
