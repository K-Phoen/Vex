<?php

namespace Vex\Tests\Platform;


abstract class ApiPlatformTestCase extends TestCase
{
    abstract protected function getPlatform($adapter);

    /**
     * @dataProvider supportUrlProvider
     */
    public function testSupport($url, $is_supported)
    {
        $platform = $this->getPlatform($this->getMockAdapter($this->never()));

        if ($is_supported) {
            $this->assertTrue($platform->support($url));
        } else {
            $this->assertFalse($platform->support($url));
        }
    }

    /**
     * @dataProvider pageProvider
     */
    public function testExtract($url, $api_result, $expected_player, $expected_title, $expected_duration, $expected_thumb, $options)
    {
        $platform = $this->getPlatform($this->getMockAdapterReturns($api_result, $this->once()));
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
    public function testFailingExtract($url, $api_result)
    {
        $platform = $this->getPlatform($this->getMockAdapterReturns($api_result, $api_result === null ? $this->never() : $this->once()));
        $platform->extract($url);
    }
}
