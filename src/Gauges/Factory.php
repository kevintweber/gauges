<?php

namespace Kevintweber\Gauges;

use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Psr\Log\LoggerInterface;

class Factory
{
    /**
     * Will return a fully built Request object.
     *
     * @param string          $token
     * @param array           $httpDefaults (Optional)
     * @param LoggerInterface $logger       (Optional)
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
            $request->attachLogger($logger, $format);
        }

        return $request;
    }

    /**
     * Factory method used for testing.
     *
     * @param Response $response
     *
     * @return Request
     */
    public static function createMockRequest(Response $response)
    {
        // Create mock.
        $mock = new Mock(array($response));

        // Create client.
        $client = new Client(
            array(
                'base_url' => Request::URL
            )
        );
        $client->getEmitter()->attach($mock);

        $request = new Request('fake_token');
        $request->setHttpClient($client);

        return $request;
    }
}
