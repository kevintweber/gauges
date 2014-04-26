<?php

namespace kevintweber\Gauges;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Used to make Gauges API calls.
 */
class Request
{
    const URL = 'https://secure.gaug.es';

    /** @var array */
    protected $httpDefaults;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $token;

    /**
     * Constructor
     *
     * @param string          $token        Your API token
     * @param array           $httpDefaults See Guzzle documentation (proxy, etc.)
     * @param LoggerInterface $logger       (Optional) A logging service.
     */
    public function __construct($token,
                                array $httpDefaults = array(),
                                LoggerInterface $logger = null)
    {
        $this->httpDefaults = $httpDefaults;
        $this->logger = $logger;
        $this->token = $token;
    }

    /**
     * Get Your Information
     *
     * Returns your information.
     *
     * @return GuzzleHttp\Message\Response
     */
    public function me()
    {
        return $this->makeApiCall(__FUNCTION__, 'GET', 'me');
    }

    /**
     * Update Your Information
     *
     * Updates and returns your information with the updates applied.
     *
     * @param string $first_name Your first name.
     * @param string $last_name  Your last name.
     *
     * @return GuzzleHttp\Message\Response
     */
    public function update_me($first_name = null, $last_name = null)
    {
        $params = array();

        if (isset($first_name)) {
            $params['first_name'] = (string) $first_name;
        }

        if (isset($last_name)) {
            $params['last_name'] = (string) $last_name;
        }

        return $this->makeApiCall(__FUNCTION__, 'PUT', 'me', $params);
    }

    /**
     * API Client List
     *
     * Returns an array of your API clients.
     *
     * @return GuzzleHttp\Message\Response
     */
    public function list_clients()
    {
        return $this->makeApiCall(__FUNCTION__, 'GET', 'clients');
    }

    /**
     * Creating an API Client
     *
     * Creates an API client, which can be used to authenticate against
     * the Gaug.es API.
     *
     * @param string $description Short description for the key
     *
     * @return GuzzleHttp\Message\Response
     */
    public function create_client($description = null)
    {
        $params = array();

        if (isset($description)) {
            $params['description'] = (string) $description;
        }

        return $this->makeApiCall(__FUNCTION__, 'POST', 'clients', $params);
    }

    /**
     * Delete an API Client
     *
     * Permanently deletes an API client key.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Message\Response
     */
    public function delete_client($id)
    {
        return $this->makeApiCall(__FUNCTION__, 'DELETE', 'clients/' . $id);
    }

