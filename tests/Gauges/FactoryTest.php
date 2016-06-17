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
        $request = Factory::createRequest('fake-token', array(), $logger, 'whoa');
        $this->assertInstanceOf('Kevintweber\Gauges\Request', $request);
    }
}