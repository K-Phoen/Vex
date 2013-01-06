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
    public function testExtract($url, $api_result, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $platform = new VimeoPlatform($this->getMockAdapterReturns($api_result, $this->once()));
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
        $url = 'http://vimeo.com/56490557';
        $api_result = <<<EOF
[{"id":56490557,"title":"Seb Toots Montreal snowboarding run","description":"This is a video about Seb Toots doing a top to bottom run from the Mt.Royal (downtown montreal mountain)<br \/>Films made with Red Scarlet camera during 2 days of shooting !<br \/>Film and edit by : Sunset Films (Mathieu Cowan)<br \/>Twitter\/Instagram: @sebtoots @sunsetfilms","url":"http:\/\/vimeo.com\/56490557","upload_date":"2012-12-29 21:39:40","thumbnail_small":"http:\/\/b.vimeocdn.com\/ts\/391\/154\/391154832_100.jpg","thumbnail_medium":"http:\/\/b.vimeocdn.com\/ts\/391\/154\/391154832_200.jpg","thumbnail_large":"http:\/\/b.vimeocdn.com\/ts\/391\/154\/391154832_640.jpg","user_id":9644233,"user_name":"Seb Toots","user_url":"http:\/\/vimeo.com\/user9644233","user_portrait_small":"http:\/\/b.vimeocdn.com\/ps\/467\/705\/4677055_30.jpg","user_portrait_medium":"http:\/\/b.vimeocdn.com\/ps\/467\/705\/4677055_75.jpg","user_portrait_large":"http:\/\/b.vimeocdn.com\/ps\/467\/705\/4677055_100.jpg","user_portrait_huge":"http:\/\/b.vimeocdn.com\/ps\/467\/705\/4677055_300.jpg","stats_number_of_likes":0,"stats_number_of_plays":139593,"stats_number_of_comments":52,"duration":238,"width":1280,"height":720,"tags":"snowboarding, red scarlet, red camera, seb toots, montreal, city, sunsetfilms, mathieu cowan, mt royal, city mountain, red bull, oakley, ride, o'neill, oneill, giro, empire","embed_privacy":"anywhere"}]
EOF;
        $player = '<iframe src="http://player.vimeo.com/video/56490557" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        $other_player = '<iframe src="http://player.vimeo.com/video/56490557" width="520" height="280" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

        return array(
            // page url, page html, player, title, duration, thumb, options
            array($url, $api_result, $player, null, null, null, array()),
            array($url, $api_result, $player, null, 238, 'http://b.vimeocdn.com/ts/391/154/391154832_640.jpg', array('with_thumb' => true, 'with_duration' => true)),
            array($url, $api_result, $player, null, 238, null, array('with_thumb' => false, 'with_duration' => true)),
            array($url, $api_result, $player, null, null, 'http://b.vimeocdn.com/ts/391/154/391154832_640.jpg', array('with_thumb' => true, 'with_duration' => false)),
            array($url, $api_result, $player, 'Seb Toots Montreal snowboarding run', 238, 'http://b.vimeocdn.com/ts/391/154/391154832_640.jpg', array('with_thumb' => true, 'with_duration' => true, 'with_title' => true)),
            array($url, $api_result, $other_player, null, 238, null, array('with_duration' => true, 'width' => 520, 'height' => 280)),
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
