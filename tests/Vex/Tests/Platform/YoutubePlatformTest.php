<?php

namespace Vex\Tests\Platform;

use Vex\Platform\YoutubePlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class YoutubePlatformTest extends TestCase
{
    public function testGetName()
    {
        $platform = new YoutubePlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('youtube', $platform->getName());
    }

    /**
     * @dataProvider supportUrlProvider
     */
    public function testSupport($url, $is_supported)
    {
        $platform = new YoutubePlatform($this->getMockAdapter($this->never()));

        if ($is_supported) {
            $this->assertTrue($platform->support($url));
        } else {
            $this->assertFalse($platform->support($url));
        }
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $html_content, $expected_player, $expected_duration, $expected_thumb, $options)
    {
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];

        $platform = new YoutubePlatform($this->getMockAdapterReturns($html_content, $find_thumb ? $this->once() : $this->never()));
        $expected_data = array(
            'link'          => $url,
            'embed_code'    => $expected_player,
            'duration'      => $expected_duration,
            'thumb'         => $expected_thumb,
        );

        $this->assertSame($expected_data, $platform->extract($url, $options));
    }

    /**
     * @dataProvider failingExtractProvider
     * @expectedException \Vex\Exception\VideoNotFoundException
     */
    public function testFailingExtract($url)
    {
        $platform = new YoutubePlatform($this->getMockAdapter($this->never()));
        $platform->extract($url);
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
            // page url, page html, player, duration, thumb, options
            array($url, '<html><head></head></html>', $player, null, null, array()),
            array($url, '<html><head><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, 'http://cdn.youtube.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, 'http://cdn.youtube.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.youtube.com/thumb.jpg"></head></html>', $player, null, null, array('with_thumb' => false, 'with_duration' => true)),
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
