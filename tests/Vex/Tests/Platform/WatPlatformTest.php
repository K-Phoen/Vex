<?php

namespace Vex\Tests\Platform;

use Vex\Platform\WatPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class WatPlatformTest extends TestCase
{
    protected function getPlatform($adapter)
    {
        return new WatPlatform($adapter);
    }

    public function testGetName()
    {
        $platform = new WatPlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('wat', $platform->getName());
    }

    /**
     * @dataProvider supportUrlProvider
     */
    public function testSupport($url, $is_supported)
    {
        $platform = new WatPlatform($this->getMockAdapter($this->never()));

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
        $platform = new WatPlatform($this->getMockAdapterReturns($html_content, $this->once()));
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
            array('http://wat.tv/foo', true),
        );
    }

    /**
     * @dataProvider failingExtractProvider
     * @expectedException \Vex\Exception\VideoNotFoundException
     */
    public function testFailingExtract($url, $html_content)
    {
        $platform = new watPlatform($this->getMockAdapterReturns($html_content));
        $platform->extract($url);
    }

    public function pageProvider()
    {
        $url = 'http://www.wat.tv/video/black-rock-shooter-ep06-vostfr-54opt_2g4v7_.html';
        $player = '<iframe src="https://www.wat.tv/embedframe/313147nIc0K118616737" frameborder="0" style="width: 560px; height: 315px;"></iframe>';
        $other_player = '<iframe src="https://www.wat.tv/embedframe/313147nIc0K118616737" frameborder="0" style="width: 520px; height: 280px;"></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="og:image" content="http://cdn.wat.com/thumb.jpg"></head></html>', $player, null, null, 'http://cdn.wat.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="og:image" content="http://cdn.wat.com/thumb.jpg"></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="video:duration" content="120"><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg"></head></html>', $player, null, 120, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="video:duration" content="120"><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg"></head></html>', $player, null, null, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="video:duration" content="120"><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg"></head></html>', $player, null, 120, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="video:duration" content="120"></head></html>', $player, null, 120, null, array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="og:title" content="Foo"></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="og:title" content="Foo"></head></html>', $player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true)),
            array($url, '<html><head><meta name="twitter:player" content="https://www.wat.tv/embedframe/313147nIc0K118616737"><meta property="og:title" content="Foo"></head></html>', $other_player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true, 'width' => 520, 'height' => 280)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url, page html
            array('http://wat.tv/foo', '<html><head></head></html>'),
            array('http://wat.tv/foo', '<html><head><meta property="og:video" content="http://www.wat.tv/swf2/292458nIc0K1184&&50305" /></head></html>'),
        );
    }
}
