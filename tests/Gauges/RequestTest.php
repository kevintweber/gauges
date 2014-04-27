<?php

namespace Kevintweber\Gauges\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Kevintweber\Gauges\Factory;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected static $testHandler;

    public static function setUpBeforeClass()
    {
        self::$testHandler = new TestHandler();
    }

    public function testGetClientEmitter()
    {
        $request = $this->buildRequest(200);
        $emitter = $request->getClientEmitter();
        $this->assertInstanceOf('GuzzleHttp\Event\Emitter', $emitter);
    }

    public function testMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->me();
        $this->assertInstanceOf('GuzzleHttp\Message\Response', $response);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/me");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testUpdateMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->update_me('Kevin', 'Weber');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/me?first_name=Kevin&last_name=Weber");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT');

        $response = $request->update_me(null, 'Weber');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/me?last_name=Weber");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT');
    }

    public function testListClients()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_clients();
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testCreateClient()
    {
        $request = $this->buildRequest(200);
        $response = $request->create_client();
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST');

        $response = $request->create_client('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients?description=asdf");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST');
    }

    public function testDeleteClients()
    {
        $request = $this->buildRequest(200);
        $response = $request->delete_client('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients/asdf");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'DELETE');
    }

    public function testListGauges()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_gauges();
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->list_gauges(3);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges?page=3");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testCreateGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->create_gauge('asdf', 'America/New_York');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST');

        $response = $request->create_gauge('asdf', 'America/New_York', 'all,none');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York&allowed_hosts=all%2Cnone");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST');
    }

    public function testGaugeDetail()
    {
        $request = $this->buildRequest(200);
        $response = $request->gauge_detail('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testUpdateGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->update_gauge('asdf1', 'asdf2', 'America/New_York');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT');

        $response = $request->update_gauge('asdf1', 'asdf2',
                                           'America/New_York', 'all,none');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York&allowed_hosts=all%2Cnone");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT');
    }

    public function testDeleteGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->delete_gauge('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'DELETE');
    }

    public function testListShares()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_shares('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/shares");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testShareGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->share_gauge('asdf', 'kevintweber@gmail.com');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/shares?email=kevintweber%40gmail.com");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST');
    }

    public function testUnshareGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->unshare_gauge('asdf', 'kevintweber');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/shares/kevintweber");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'DELETE');
    }

    public function testTopContent()
    {
        $request = $this->buildRequest(200);
        $response = $request->top_content('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/content");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->top_content('asdf', '2014-01-01', 3);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/content?date=2014-01-01&page=3");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->top_content('asdf', null, 2);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/content?page=2");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testTopReferrers()
    {
        $request = $this->buildRequest(200);
        $response = $request->top_referrers('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/referrers");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->top_referrers('asdf', '2014-01-01', 3);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/referrers?date=2014-01-01&page=3");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->top_referrers('asdf', null, 2);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/referrers?page=2");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testTraffic()
    {
        $request = $this->buildRequest(200);
        $response = $request->traffic('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/traffic");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->traffic('asdf', '2014-01-01');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/traffic?date=2014-01-01");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testBrowserResolutions()
    {
        $request = $this->buildRequest(200);
        $response = $request->browser_resolutions('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/resolutions");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->browser_resolutions('asdf', '2014-01-01');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/resolutions?date=2014-01-01");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testTechnology()
    {
        $request = $this->buildRequest(200);
        $response = $request->technology('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/technology");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->technology('asdf', '2014-01-01');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/technology?date=2014-01-01");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testSearchTerms()
    {
        $request = $this->buildRequest(200);
        $response = $request->search_terms('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/terms");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->search_terms('asdf', '2014-01-01', 3);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/terms?date=2014-01-01&page=3");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->search_terms('asdf', null, 3);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/terms?page=3");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testSearchEngines()
    {
        $request = $this->buildRequest(200);
        $response = $request->search_engines('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/engines");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->search_engines('asdf', '2014-01-01');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/engines?date=2014-01-01");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    public function testLocations()
    {
        $request = $this->buildRequest(200);
        $response = $request->locations('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/locations");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');

        $response = $request->locations('asdf', '2014-01-01');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/locations?date=2014-01-01");

        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET');
    }

    /**
     * Helper function for testing requests.
     *
     * @param int   $statusCode
     * @param mixed $body
     *
     * @return Request
     */
    protected function buildRequest($statusCode, $body = null)
    {
        if ($body === null) {
            $body = '{"test":"fake"}';
        }

        $responseBody = $body;
        if (is_array($body)) {
            $responseBody = json_encode($body);
        }

        $response = new Response($statusCode, array(),
                                 Stream::factory($responseBody));

        $logger = new Logger('testing');
        $logger->pushHandler(self::$testHandler);

        return Factory::createMockRequest($response, $logger, '{method}');
    }

    /**
     * Helper method for retrieving log messages.
     *
     * @return string
     */
    protected function getLastLoggingMessage()
    {
        $records = self::$testHandler->getRecords();

        $record = end($records);
        return $record['message'];
    }
}