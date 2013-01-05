<?php

namespace Vex\Tests;

use Vex\Vex;


/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class VexTest extends \PHPUnit_Framework_TestCase
{
    protected $vex;

    protected function setUp()
    {
        $this->vex = new Vex();
    }

    public function testRegisterPlatform()
    {
        $platform = $this->getMockPlatform();
        $this->vex->addPlatform($platform);

        $this->assertSame($platform, $this->vex->getPlatform());
    }

    public function testRegisterPlatforms()
    {
        $platform = $this->getMockPlatform();
        $this->vex->addPlatforms(array($platform));

        $this->assertSame($platform, $this->vex->getPlatform());
    }

    public function testUsing()
    {
        $platform1 = $this->getMockPlatform('test1');
        $platform2 = $this->getMockPlatform('test2');
        $this->vex->addPlatforms(array($platform1, $platform2));

        $this->assertSame($platform1, $this->vex->getPlatform());

        $this->vex->using('test1');
        $this->assertSame($platform1, $this->vex->getPlatform());

        $this->vex->using('test2');
        $this->assertSame($platform2, $this->vex->getPlatform());

        $this->vex->using('test1');
        $this->assertSame($platform1, $this->vex->getPlatform());

        $this->vex->using('non_existant');
        $this->assertSame($platform1, $this->vex->getPlatform());

        $this->vex->using(null);
        $this->assertSame($platform1, $this->vex->getPlatform());

        $this->vex->using('');
        $this->assertSame($platform1, $this->vex->getPlatform());
    }

    public function testGetPlatforms()
    {
        $platform1 = $this->getMockPlatform('test1');
        $platform2 = $this->getMockPlatform('test2');

        $this->vex->addPlatforms(array($platform1, $platform2));
        $result = $this->vex->getPlatforms();

        $expected = array(
            'test1' => $platform1,
            'test2' => $platform2
        );

        $this->assertSame($expected, $result);
        $this->assertArrayHasKey('test1', $result);
        $this->assertArrayHasKey('test2', $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetplatform()
    {
        $this->vex->getPlatform();
    }

    public function testGetPlatformWithMultiplePlatformsReturnsTheFirstOne()
    {
        $platform1 = $this->getMockPlatform('test1');
        $platform2 = $this->getMockPlatform('test2');
        $platform3 = $this->getMockPlatform('test3');
        $this->vex->addPlatforms(array($platform1, $platform2, $platform3));

        $this->assertSame($platform1, $this->vex->getPlatform());
    }

    /**
     * @dataProvider emptyDataProvider
     */
    public function testEmpty($url)
    {
        $platform = $this->getMockPlatform();
        $platform->expects($this->never())->method('extract');

        $this->vex->addPlatform($platform);

        $this->assertNull($this->vex->extract($url));
    }

    public function testExtract()
    {
        $platform = $this->getMockPlatform();
        $platform
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo('some url'))
            ->will($this->returnValue(array(
                'title' => 'foo',
                'link'  => 'some url'
            )));
        $this->vex->addPlatform($platform);
        $video = $this->vex->extract('some url');

        $this->assertInstanceof('\Vex\Result\Video', $video);
        $this->assertEquals('foo', $video->getTitle());
        $this->assertEquals('some url', $video->getLink());
        $this->assertNull($video->getDuration());
    }


    protected function getMockPlatform($name = 'test_platform')
    {
        $platform = $this->getMock('Vex\Platform\PlatformInterface');
        $platform
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $platform;
    }

    public function emptyDataProvider()
    {
        return array(
            array(''),
            array(null),
            array(false),
        );
    }
}
