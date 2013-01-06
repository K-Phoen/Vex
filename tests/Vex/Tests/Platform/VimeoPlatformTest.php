<?php

namespace Vex\Tests\Platform;

use Vex\Platform\VimeoPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class VimeoPlatformTest extends PlatformTestCase
{
    protected function getPlatform($adapter)
    {
        return new VimeoPlatform($adapter);
    }

    public function testGetName()
    {
        $platform = new VimeoPlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('vimeo', $platform->getName());
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $html_content, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        $platform = new VimeoPlatform($this->getMockAdapterReturns($html_content, $find_thumb || $find_duration ? $this->once() : $this->never()));
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
            array('http://vimeo.com/foo', true),
        );
    }

    public function pageProvider()
    {
        $url = 'http://vimeo.com/42';
        $player = '<iframe src="http://player.vimeo.com/video/42" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        $other_player = '<iframe src="http://player.vimeo.com/video/42" width="520" height="280" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta property="og:image" content="http://cdn.vimeo.com/thumb.jpg"></head></html>', $player, null, null, 'http://cdn.vimeo.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.vimeo.com/thumb.jpg"></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg"></head><meta itemprop="duration" content="PT00H03M58S"></html>', $player, null, 238, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg"></head><meta itemprop="duration" content="PT00H03M58S"></html>', $player, null, null, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg"></head></html><meta itemprop="duration" content="PT00H03M58S">', $player, null, 238, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head></head><meta itemprop="duration" content="PT00H03M58S"></html>', $player, null, 238, null, array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:title" content="Foo"></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo"></head></html>', $player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true)),
            array($url, '<html><head><meta property="og:title" content="Foo"></head></html>', $other_player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true, 'width' => 520, 'height' => 280)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url
            array('http://vimeo.com'),
            array('http://vimeo.com/foo'),
            array('http://vimeo.com/video/42'),
        );
    }
}
