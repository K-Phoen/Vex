<?php

namespace Vex\Tests\HttpAdapter;

use Vex\HttpAdapter\BuzzHttpAdapter;


/**
 * @author William Durand <william.durand1@gmail.com>
 */
class BuzzHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Buzz\Browser')) {
            $this->markTestSkipped('Buzz library has to be installed');
        }
    }

    public function testGetName()
    {
        $buzz = new BuzzHttpAdapter();
        $this->assertEquals('buzz', $buzz->getName());
    }

    public function testGetNullContent()
    {
        $buzz = new BuzzHttpAdapter();
        $this->assertNull($buzz->getContent(null));
    }

    public function testGetFalseContent()
    {
        $buzz = new BuzzHttpAdapter();
        $this->assertNull($buzz->getContent(false));
    }

    public function testGetContentWithCustomBrowser()
    {
        $content = 'foobar content';
        $browser = $this->getBrowserMock($content);

        $buzz = new BuzzHttpAdapter($browser);
        $this->assertEquals($content, $buzz->getContent('http://www.example.com'));
    }

    protected function getBrowserMock($content)
    {
        $mock = $this->getMock('\Buzz\Browser');
        $mock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->getResponseMock($content)))
            ;

        return $mock;
    }

    protected function getResponseMock($content)
    {
        $mock = $this->getMock('\Buzz\Message\Response');
        $mock
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));

        return $mock;
    }
}
