<?php

namespace Kevintweber\Gauges;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerInterface;

/**
 * A helper class to make creating requests easy.
 */
class Factory
{
    /**
     * Will return a fully built Request object.
     *
     * @param string          $token
     * @param array           $httpDefaults (Optional)
     * @param LoggerInterface $logger       (Optional)
     * @param string          $format       (Optional log format)
     *
     * @return Request
     */
    public static function createRequest($token,
                                         array $httpDefaults = array(),
                                         LoggerInterface $logger = null,
                                         $format = null)
    {
        $request = new Request($token, $httpDefaults);

        if ($logger instanceof LoggerInterface) {
            $request->setLogger($logger);
        }

        if ($format !== null) {
            $request->setMessageFormatter(
                new MessageFormatter($format)
            );
        }

        return $request;
    }
}
