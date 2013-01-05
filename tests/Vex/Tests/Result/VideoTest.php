<?php

namespace Vex\Tests\Result;

use Vex\Result\Video;


class VideoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testConstructor($data, $title, $link, $duration, $code, $thumb)
    {
        $video = new Video($data);

        $this->assertEquals($title, $video->getTitle());
        $this->assertEquals($link, $video->getLink());
        $this->assertEquals($duration, $video->getDuration());
        $this->assertEquals($code, $video->getCode());
        $this->assertEquals($thumb, $video->getThumb());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFromArray($data, $title, $link, $duration, $code, $thumb)
    {
        $video = new Video();
        $video->fromArray($data);

        $this->assertEquals($title, $video->getTitle());
        $this->assertEquals($link, $video->getLink());
        $this->assertEquals($duration, $video->getDuration());
        $this->assertEquals($code, $video->getCode());
        $this->assertEquals($thumb, $video->getThumb());
    }


    public function dataProvider()
    {
        return array(
            array(array(), null, null, null, null, null),
            array(array('title' => 'joe'), 'joe', null, null, null, null),
            array(array('link' => 'http://google.fr'), null, 'http://google.fr', null, null, null),
            array(array('duration' => 42), null, null, 42, null, null),
            array(array('duration' => '42'), null, null, 42, null, null),
            array(array('thumb' => 'foo.jpg'), null, null, null, null, 'foo.jpg'),
            array(array('embed_code' => '<code>yeah!</code>'), null, null, null, '<code>yeah!</code>', null),
            array(array('link' => 'http://google.fr', 'duration' => 42), null, 'http://google.fr', 42, null, null),
            array(array('link' => 'http://google.fr', 'duration' => 42, 'thumb' => 'foo.jpg', 'embed_code' => 'joe', 'title' => 'Foo'), 'Foo', 'http://google.fr', 42, 'joe', 'foo.jpg'),
        );
    }
}
