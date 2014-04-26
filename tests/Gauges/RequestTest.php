<?php

namespace Kevintweber\Gauges\Tests;

use Kevintweber\Gauges\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected static $request;

    public static function setUpBeforeClass()
    {
        self::$request = new Request('asdfsadf');
    }

    public function testMe()
    {
        $this->assertTrue(true);
    }
}