    /**
     * Gauges List
     *
     * Returns an array of your gauges, with recent traffic included.
     *
     * @return GuzzleHttp\Message\Response
     */
    public function list_gauges($page = null)
    {
        $params = array();

        if (isset($page)) {
            $params['page'] = (int) $page;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET', 'gauges', $params);
    }

    /**
     * Create a New Gauge
     *
     * Creates a gauge.
     *
     * @param string $title
     * @param string $tz
     * @param string $allowedHosts (Optional)
     *
     * @return GuzzleHttp\Message\Response
     */
    public function create_gauge($title, $tz, $allowedHosts = null)
    {
        $params = array(
            'title' => $title,
            'tz' => $tz
        );

        if (isset($allowedHosts)) {
            $params['allowed_hosts'] = (string) $allowedHosts;
        }

        return $this->makeApiCall(__FUNCTION__, 'POST', 'gauges', $params);
    }

    /**
     * Gauge Detail
     *
     * Gets details for a gauge.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Message\Response
     */
    public function gauge_detail($id)
    {
        return $this->makeApiCall(__FUNCTION__, 'GET', 'gauges/' . $id);
    }

    /**
     * Update a Gauge
     *
     * Updates and returns a gauge with the updates applied.
     *
     * @param string $id
     * @param string $title
     * @param string $tz
     * @param string $allowedHosts (Optional)
     *
     * @return GuzzleHttp\Message\Response
     */
    public function update_gauge($id, $title, $tz, $allowedHosts = null)
    {
        $params = array(
            'title' => $title,
            'tz' => $tz
        );

        if (isset($allowedHosts)) {
            $params['allowed_hosts'] = (string) $allowedHosts;
        }

        return $this->makeApiCall(__FUNCTION__, 'PUT', 'gauges/' . $id, $params);
    }

    /**
     * Delete a Gauge
     *
     * Permanently deletes a gauge.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Message\Response
     */
    public function delete_gauge($id)
    {
        return $this->makeApiCall(__FUNCTION__, 'DELETE', 'gauges/' . $id);
    }

    /**
     * List Shares
     *
     * Lists the people that have access to a Gauge.
     *
     * @param string id
     *
     * @return GuzzleHttp\Message\Response
     */
    public function list_shares($id)
    {
        return $this->makeApiCall(__FUNCTION__, 'GET', 'gauges/' . $id . '/shares');
    }

    /**
     * Share a Gauge
     *
     * Shares gauge with a person by their email. Any valid email will work
     * and will receive an invite even if there is no existing Gauges user
     * with that email.
     *
     * @param string id
     * @param string email
     *
     * @return GuzzleHttp\Message\Response
     */
    public function share_gauge($id, $email)
    {
        $params = array(
            'email' => $email
        );

        return $this->makeApiCall(__FUNCTION__, 'POST', 'gauges/' . $id . '/shares',
                                  $params);
    }

    /**
     * Un-share Gauge
     *
     * @param string id
     * @param string user_id
     *
     * @return GuzzleHttp\Message\Response
     */
    public function unshare_gauge($id, $user_id)
    {
        return $this->makeApiCall(__FUNCTION__, 'DELETE',
                                  'gauges/' . $id . '/shares/' . $user_id);
    }

    /**
     * Top Content
     *
     * Gets top content for a gauge, paginated.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     * @param string page (Optional)
     *
     * @return GuzzleHttp\Message\Response
     */
    public function top_content($id, $date = null, $page = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        if (isset($page)) {
            $params['page'] = (int) $page;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/content', $params);
    }

    /**
     * Top Referrers
     *
     * Gets top referrers for a gauge, paginated.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     * @param string page (Optional)
     *
     * @return GuzzleHttp\Message\Response
     */
    public function top_referrers($id, $date = null, $page = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        if (isset($page)) {
            $params['page'] = (int) $page;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/referrers', $params);
    }

    /**
     * Traffic
     *
     * Gets traffic for a gauge.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Message\Response
     */
    public function traffic($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/traffic', $params);
    }

    /**
     * Browser Resolutions
     *
     * Gets browsers heights, browser widths, and screen widths for a gauge.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Message\Response
     */
    public function browser_resolutions($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/resolutions', $params);
    }

    /**
     * Technology
     *
     * Gets browsers and platforms for a gauge.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Message\Response
     */
    public function technology($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/technology', $params);
    }

    /**
     * Search Terms
     *
     * Gets search terms for a gauge, paginated.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     * @param string page (Optional)
     *
     * @return GuzzleHttp\Message\Response
     */
    public function search_terms($id, $date = null, $page = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        if (isset($page)) {
            $params['page'] = (int) $page;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/terms', $params);
    }

    /**
     * Search Engines
     *
     * Gets search engines for a gauge.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Message\Response
     */
    public function search_terms($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/engines', $params);
    }

    /**
     * Locations
     *
     * Gets locations for a gauge.
     *
     * @param string id
     * @param string date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Message\Response
     */
    public function search_terms($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall(__FUNCTION__, 'GET',
                                  'gauges/' . $id . '/locations', $params);
    }

    /**
     * Make the actual gauges API call.
     *
     * @param string $functionName The calling function name.
     * @param string $method       [GET|POST|PUT|DELETE]
     * @param string $path
     * @param array  $params
     *
     * @return GuzzleHttp\Message\Response
     */
    protected function makeApiCall($functionName,
                                   $method,
                                   $path,
                                   array $params = array())
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
                'base_url' => array(self::URL),
                'defaults' => $this->httpDefaults
            )
        );

        $request = $client->createRequest(
            $method,
            $path,
            array('headers' => array('X-Gauges-Token' => $this->token))
        );
        $request->setQuery($params);

        $response = $client->send($request);

        // Log the message (if the logger is present).
        if ($this->logger) {
            if ($response->getStatusCode() == 200) {
                $message = 'successful.';
            } else {
                $message = 'unsuccessful. (status=' . $response->getStatusCode() .
                    ')';
            }

            $this->logger->debug('Gauges (' . self::URL . '): ' . $functionName .
                                 ' request ' . $message);
        }

        return $response;
    }
}