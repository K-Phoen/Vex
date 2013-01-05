<?php

namespace Vex\Tests\Platform;

use Vex\Platform\QipPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class QipPlatformTest extends TestCase
{
    public function testGetName()
    {
        $platform = new QipPlatform($this->getMockAdapter($this->never()));
        $this->assertEquals('qip', $platform->getName());
    }

    /**
     * @dataProvider supportUrlProvider
     */
    public function testSupport($url, $is_supported)
    {
        $platform = new QipPlatform($this->getMockAdapter($this->never()));

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
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        $platform = new QipPlatform($this->getMockAdapterReturns($html_content, $find_thumb || $find_duration ? $this->once() : $this->never()));
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
    public function testFailingExtract($url)
    {
        $platform = new QipPlatform($this->getMockAdapter($this->never()));
        $platform->extract($url);
    }


    public function supportUrlProvider()
    {
        return array(
            array('http://www.google.fr/', false),
            array('http://qip.fr/foo', false),
            array('http://qip.ru/foo', true),
            array('http://qip.ru/foo/', true),
            array('http://smotri.com/video/view/?id=foo', true),
            array('http://smotri.ru/video/view/?id=foo', true),
        );
    }

    public function pageProvider()
    {
        $url = 'http://smotri.com/video/view/?id=v2348906c5ff';
        $player = '<object id="smotriComVideoPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="640" height="360"><param name="movie" value="http://pics.video.qip.ru/player.swf?file=v2348906c5ff&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics%2Esmotri%2Ecom%2Fcskins%2Fblue%2Fskin%5Fcolor%2Exml&xmldatasource=http%3A%2F%2Fpics.video.qip.ru%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="bgcolor" value="#ffffff" /><embed name="smotriComVideoPlayer" src="http://pics.video.qip.ru/player.swf?file=v2348906c5ff&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics%2Esmotri%2Ecom%2Fcskins%2Fblue%2Fskin%5Fcolor%2Exml&xmldatasource=http%3A%2F%2Fpics.video.qip.ru%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="window" width="640" height="360" type="application/x-shockwave-flash"></embed></object>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, '<html><head></head></html>', $player, null, null, null, array()),
            array($url, '<html><head><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, null, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, null, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:duration" content="120" /><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, 120, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:duration" content="120" /><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, null, 'http://cdn.smotri.com/thumb.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, '<html><head><meta property="og:duration" content="120" /><meta property="og:image" content="http://cdn.smotri.com/thumb.jpg" /></head></html>', $player, null, 120, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:duration" content="120" /></head></html>', $player, null, 120, null, array('with_thumb' => true, 'with_duration' => true)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', $player, null, null, null, array('with_thumb' => true, 'with_title' => false)),
            array($url, '<html><head><meta property="og:title" content="Foo" /></head></html>', $player, 'Foo', null, null, array('with_thumb' => true, 'with_title' => true)),
        );
    }

    public function failingExtractProvider()
    {
        return array(
            // page url
            array('http://smotri.com/video/view/'),
        );
    }
}
