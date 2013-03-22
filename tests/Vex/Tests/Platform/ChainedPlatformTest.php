<?php

namespace Vex\Tests\Platform;

use Vex\Platform\ChainedPlatform;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class ChainedPlatformTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $platform = new ChainedPlatform();
        $this->assertEquals('chained', $platform->getName());
    }

    public function testAddPlatformsInConstructor()
    {
        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');
        $chained_platform = new ChainedPlatform(array($platform1, $platform2));

        $this->assertSame(array($platform1, $platform2), $chained_platform->getPlatforms());
    }

    public function testAddPlatform()
    {
        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');
        $chained_platform = new ChainedPlatform();

        $this->assertEmpty($chained_platform->getPlatforms());

        $chained_platform->addPlatform($platform1);
        $this->assertSame(array($platform1), $chained_platform->getPlatforms());

        $chained_platform->addPlatform($platform2);
        $this->assertSame(array($platform1, $platform2), $chained_platform->getPlatforms());
    }

    public function testAddPlatforms()
    {
        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');
        $chained_platform = new ChainedPlatform();

        $this->assertEmpty($chained_platform->getPlatforms());

        $chained_platform->addPlatforms(array($platform1, $platform2));
        $this->assertSame(array($platform1, $platform2), $chained_platform->getPlatforms());
    }

    public function testSupportTrue()
    {
        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');

        $platform1
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(false));

        $platform2
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(true));

        $chained_platform = new ChainedPlatform(array($platform1, $platform2));
        $this->assertTrue($chained_platform->support('some url'));
    }

    public function testSupportFalse()
    {
        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');

        $platform1
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(false));

        $platform2
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(false));

        $chained_platform = new ChainedPlatform(array($platform1, $platform2));
        $this->assertFalse($chained_platform->support('some url'));
    }

    public function testExtract()
    {
        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform3 = $this->getMock('\Vex\Platform\PlatformInterface');

        // does not support anything
        $platform1
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(false));
        $platform1
            ->expects($this->never())
            ->method('extract');

        // supports, but does not find the video
        $platform2
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(true));
        $platform2
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo('some url'))
            ->will($this->throwException(new \Vex\Exception\VideoNotFoundException()));

        // supports and finds
        $platform3
            ->expects($this->once())
            ->method('support')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(true));
        $platform3
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(42));

        $chained_platform = new ChainedPlatform(array($platform1, $platform2, $platform3));
        $this->assertEquals(42, $chained_platform->extract('some url'));
    }

    public function testReverse()
    {
        $to_reverse = 'some html embed code';
        $url = 'http://some.url';

        $platform1 = $this->getMock('\Vex\Platform\PlatformInterface');
        $platform2 = $this->getMock('\Vex\Platform\PlatformInterface');

        $platform1
            ->expects($this->once())
            ->method('reverse')
            ->with($this->equalTo($to_reverse))
            ->will($this->returnValue(null));

        $platform2
            ->expects($this->once())
            ->method('reverse')
            ->with($this->equalTo($to_reverse))
            ->will($this->returnValue($url));

        $chained_platform = new ChainedPlatform(array($platform1, $platform2));
        $this->assertEquals($url, $chained_platform->reverse($to_reverse));
    }

    /**
     * @expectedException \Vex\Exception\VideoNotFoundException
     */
    public function testExtractWithNoPlatform()
    {
        $chained_platform = new ChainedPlatform();
        $chained_platform->extract('foo');
    }
}
