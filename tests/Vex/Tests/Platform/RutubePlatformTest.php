<?php

namespace Vex\Tests\Platform;

use Vex\Platform\RutubePlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class RutubePlatformTest extends TestCase
{
    public function testGetName()
    {
        $platform = new RutubePlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('rutube', $platform->getName());
    }

    /**
     * @dataProvider supportUrlProvider
     */
    public function testSupport($url, $is_supported)
    {
        $platform = new RutubePlatform($this->getMockAdapter($this->never()));

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
        $platform = new RutubePlatform($this->getMockAdapterReturns($html_content));
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
    public function testFailingExtract($url, $html_content)
    {
        $platform = new RutubePlatform($this->getMockAdapterReturns($html_content));
        $platform->extract($url);
    }


    public function supportUrlProvider()
    {
        return array(
            array('http://www.google.fr/', false),
            array('http://rutube.fr/foo', false),
            array('http://rutube.ru/foo', true),
            array('http://rutube.ru/foo/', true),
        );
    }

    public function pageProvider()
    {
        return array(
            // page url, page html, player, duration, thumb, options
            array('http://rutube.ru/foo', '<html><head><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', '<iframe width="640" height="360" src="http://rutube.ru/embed/4242" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>', null, null, array()),
            array('http://rutube.ru/foo', '<html><head><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', '<iframe width="640" height="360" src="http://rutube.ru/embed/4242" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>', null, null, array('with_thumb' => true, 'with_duration' => true)),
            array('http://rutube.ru/foo', '<html><head><meta property="og:image" content="http://cdn.rutube.ru/thumb.jpg" /><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', '<iframe width="640" height="360" src="http://rutube.ru/embed/4242" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>', null, 'http://cdn.rutube.ru/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array('http://rutube.ru/foo', '<html><head><meta property="og:image" content="http://cdn.rutube.ru/thumb.jpg" /><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', '<iframe width="640" height="360" src="http://rutube.ru/embed/4242" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>', null, null, array('with_thumb' => false, 'with_duration' => true)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url, page html
            array('http://rutube.ru/foo', '<html><head><meta name="twitter:player" value="https://video.rutube.ru/42&42" /></head></html>'),
            array('http://rutube.ru/foo', '<html><head><meta name="twitter:player" value="https://video.rutube.ru" /></head></html>'),
            array('http://rutube.ru/foo', '<html><head><meta name="twitter:player" value="https://video.rutube.ru/foo" /></head></html>'),
        );
    }
}
