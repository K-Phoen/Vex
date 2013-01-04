<?php

namespace Vex\Tests\HttpAdapter;

use Vex\HttpAdapter\GuzzleHttpAdapter;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Service\Client;


/**
 * @author Michael Dowling <michael@guzzlephp.org>
 */
class GuzzleHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Guzzle\Service\Client')) {
            $this->markTestSkipped('Guzzle library has to be installed');
        }
    }

    /**
     * @covers Vex\HttpAdapter\GuzzleHttpAdapter::__construct
     */
    public function testCreatesDefaultClient()
    {
        $adapter = new GuzzleHttpAdapter();
        $this->assertEquals('guzzle', $adapter->getName());
    }

    /**
     * @covers Vex\HttpAdapter\GuzzleHttpAdapter::__construct
     * @covers Vex\HttpAdapter\GuzzleHttpAdapter::getContent
     */
    public function testRetrievesResponse()
    {
        $historyPlugin = new HistoryPlugin();
        $mockPlugin = new MockPlugin(array(new Response(200, null, 'body')));

        $client = new Client();
        $client->getEventDispatcher()->addSubscriber($mockPlugin);
        $client->getEventDispatcher()->addSubscriber($historyPlugin);

        $adapter = new GuzzleHttpAdapter($client);
        $this->assertEquals('body', $adapter->getContent('http://test.com/'));

        $this->assertEquals('http://test.com/',
            $historyPlugin->getLastRequest()->getUrl());
    }
}
