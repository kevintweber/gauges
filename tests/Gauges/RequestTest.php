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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInSetLogLevel()
    {
        $request = $this->buildRequest(200);
        $request->setLogLevel('asdf');
    }

    public function testMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->me();
        $this->assertInstanceOf('GuzzleHttp\Psr7\Response', $response);
        $this->assertEquals($response->getStatusCode(), 200);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/me', $logMessage);
    }

    public function testUpdateMe()
    {
        $request = $this->buildRequest(200);
        $request->updateMe('Kevin', 'Weber');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('PUT-https://secure.gaug.es/me?first_name=Kevin&last_name=Weber', $logMessage);

        $request = $this->buildRequest(200);
        $request->updateMe(null, 'Weber');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('PUT-https://secure.gaug.es/me?last_name=Weber', $logMessage);
    }

    public function testListClients()
    {
        $request = $this->buildRequest(200);
        $request->listClients();
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/clients', $logMessage);
    }

    public function testCreateClient()
    {
        $request = $this->buildRequest(200);
        $request->createClient();
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('POST-https://secure.gaug.es/clients', $logMessage);

        $request = $this->buildRequest(200);
        $request->createClient('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('POST-https://secure.gaug.es/clients?description=asdf', $logMessage);
    }

    public function testDeleteClients()
    {
        $request = $this->buildRequest(200);
        $request->deleteClient('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('DELETE-https://secure.gaug.es/clients/asdf', $logMessage);
    }

    public function testListGauges()
    {
        $request = $this->buildRequest(200);
        $request->listGauges();
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges', $logMessage);

        $request = $this->buildRequest(200);
        $request->listGauges(3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges?page=3', $logMessage);
    }

    public function testCreateGauge()
    {
        $request = $this->buildRequest(200);
        $request->createGauge('asdf', 'America/New_York');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('POST-https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York', $logMessage);

        $request = $this->buildRequest(200);
        $request->createGauge('asdf', 'America/New_York', 'all,none');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('POST-https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York&allowed_hosts=all%2Cnone',
            $logMessage
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionFromEmptyTitleInCreateGauge()
    {
        $request = $this->buildRequest(200);
        $request->createGauge('', 'America/New_York');
    }

    public function testGaugeDetail()
    {
        $request = $this->buildRequest(200);
        $request->gaugeDetail('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf', $logMessage);
    }

    public function testUpdateGauge()
    {
        $request = $this->buildRequest(200);
        $request->updateGauge('asdf1', 'asdf2', 'America/New_York');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('PUT-https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York', $logMessage);

        $request = $this->buildRequest(200);
        $request->updateGauge('asdf1', 'asdf2', new \DateTimeZone('America/New_York'), 'all,none');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals(
            'PUT-https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York&allowed_hosts=all%2Cnone',
            $logMessage
        );
    }

    public function testDeleteGauge()
    {
        $request = $this->buildRequest(200);
        $request->deleteGauge('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('DELETE-https://secure.gaug.es/gauges/asdf', $logMessage);
    }

    public function testListShares()
    {
        $request = $this->buildRequest(200);
        $request->listShares('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/shares', $logMessage);
    }

    public function testShareGauge()
    {
        $request = $this->buildRequest(200);
        $request->shareGauge('asdf', 'kevintweber@gmail.com');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('POST-https://secure.gaug.es/gauges/asdf/shares?email=kevintweber%40gmail.com', $logMessage);
    }

    public function testUnshareGauge()
    {
        $request = $this->buildRequest(200);
        $request->unshareGauge('asdf', 'kevintweber');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('DELETE-https://secure.gaug.es/gauges/asdf/shares/kevintweber', $logMessage);
    }

    public function testTopContent()
    {
        $request = $this->buildRequest(200);
        $request->topContent('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/content', $logMessage);

        $request = $this->buildRequest(200);
        $request->topContent('asdf', '2014-01-01', null, 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/content?date=2014-01-01&page=3', $logMessage);

        $request = $this->buildRequest(200);
        $request->topContent('asdf', null, null, 2);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/content?page=2', $logMessage);

        $request = $this->buildRequest(200);
        $request->topContent('asdf', null, 'month', 2);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/content?page=2&group=month', $logMessage);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInTopContent()
    {
        $request = $this->buildRequest(200);
        $request->topContent('asdf', '2014-01-01', 'asdf');
    }

    public function testTopReferrers()
    {
        $request = $this->buildRequest(200);
        $request->topReferrers('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/referrers', $logMessage);

        $request = $this->buildRequest(200);
        $request->topReferrers('asdf', '2014-01-01', 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/referrers?date=2014-01-01&page=3', $logMessage);

        $request = $this->buildRequest(200);
        $request->topReferrers('asdf', new \DateTime('2014-01-01'), 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/referrers?date=2014-01-01&page=3', $logMessage);

        $request = $this->buildRequest(200);
        $request->topReferrers('asdf', null, 2);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/referrers?page=2', $logMessage);
    }

    public function testTraffic()
    {
        $request = $this->buildRequest(200);
        $request->traffic('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/traffic', $logMessage);

        $request = $this->buildRequest(200);
        $request->traffic('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/traffic?date=2014-01-01', $logMessage);
    }

    public function testBrowserResolutions()
    {
        $request = $this->buildRequest(200);
        $request->browserResolutions('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/resolutions', $logMessage);

        $request = $this->buildRequest(200);
        $request->browserResolutions('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/resolutions?date=2014-01-01', $logMessage);
    }

    public function testTechnology()
    {
        $request = $this->buildRequest(200);
        $request->technology('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/technology', $logMessage);

        $request = $this->buildRequest(200);
        $request->technology('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/technology?date=2014-01-01', $logMessage);
    }

    public function testSearchTerms()
    {
        $request = $this->buildRequest(200);
        $request->searchTerms('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/terms', $logMessage);

        $request = $this->buildRequest(200);
        $request->searchTerms('asdf', '2014-01-01', 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/terms?date=2014-01-01&page=3', $logMessage);

        $request = $this->buildRequest(200);
        $request->searchTerms('asdf', null, 3);
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/terms?page=3', $logMessage);
    }

    public function testSearchEngines()
    {
        $request = $this->buildRequest(200);
        $request->searchEngines('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/engines', $logMessage);

        $request = $this->buildRequest(200);
        $request->searchEngines('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/engines?date=2014-01-01', $logMessage);
    }

    public function testLocations()
    {
        $request = $this->buildRequest(200);
        $request->locations('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/locations', $logMessage);

        $request = $this->buildRequest(200);
        $request->locations('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/locations?date=2014-01-01', $logMessage);
    }

    public function testBrowserstats()
    {
        $request = $this->buildRequest(200);
        $request->browserStats('asdf');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/browserstats', $logMessage);

        $request = $this->buildRequest(200);
        $request->browserStats('asdf', '2014-01-01');
        $logMessage = $this->getLastLoggingMessage();
        $this->assertEquals('GET-https://secure.gaug.es/gauges/asdf/browserstats?date=2014-01-01', $logMessage);
    }

    /**
     * @expectedException GuzzleHttp\Exception\ServerException
     */
    public function testServerDown()
    {
        $request = $this->buildRequest(500);
        $request->me();
    }

    /**
     * Helper function for testing requests.
     *
     * @param int   $statusCode
     * @param mixed $body
     *
     * @return Request
     */
    protected function buildRequest(int $statusCode, $body = null) : Request
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
    protected function getLastLoggingMessage() : string
    {
        $records = self::$testHandler->getRecords();
        $record = end($records);

        return $record['message'];
    }
}
