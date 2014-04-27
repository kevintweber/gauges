<?php

namespace Kevintweber\Gauges\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Kevintweber\Gauges\Factory;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $request = Factory::createRequest('fake-token');
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