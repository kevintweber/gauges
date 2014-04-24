<?php

namespace kevintweber\Gauges;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class Request
{
    /** @var array */
    protected $httpDefaults;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $token;

    /**
     * Constructor
     */
    public function __construct($token,
                                array $httpDefaults = array(),
                                LoggerInterface $logger = null)
    {
        $this->httpDefaults = $httpDefaults;
        $this->logger = $logger;
        $this->token = $token;
    }

    public function me()
    {
        return $this->makeApiCall('get', 'me');
    }

    public function update_me($first_name = null, $last_name = null)
    {
        $params = array();

        if (isset($first_name)) {
            $params['first_name'] = (string) $first_name;
        }

        if (isset($last_name)) {
            $params['last_name'] = (string) $last_name;
        }

        return $this->makeApiCall('put', 'me', $params);
    }

    public function list_clients()
    {
        return $this->makeApiCall('get', 'clients');
    }

    public function create_client($description = null)
    {
        $params = array();

        if (isset($description)) {
            $params['description'] = (string) $description;
        }

        return $this->makeApiCall('post', 'clients', $params);
    }

    protected function makeApiCall($method, $path, array $params = array())
    {
        // Validate method.
        $method = strtoupper($method);
        if ($method != 'GET' &&
            $method != 'POST' &&
            $method != 'PUT' &&
            $method != 'DELETE') {
            throw new \InvalidArgumentException('Invalid method: ' . $method);
        }

        // Validate path.
        if ($path[0] != '/') {
            $path = '/' . $path;
        }

        // Make API call.
        $client = new Client(
            array(
                'base_url' => array('https://secure.gaug.es'),
                'defaults' => $this->httpDefaults
            )
        );

        $request = $client->createRequest(
            $method,
            $path,
            array('headers' => array('X-Gauges-Token' => $this->token))
        );

        $response = $client->send($request);

        /// @todo
        if ($this->logger) {
            $this->logger->debug('');
        }
    }
}