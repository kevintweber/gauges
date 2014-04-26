<?php

namespace Kevintweber\Gauges\Tests;

use Kevintweber\Gauges\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $request = Factory::createRequest('fake-token');
        $this->assertInstanceOf('Kevintweber\Gauges\Request', $request);
    }
}