<?php

namespace Kevintweber\Gauges\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Kevintweber\Gauges\Factory;
use Monolog\Logger;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $logger = new Logger('testing');
        $request = Factory::createRequest('fake-token', array(), $logger);
        $this->assertInstanceOf('Kevintweber\Gauges\Request', $request);
    }

    public function testCreateMockRequest()
    {
        $response = new Response(200, array(),
                                 Stream::factory('{"test":"fake"}'));
        $request = Factory::createMockRequest($response);
        $this->assertInstanceOf('Kevintweber\Gauges\Request', $request);
    }
}