<?php

namespace Kevintweber\Gauges\Tests;

use Kevintweber\Gauges\Factory;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testCreateRequest()
    {
        $logger = new Logger('testing');
        $request = Factory::createRequest('fake-token', array(), $logger, 'whoa');
        $this->assertInstanceOf('Kevintweber\Gauges\Request', $request);
    }
}