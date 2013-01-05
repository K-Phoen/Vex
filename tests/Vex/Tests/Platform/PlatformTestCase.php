<?php

namespace Vex\Tests\Platform;

abstract class PlatformTestCase extends TestCase
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
     * @dataProvider failingExtractProvider
     * @expectedException \Vex\Exception\VideoNotFoundException
     */
    public function testFailingExtract($url)
    {
        $platform = $this->getPlatform($this->getMockAdapter($this->never()));
        $platform->extract($url);
    }
}
