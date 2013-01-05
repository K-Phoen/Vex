<?php

namespace Vex\Tests\Platform;

use Vex\Platform\VeevrPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class VeevrPlatformTest extends TestCase
{
    public function testGetName()
    {
        $platform = new VeevrPlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('veevr', $platform->getName());
    }

    /**
     * @dataProvider supportUrlProvider
     */
    public function testSupport($url, $is_supported)
    {
        $platform = new VeevrPlatform($this->getMockAdapter($this->never()));

        if ($is_supported) {
            $this->assertTrue($platform->support($url));
        } else {
            $this->assertFalse($platform->support($url));
        }
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $html_content, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $platform = new VeevrPlatform($this->getMockAdapterReturns($html_content));
        $expected_data = array(
            'title'         => $expected_title,
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
    public function testFailingExtract($url, $html_content)
    {
        $platform = new VeevrPlatform($this->getMockAdapterReturns($html_content));
        $platform->extract($url);
    }


    public function supportUrlProvider()
    {
        return array(
            array('http://www.google.fr/', false),
            array('http://veevr.fr/foo', false),
            array('http://veevr.com/foo', true),
            array('http://veevr.com/foo/', true),
        );
    }

    public function pageProvider()
    {
        $url = 'http://veevr.com/videos/x40V2HW1A';
        $player = '<iframe src="http://veevr.com/embed/x40V2HW1A" width="640" height="360" scrolling="no" frameborder="0"></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head><meta property="og:url" content="http://veevr.com/videos/x40V2HW1A" /></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta property="og:url" content="http://veevr.com/videos/x40V2HW1A" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.veevr.com/thumb.jpg" /><meta property="og:url" content="http://veevr.com/videos/x40V2HW1A" /></head></html>', $player, null, null, 'http://cdn.veevr.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.veevr.com/thumb.jpg" /><meta property="og:url" content="http://veevr.com/videos/x40V2HW1A" /></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:url" content="http://veevr.com/videos/x40V2HW1A" /><meta property="og:title" content="Foo" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo" /><meta property="og:url" content="http://veevr.com/videos/x40V2HW1A" /></head></html>', $player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url, page html
            array('http://veevr.com/foo', '<html><head><meta property="og:url" content="http://veevr.com/videos/x40V2&HW1A" /></html>'),
            array('http://veevr.com/foo', '<html><head><meta property="og:url" content="http://veevr.com/videos/" /></head></html>'),
        );
    }
}
