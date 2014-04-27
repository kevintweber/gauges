<?php

namespace Kevintweber\Gauges;

use GuzzleHttp\Adapter\MockAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
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
        // Create client.
        $client = new Client(
            array(
                'base_url' => Request::URL,
                'defaults' => $httpDefaults
            )
        );

        // Attaching logging subscriber (if available).
        if ($logger !== null) {
            if ($format === null) {
                $format = Formatter::CLF;
            }

            $subscriber = new LogSubscriber($logger, $format);

            $emitter = $client->getEmitter();
            $emitter->attach($subscriber);
        }

        // Create request.
        $request = new Request($token, $httpDefaults);
        $request->setHttpClient($client);

        return $request;
    }

    /**
     * Factory method used for testing.
     *
     * @param Response $response
     *
     * @return Request
     */
    static public function createMockRequest(Response $response,
                                             LoggerInterface $logger = null,
                                             $format = null)
    {
        // Create client.
        $client = new Client(
            array(
                'adapter'  => new MockAdapter($response),
                'base_url' => Request::URL
            )
        );

        // Attaching logging subscriber (if available).
        if ($logger !== null) {
            if ($format === null) {
                $format = Formatter::DEBUG;
            }

            $subscriber = new LogSubscriber($logger, $format);

            $emitter = $client->getEmitter();
            $emitter->attach($subscriber);
        }

        $request = new Request('fake_token');
        $request->setHttpClient($client);

        return $request;
    }
}