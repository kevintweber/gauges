<?php

namespace Kevintweber\Gauges\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Response;
use Kevintweber\Gauges\Request;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class RequestTest extends TestCase
{
    protected static $testHandler;

    public static function setUpBeforeClass()
    {
        self::$testHandler = new TestHandler();
    }

    public function testMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->me();
        $this->assertInstanceOf('GuzzleHttp\Psr7\Response', $response);
        $this->assertEquals($response->getStatusCode(), 200);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/me');
    }

    public function testUpdateMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->update_me('Kevin', 'Weber');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT-https://secure.gaug.es/me?first_name=Kevin&last_name=Weber');

        $request = $this->buildRequest(200);
        $response = $request->update_me(null, 'Weber');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT-https://secure.gaug.es/me?last_name=Weber');
    }

    public function testListClients()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_clients();
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/clients');
    }

    public function testCreateClient()
    {
        $request = $this->buildRequest(200);
        $response = $request->create_client();
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST-https://secure.gaug.es/clients');

        $request = $this->buildRequest(200);
        $response = $request->create_client('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST-https://secure.gaug.es/clients?description=asdf');
    }

    public function testDeleteClients()
    {
        $request = $this->buildRequest(200);
        $response = $request->delete_client('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'DELETE-https://secure.gaug.es/clients/asdf');
    }

    public function testListGauges()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_gauges();
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges');

        $request = $this->buildRequest(200);
        $response = $request->list_gauges(3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges?page=3');
    }

    public function testCreateGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->create_gauge('asdf', 'America/New_York');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST-https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York');

        $request = $this->buildRequest(200);
        $response = $request->create_gauge('asdf', 'America/New_York', 'all,none');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST-https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York&allowed_hosts=all%2Cnone');
    }

    public function testGaugeDetail()
    {
        $request = $this->buildRequest(200);
        $response = $request->gauge_detail('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf');
    }

    public function testUpdateGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->update_gauge('asdf1', 'asdf2', 'America/New_York');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT-https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York');

        $request = $this->buildRequest(200);
        $response = $request->update_gauge('asdf1', 'asdf2',
                                           'America/New_York', 'all,none');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'PUT-https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York&allowed_hosts=all%2Cnone');
    }

    public function testDeleteGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->delete_gauge('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'DELETE-https://secure.gaug.es/gauges/asdf');
    }

    public function testListShares()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_shares('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/shares');
    }

    public function testShareGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->share_gauge('asdf', 'kevintweber@gmail.com');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'POST-https://secure.gaug.es/gauges/asdf/shares?email=kevintweber%40gmail.com');
    }

    public function testUnshareGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->unshare_gauge('asdf', 'kevintweber');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'DELETE-https://secure.gaug.es/gauges/asdf/shares/kevintweber');
    }

    public function testTopContent()
    {
        $request = $this->buildRequest(200);
        $response = $request->top_content('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/content');

        $request = $this->buildRequest(200);
        $response = $request->top_content('asdf', '2014-01-01', 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/content?date=2014-01-01&page=3');

        $request = $this->buildRequest(200);
        $response = $request->top_content('asdf', null, 2);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/content?page=2');

        $request = $this->buildRequest(200);
        $response = $request->top_content('asdf', null, 2, 'month');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/content?group=month&page=2');
    }

    public function testTopReferrers()
    {
        $request = $this->buildRequest(200);
        $response = $request->top_referrers('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/referrers');

        $request = $this->buildRequest(200);
        $response = $request->top_referrers('asdf', '2014-01-01', 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/referrers?date=2014-01-01&page=3');

        $request = $this->buildRequest(200);
        $response = $request->top_referrers('asdf', null, 2);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/referrers?page=2');
    }

    public function testTraffic()
    {
        $request = $this->buildRequest(200);
        $response = $request->traffic('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/traffic');

        $request = $this->buildRequest(200);
        $response = $request->traffic('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/traffic?date=2014-01-01');
    }

    public function testBrowserResolutions()
    {
        $request = $this->buildRequest(200);
        $response = $request->browser_resolutions('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/resolutions');

        $request = $this->buildRequest(200);
        $response = $request->browser_resolutions('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/resolutions?date=2014-01-01');
    }

    public function testTechnology()
    {
        $request = $this->buildRequest(200);
        $response = $request->technology('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/technology');

        $request = $this->buildRequest(200);
        $response = $request->technology('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/technology?date=2014-01-01');
    }

    public function testSearchTerms()
    {
        $request = $this->buildRequest(200);
        $response = $request->search_terms('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/terms');

        $request = $this->buildRequest(200);
        $response = $request->search_terms('asdf', '2014-01-01', 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/terms?date=2014-01-01&page=3');

        $request = $this->buildRequest(200);
        $response = $request->search_terms('asdf', null, 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/terms?page=3');
    }

    public function testSearchEngines()
    {
        $request = $this->buildRequest(200);
        $response = $request->search_engines('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/engines');

        $request = $this->buildRequest(200);
        $response = $request->search_engines('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/engines?date=2014-01-01');
    }

    public function testLocations()
    {
        $request = $this->buildRequest(200);
        $response = $request->locations('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/locations');

        $request = $this->buildRequest(200);
        $response = $request->locations('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/locations?date=2014-01-01');
    }

    public function testBrowserstats()
    {
        $request = $this->buildRequest(200);
        $response = $request->browser_stats('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/browserstats');

        $request = $this->buildRequest(200);
        $response = $request->browser_stats('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals($logMessage, 'GET-https://secure.gaug.es/gauges/asdf/browserstats?date=2014-01-01');
    }

    /**
     * @expectedException GuzzleHttp\Exception\ServerException
     */
    public function testServerDown()
    {
        $request = $this->buildRequest(500);
        $response = $request->me();
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

        $logger = new Logger('testing');
        $logger->pushHandler(self::$testHandler);

        $request = new Request('fake-token');
        $request->setLogger($logger);
        $request->setLogLevel(LogLevel::DEBUG);
        $request->setMessageFormatter(
            new MessageFormatter('{method}-{uri}')
        );

        $mockHandler = new MockHandler(array(
            new Response($statusCode, array(), $body)
        ));
        $handler = HandlerStack::create($mockHandler);
        $request->setHandlerStack($handler);

        return $request;
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