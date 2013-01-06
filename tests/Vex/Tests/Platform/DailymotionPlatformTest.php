<?php

namespace Vex\Tests\Platform;

use Vex\Platform\DailymotionPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class DailymotionPlatformTest extends PlatformTestCase
{
    protected function getPlatform($adapter)
    {
        return new DailymotionPlatform($adapter);
    }

    public function testGetName()
    {
        $platform = new DailymotionPlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('dailymotion', $platform->getName());
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $html_content, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        $platform = new DailymotionPlatform($this->getMockAdapterReturns($html_content, $find_thumb || $find_duration ? $this->once() : $this->never()));
        $expected_data = array(
            'title'         => $expected_title,
            'link'          => $url,
            'embed_code'    => $expected_player,
            'duration'      => $expected_duration,
            'thumb'         => $expected_thumb,
        );

        $this->assertSame($expected_data, $platform->extract($url, $options));
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
        $player = '<iframe frameborder="0" width="560" height="315" src="http://www.dailymotion.com/embed/video/xw7s8w"></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta property="og:image" content="http://cdn.dailymotion.com/thumb.jpg" /></head></html>', $player, null, null, 'http://cdn.dailymotion.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.dailymotion.com/thumb.jpg" /></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="video:duration" content="120" /><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, 120, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="video:duration" content="120" /><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, null, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="video:duration" content="120" /><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, 120, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="video:duration" content="120" /></head></html>', $player, null, 120, null, array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', $player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', '<iframe frameborder="0" width="640" height="320" src="http://www.dailymotion.com/embed/video/xw7s8w"></iframe>', 'Foo', null, null, array('with_thumb' => true, 'with_title' => true, 'width' => '640', 'height' => '320')),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url
            array('http://www.dailymotion.com/video/'),
        );
    }
}
