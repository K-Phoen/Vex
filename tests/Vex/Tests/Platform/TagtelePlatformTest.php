<?php

namespace Vex\Tests\Platform;

use Vex\Platform\TagTelePlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class TagTelePlatformTest extends PlatformTestCase
{
    protected function getPlatform($adapter)
    {
        return new TagTelePlatform($adapter);
    }

    public function testGetName()
    {
        $platform = new TagTelePlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('tagtele', $platform->getName());
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $html_content, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];

        $platform = new TagTelePlatform($this->getMockAdapterReturns($html_content, $find_thumb ? $this->once() : $this->never()));
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
            array('http://www.tagtele.com/foo', true),
            array('http://tagtele.com/foo', true),
        );
    }

    public function pageProvider()
    {
        $url = 'http://www.tagtele.com/videos/voir/94555';
        $player = '<object width="425" height="350"><param name="movie" value="http://www.tagtele.com/v/94555"></param><param name="wmode" value="transparent"></param><embed src="http://www.tagtele.com/v/94555" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
        $other_player = '<object width="520" height="280"><param name="movie" value="http://www.tagtele.com/v/94555"></param><param name="wmode" value="transparent"></param><embed src="http://www.tagtele.com/v/94555" type="application/x-shockwave-flash" wmode="transparent" width="520" height="280"></embed></object>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta property="og:image" content="http://cdn.tagtele.com/thumb.jpg" /></head></html>', $player, null, null, 'http://cdn.tagtele.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.tagtele.com/thumb.jpg" /></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.tagtele.com/thumb.jpg" /></head></html>', $player, null, null, 'http://cdn.tagtele.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', $other_player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true, 'width' => 520, 'height' => 280)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url
            array('//'),
        );
    }
}
