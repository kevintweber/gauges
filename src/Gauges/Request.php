<?php

namespace kevintweber\Gauges;

class Request
{
    /** @var string */
    protected $token;

    /**
     * Constructor
     */
    public function __construct($token)
    {
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
        $method = strtolower($method);
        if ($method != 'get' &&
            $method != 'post' &&
            $method != 'put' &&
            $method != 'delete') {
            throw new \InvalidArgumentException('Invalid method: ' . $method);
        }

        /// @todo
        return new Response(200, '{}');
    }
}