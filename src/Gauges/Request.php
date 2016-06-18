<?php

namespace Kevintweber\Gauges;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Used to make Gauges API calls.
 */
class Request implements LoggerAwareInterface
{
    const URI = 'https://secure.gaug.es/';

    /** @var null|ClientInterface */
    private $client;

    /** @var HandlerStack */
    private $handlerStack;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $logLevel;

    /** @var MessageFormatter */
    private $messageFormatter;

    /** @var array */
    private $options;

    /** @var string */
    protected $token;

    /**
     * Constructor
     *
     * @param string $token     Your API token
     * @param array  $options   See Guzzle documentation (proxy, etc.)
     */
    public function __construct($token, array $options = array())
    {
        $this->client = null;
        $this->handlerStack = HandlerStack::create();
        $this->logger = null;
        $this->logLevel = LogLevel::INFO;
        $this->messageFormatter = new MessageFormatter('{req_body} - {res_body}');
        $this->options = array_merge(
            array('timeout' => 10),
            $options
        );
        $this->options['base_uri'] = self::URI;
        $this->token = $token;
    }

    /**
     * Getter for the HTTP client.
     *
     * @return Client
     */
    protected function getHttpClient()
    {
        if ($this->client === null) {
            if ($this->logger instanceof LoggerInterface) {
                $this->handlerStack->push(
                    Middleware::log(
                        $this->logger,
                        $this->messageFormatter,
                        $this->logLevel
                    )
                );
            }

            $this->options['handler'] = $this->handlerStack;
            $this->client = new Client($this->options);
        }

        return $this->client;
    }

    /**
     * Setter for the Guzzle HandlerStack
     */
    public function setHandlerStack(HandlerStack $handlerStack)
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    /**
     * Setter for the Guzzle MessageFormatter
     */
    public function setMessageFormatter(MessageFormatter $messageFormatter)
    {
        $this->messageFormatter = $messageFormatter;

        return $this;
    }

    /**
     * Get Your Information
     *
     * Returns your information.
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function me()
    {
        return $this->makeApiCall('GET', 'me');
    }

    /**
     * Update Your Information
     *
     * Updates and returns your information with the updates applied.
     *
     * @param string $first_name Your first name.
     * @param string $last_name  Your last name.
     *
     * @return GuzzleHttp\Psr7\Response
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

        return $this->makeApiCall('PUT', 'me', $params);
    }

    /**
     * API Client List
     *
     * Returns an array of your API clients.
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function list_clients()
    {
        return $this->makeApiCall('GET', 'clients');
    }

    /**
     * Creating an API Client
     *
     * Creates an API client, which can be used to authenticate against
     * the Gaug.es API.
     *
     * @param string $description Short description for the key
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function create_client($description = null)
    {
        $params = array();

        if (isset($description)) {
            $params['description'] = (string) $description;
        }

        return $this->makeApiCall('POST', 'clients', $params);
    }

    /**
     * Delete an API Client
     *
     * Permanently deletes an API client key.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function delete_client($id)
    {
        return $this->makeApiCall('DELETE', 'clients/' . $id);
    }

    /**
     * Gauges List
     *
     * Returns an array of your gauges, with recent traffic included.
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function list_gauges($page = null)
    {
        $params = array();

        if (isset($page)) {
            $params['page'] = (int) $page;
        }

        return $this->makeApiCall('GET', 'gauges', $params);
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
     * @return GuzzleHttp\Psr7\Response
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

        return $this->makeApiCall('POST', 'gauges', $params);
    }

    /**
     * Gauge Detail
     *
     * Gets details for a gauge.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function gauge_detail($id)
    {
        return $this->makeApiCall('GET', 'gauges/' . $id);
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
     * @return GuzzleHttp\Psr7\Response
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

        return $this->makeApiCall('PUT', 'gauges/' . $id, $params);
    }

    /**
     * Delete a Gauge
     *
     * Permanently deletes a gauge.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function delete_gauge($id)
    {
        return $this->makeApiCall('DELETE', 'gauges/' . $id);
    }

    /**
     * List Shares
     *
     * Lists the people that have access to a Gauge.
     *
     * @param string $id
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function list_shares($id)
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/shares');
    }

    /**
     * Share a Gauge
     *
     * Shares gauge with a person by their email. Any valid email will work
     * and will receive an invite even if there is no existing Gauges user
     * with that email.
     *
     * @param string $id
     * @param string $email
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function share_gauge($id, $email)
    {
        $params = array(
            'email' => $email
        );

        return $this->makeApiCall('POST', 'gauges/' . $id . '/shares', $params);
    }

    /**
     * Un-share Gauge
     *
     * @param string $id
     * @param string $user_id
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function unshare_gauge($id, $user_id)
    {
        return $this->makeApiCall('DELETE', 'gauges/' . $id . '/shares/' . $user_id);
    }

    /**
     * Top Content
     *
     * Gets top content for a gauge, paginated.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     * @param int    $page (Optional)
     *
     * @return GuzzleHttp\Psr7\Response
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

        return $this->makeApiCall('GET', 'gauges/' . $id . '/content', $params);
    }

    /**
     * Top Referrers
     *
     * Gets top referrers for a gauge, paginated.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     * @param int    $page (Optional)
     *
     * @return GuzzleHttp\Psr7\Response
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

        return $this->makeApiCall('GET', 'gauges/' . $id . '/referrers', $params);
    }

    /**
     * Traffic
     *
     * Gets traffic for a gauge.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function traffic($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall('GET', 'gauges/' . $id . '/traffic', $params);
    }

    /**
     * Browser Resolutions
     *
     * Gets browsers heights, browser widths, and screen widths for a gauge.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function browser_resolutions($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall('GET', 'gauges/' . $id . '/resolutions', $params);
    }

    /**
     * Technology
     *
     * Gets browsers and platforms for a gauge.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function technology($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall('GET', 'gauges/' . $id . '/technology', $params);
    }

    /**
     * Search Terms
     *
     * Gets search terms for a gauge, paginated.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     * @param int    $page (Optional)
     *
     * @return GuzzleHttp\Psr7\Response
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

        return $this->makeApiCall('GET', 'gauges/' . $id . '/terms', $params);
    }

    /**
     * Search Engines
     *
     * Gets search engines for a gauge.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function search_engines($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall('GET', 'gauges/' . $id . '/engines', $params);
    }

    /**
     * Locations
     *
     * Gets locations for a gauge.
     *
     * @param string $id
     * @param string $date (Optional) Date in format YYYY-MM-DD
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function locations($id, $date = null)
    {
        $params = array();

        if (isset($date)) {
            $params['date'] = (string) $date;
        }

        return $this->makeApiCall('GET', 'gauges/' . $id . '/locations', $params);
    }

    /**
     * Make the actual gauges API call.
     *
     * @param string $method       [GET|POST|PUT|DELETE]
     * @param string $path
     * @param array  $params
     *
     * @return GuzzleHttp\Psr7\Response
     */
    protected function makeApiCall($method, $path, array $params = array())
    {
        // Format method.
        $method = strtoupper($method);

        // Make API call.
        return $this->getHttpClient()->request(
            $method,
            $path,
            array(
                'headers' => array('X-Gauges-Token' => $this->token),
                'query' => $params
            )
        );
    }
}
