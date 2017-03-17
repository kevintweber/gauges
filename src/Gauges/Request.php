<?php

namespace Kevintweber\Gauges;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Response;
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

    /** @var null|LoggerInterface */
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
    public function __construct(string $token, array $options = array())
    {
        $this->client = null;
        $this->handlerStack = HandlerStack::create();
        $this->logger = null;
        $this->logLevel = LogLevel::INFO;
        $this->messageFormatter = new MessageFormatter();
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
    protected function getHttpClient() : Client
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
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setLogLevel(string $logLevel)
    {
        $logLevel = strtolower($logLevel);
        if ($logLevel !== LogLevel::ALERT &&
            $logLevel !== LogLevel::CRITICAL &&
            $logLevel !== LogLevel::DEBUG &&
            $logLevel !== LogLevel::EMERGENCY &&
            $logLevel !== LogLevel::ERROR &&
            $logLevel !== LogLevel::INFO &&
            $logLevel !== LogLevel::NOTICE &&
            $logLevel !== LogLevel::WARNING) {
            throw new \InvalidArgumentException('Invalid log level: ' . $logLevel);
        }

        $this->logLevel = $logLevel;
    }

    /**
     * Setter for the Guzzle MessageFormatter
     */
    public function setMessageFormatter(MessageFormatter $messageFormatter)
    {
        $this->messageFormatter = $messageFormatter;
    }

    /**
     * Get Your Information
     *
     * Returns your information.
     *
     * @return Response
     */
    public function me() : Response
    {
        return $this->makeApiCall('GET', 'me');
    }

    /**
     * Update Your Information
     *
     * Updates and returns your information with the updates applied.
     *
     * @param string $first_name Your first name. (Optional)
     * @param string $last_name  Your last name. (Optional)
     *
     * @return Response
     */
    public function updateMe(string $first_name = null, string $last_name = null) : Response
    {
        $params = array();
        if (isset($first_name)) {
            $params['first_name'] = $first_name;
        }

        if (isset($last_name)) {
            $params['last_name'] = $last_name;
        }

        return $this->makeApiCall('PUT', 'me', $params);
    }

    /**
     * API Client List
     *
     * Returns an array of your API clients.
     *
     * @return Response
     */
    public function listClients() : Response
    {
        return $this->makeApiCall('GET', 'clients');
    }

    /**
     * Creating an API Client
     *
     * Creates an API client, which can be used to authenticate against
     * the Gaug.es API.
     *
     * @param string $description Short description for the key (Optional)
     *
     * @return Response
     */
    public function createClient(string $description = null) : Response
    {
        $params = array();
        if (isset($description)) {
            $params['description'] = $description;
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
     * @return Response
     */
    public function deleteClient(string $id) : Response
    {
        return $this->makeApiCall('DELETE', 'clients/' . $id);
    }

    /**
     * Gauges List
     *
     * Returns an array of your gauges, with recent traffic included.
     *
     * @param int $page Page number (Optional)
     *
     * @return Response
     */
    public function listGauges(int $page = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges', $this->formatCommonParameters(null, $page));
    }

    /**
     * Create a New Gauge
     *
     * Creates a gauge.
     *
     * @param string               $title
     * @param string|\DateTimeZone $tz
     * @param string               $allowedHosts (Optional)
     *
     * @return Response
     */
    public function createGauge(string $title, $tz, string $allowedHosts = null) : Response
    {
        return $this->makeApiCall('POST', 'gauges', $this->formatGaugeParameters($title, $tz, $allowedHosts));
    }

    /**
     * Gauge Detail
     *
     * Gets details for a gauge.
     *
     * @param string $id
     *
     * @return Response
     */
    public function gaugeDetail(string $id) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id);
    }

    /**
     * Update a Gauge
     *
     * Updates and returns a gauge with the updates applied.
     *
     * @param string               $id
     * @param string               $title
     * @param string|\DateTimeZone $tz
     * @param string               $allowedHosts (Optional)
     *
     * @return Response
     */
    public function updateGauge(string $id, string $title, $tz, string $allowedHosts = null) : Response
    {
        return $this->makeApiCall('PUT', 'gauges/' . $id, $this->formatGaugeParameters($title, $tz, $allowedHosts));
    }

    /**
     * Delete a Gauge
     *
     * Permanently deletes a gauge.
     *
     * @param string $id
     *
     * @return Response
     */
    public function deleteGauge(string $id) : Response
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
     * @return Response
     */
    public function listShares(string $id) : Response
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
     * @return Response
     */
    public function shareGauge(string $id, string $email) : Response
    {
        $params = array(
            'email' => $email
        );

        return $this->makeApiCall('POST', 'gauges/' . $id . '/shares', $params);
    }

    /**
     * Top Content
     *
     * Gets top content for a gauge, paginated.
     *
     * @param string           $id
     * @param string|\DateTime $date  (Optional) Date in format YYYY-MM-DD
     * @param string           $group (Optional) Either "day" or "month".  Default is "day".
     * @param int              $page  (Optional)
     *
     * @return Response
     */
    public function topContent(string $id, $date = null, string $group = null, int $page = null) : Response
    {
        $params = $this->formatCommonParameters($date, $page);
        if (isset($group)) {
            $group = strtolower($group);
            if ($group !== 'month' && $group !== 'day') {
                throw new \InvalidArgumentException(
                    'Invalid group parameter for "topContent" call.  Allowed values are "day" or "month".  Actual value is : ' . $group
                );
            }

            $params['group'] = $group;
        }

        return $this->makeApiCall('GET', 'gauges/' . $id . '/content', $params);
    }

    /**
     * Un-share Gauge
     *
     * @param string $id
     * @param string $user_id
     *
     * @return Response
     */
    public function unshareGauge(string $id, string $user_id) : Response
    {
        return $this->makeApiCall('DELETE', 'gauges/' . $id . '/shares/' . $user_id);
    }

    /**
     * Top Referrers
     *
     * Gets top referrers for a gauge, paginated.
     *
     * @param string           $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     * @param int              $page (Optional)
     *
     * @return Response
     */
    public function topReferrers(string $id, $date = null, int $page = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/referrers', $this->formatCommonParameters($date, $page));
    }

    /**
     * Traffic
     *
     * Gets traffic for a gauge.
     *
     * @param string           $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     *
     * @return Response
     */
    public function traffic(string $id, $date = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/traffic', $this->formatCommonParameters($date));
    }

    /**
     * Browser Resolutions
     *
     * Gets browsers heights, browser widths, and screen widths for a gauge.
     *
     * @param string $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     *
     * @return Response
     */
    public function browserResolutions(string $id, $date = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/resolutions', $this->formatCommonParameters($date));
    }

    /**
     * Technology
     *
     * Gets browsers and platforms for a gauge.
     *
     * @param string           $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     *
     * @return Response
     */
    public function technology(string $id, $date = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/technology', $this->formatCommonParameters($date));
    }

    /**
     * Search Terms
     *
     * Gets search terms for a gauge, paginated.
     *
     * @param string $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     * @param int    $page (Optional)
     *
     * @return Response
     */
    public function searchTerms(string $id, $date = null, int $page = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/terms', $this->formatCommonParameters($date, $page));
    }

    /**
     * Search Engines
     *
     * Gets search engines for a gauge.
     *
     * @param string $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     *
     * @return Response
     */
    public function searchEngines(string $id, $date = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/engines', $this->formatCommonParameters($date));
    }

    /**
     * Locations
     *
     * Gets locations for a gauge.
     *
     * @param string $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     *
     * @return Response
     */
    public function locations(string $id, $date = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/locations', $this->formatCommonParameters($date));
    }

    /**
     * Browser stats
     *
     * Get the browser statistics in a format used with the browserlist module.
     * (See https://github.com/ai/browserslist)
     *
     * @param string $id
     * @param string|\DateTime $date (Optional) Date in format YYYY-MM-DD
     *
     * @return Response
     */
    public function browserStats(string $id, $date = null) : Response
    {
        return $this->makeApiCall('GET', 'gauges/' . $id . '/browserstats', $this->formatCommonParameters($date));
    }

    /**
     * Make the actual gauges API call.
     *
     * @param string $method       [GET|POST|PUT|DELETE]
     * @param string $path
     * @param array  $params
     *
     * @return Response
     */
    protected function makeApiCall(string $method, string $path, array $params = array()) : Response
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

    private function formatCommonParameters($date = null, int $page = null) : array
    {
        $params = array();
        if (isset($date)) {
            if (!$date instanceof \DateTime) {
                $date = new \DateTime($date);
            }

            $params['date'] = $date->format('Y-m-d');
        }

        if (isset($page)) {
            $params['page'] = $page;
        }

        return $params;
    }

    private function formatGaugeParameters(string $title, $tz, string $allowedHosts = null) : array
    {
        if (empty($title)) {
            throw new \InvalidArgumentException('Gauge title must not be empty.');
        }

        if (!$tz instanceof \DateTimeZone) {
            $tz = new \DateTimeZone($tz);
        }

        $params = array(
            'title' => $title,
            'tz' => $tz->getName()
        );
        if (isset($allowedHosts)) {
            $params['allowed_hosts'] = $allowedHosts;
        }

        return $params;
    }
}
