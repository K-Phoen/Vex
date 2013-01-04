<?php

namespace Vex\Tests\HttpAdapter;

use Vex\HttpAdapter\CurlHttpAdapter;


/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CurlHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('cURL has to be enabled.');
        }
    }

    public function testGetName()
    {
        $buzz = new CurlHttpAdapter();
        $this->assertEquals('curl', $buzz->getName());
    }

    public function testGetNullContent()
    {
        $curl = new CurlHttpAdapter();
        $this->assertNull($curl->getContent(null));
    }

    public function testGetFalseContent()
    {
        $curl = new CurlHttpAdapter();
        $this->assertNull($curl->getContent(null));
    }
}
