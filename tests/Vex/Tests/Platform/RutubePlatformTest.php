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
    public function testExtract($url, $html_content, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $platform = new RutubePlatform($this->getMockAdapterReturns($html_content));
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
        $platform = new RutubePlatform($this->getMockAdapterReturns($html_content));
        $platform->extract($url);
    }

    /**
     * @dataProvider reverseDataProvider
     */
    public function testReverse($html_code, $player_page, $expected_url)
    {
        $platform = new RutubePlatform($this->getMockAdapterReturns($player_page));
        $this->assertSame($expected_url, $platform->reverse($html_code));
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
        $url = 'http://rutube.ru/foo';
        $player = '<iframe width="640" height="360" src="http://rutube.ru/video/embed/4242" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>';
        $other_player = '<iframe width="520" height="280" src="http://rutube.ru/video/embed/4242" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.rutube.ru/thumb.jpg" /><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', $player, null, null, 'http://cdn.rutube.ru/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.rutube.ru/thumb.jpg" /><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta name="twitter:player" value="https://video.rutube.ru/4242" /><meta property="og:title" content="Foo" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo" /><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', $player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true)),
            array($url, '<html><head><meta property="og:title" content="Foo" /><meta name="twitter:player" value="https://video.rutube.ru/4242" /></head></html>', $other_player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true, 'width' => 520, 'height' => 280)),
            array($url, '<html><head><meta name="twitter:player" value="https://video.rutube.ru/4242" /><meta property="video:duration" content="120" /></head></html>', $player, null, 120, null, array('with_thumb' => true, 'with_duration' => true)),
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

    public function reverseDataProvider()
    {
        $player1 = '<iframe width="640" height="360" src="http://rutube.ru/video/embed/6236741" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>';
        $link1 = 'http://rutube.ru/video/9f4dc6bc2db6b6051ea07fb20234c6cc/';
        $page1 = <<<EOF
<!DOCTYPE html >
<html>
    <head>
        <title></title>
        <style>
            html, body {
                overflow: hidden;
                margin:0;
                padding:0;
            }
            body {
                height: 100%;
                width: 100%;
                position: absolute;
            }
            video, object, embed {
                width: 100%;
                height: 100%;
            }
        </style>
        <link rel="canonical" href="http://rutube.ru/video/9f4dc6bc2db6b6051ea07fb20234c6cc/"/>
        <script data-main="/static/js/embed" src="/static/js/libs/require/require.js"></script>
    </head>
    <body>
    </body>
</html>
EOF;

        return array(
            array($player1, $page1, $link1),
        );
    }
}
