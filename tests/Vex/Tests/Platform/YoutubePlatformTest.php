<?php

namespace Vex\Tests\Platform;

use Vex\Platform\YoutubePlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class YoutubePlatformTest extends PlatformTestCase
{
    protected function getPlatform($adapter)
    {
        return new YoutubePlatform($adapter);
    }

    public function testGetName()
    {
        $platform = new YoutubePlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('youtube', $platform->getName());
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $html_content, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];

        $platform = new YoutubePlatform($this->getMockAdapterReturns($html_content, $find_thumb ? $this->once() : $this->never()));
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
            array('http://youtube.fr/foo', true),
            array('http://youtube.com/foo', true),
            array('http://youtu.be/foo', true),
        );
    }

    public function pageProvider()
    {
        $url = 'https://www.youtube.com/watch?v=L1K3Mv3CVYU';
        $player = '<iframe width="560" height="315" src="http://www.youtube.com/embed/L1K3Mv3CVYU" frameborder="0" allowfullscreen></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, null, 'http://cdn.youtube.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, null, 'http://cdn.youtube.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:title" content="Hey !"><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, null, 'http://cdn.youtube.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="og:title" content="Hey!"><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, 'Hey!', null, 'http://cdn.youtube.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false, 'with_title' => true)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url
            array('foo'),
            array('https://www.youtube.com/watch'),
        );
    }
}
